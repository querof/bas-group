<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Recipient;
use App\Repository\RecipientRepository;
use App\Service\EncryptionService;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController
{

    private EntityManager $entityManager;
    private EncryptionService $encryptionService;
    private RecipientRepository $recipientRepository;
    private int $expirationDays;

    public function __construct(
        EntityManagerInterface $entityManager,
        EncryptionService $encryptionService,
        RecipientRepository $recipientRepository,
        int $expirationDays
    )
    {
        $this->entityManager = $entityManager;
        $this->encryptionService = $encryptionService;
        $this->recipientRepository = $recipientRepository;
        $this->expirationDays = $expirationDays;
    }

    #[Route('/message', name: 'message', methods: ['POST'])]
    public function push( Request $request): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $identifier = $requestBody['identifier'];
            $recipients = $this->recipientRepository->findBy(['identifier' => $identifier]);
            if ([] === $recipients) {
                return new JsonResponse('Recipient not found', Response::HTTP_NOT_FOUND);
            }

            if (empty($requestBody['message'])) {
                return new JsonResponse('Message is required', Response::HTTP_BAD_REQUEST);
            }

            $recipient = reset($recipients);
            $messageText = $requestBody['message'];
            $encryptionKey = $recipient->getEncryptionKey();

            $encryptedMessage = $this->encryptionService->encrypt($messageText, $encryptionKey);
            $message = new Message($encryptedMessage, $recipient, $this->expirationDays);
            $this->entityManager->persist($message);

            $this->entityManager->flush();

            return new JsonResponse('Message sent to ' . $identifier, Response::HTTP_CREATED);
        } catch (ORMException | OptimisticLockException $exception) {
            return new JsonResponse(
                'Failed to send message: ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('recipient/{recipient}/message/{message}', name: 'message_get', methods: ['GET'])]
    public function get(?Recipient $recipient, ?Message $message): JsonResponse
    {
        if (!$recipient) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Recipient not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$message) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Message not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $encryptionKey = $recipient->getEncryptionKey();
        $encryptedMessage = $message->getText();
        $decryptedMessage = $this->encryptionService->decrypt($encryptedMessage, $encryptionKey);

        return new JsonResponse($decryptedMessage, Response::HTTP_OK);
    }

    #[Route('recipient/{recipient}/messages', name: 'messages_get', methods: ['GET'])]
    public function  getAllMessages(?Recipient $recipient): JsonResponse
    {
        if (!$recipient) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Recipient not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $messages = $recipient->getMessages();
        $decryptedMessages = [];
        foreach ($messages as $message) {
            $encryptionKey = $recipient->getEncryptionKey();
            $encryptedMessage = $message->getText();
            $decryptedMessage = $this->encryptionService->decrypt($encryptedMessage, $encryptionKey);
            $decryptedMessages[$message->getId()] = $decryptedMessage;
        }

        return new JsonResponse($decryptedMessages, Response::HTTP_OK);

    }
}