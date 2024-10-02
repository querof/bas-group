<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipient;
use App\Repository\RecipientRepository;
use App\Service\EncryptionService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipientController
{
    private EntityManagerInterface $entityManager;
    private RecipientRepository $recipientRepository;
    private EncryptionService $encryptionService;

    public function __construct(EntityManagerInterface $entityManager, RecipientRepository $recipientRepository,  EncryptionService $encryptionService)
    {
        $this->entityManager = $entityManager;
        $this->recipientRepository = $recipientRepository;
        $this->encryptionService = $encryptionService;
    }

    #[Route('/recipient', name: 'recipient', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $identifier = $requestBody['identifier'];
            if (!isset($identifier)) {
                return new JsonResponse([
                    'error' => 'Identifier is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $encryptionKey = $this->encryptionService->generateKey();
            $recipient = new Recipient($identifier, $encryptionKey);
            $this->entityManager->persist($recipient);

            $this->entityManager->flush();

            return new  JsonResponse([
                'id' => $recipient->getId(),
                'identifier' => $recipient->getIdentifier(),
            ], Response::HTTP_CREATED);
        } catch (ORMException | UniqueConstraintViolationException $exception) {
            if($exception instanceof UniqueConstraintViolationException) {
                return new JsonResponse([
                    'error' => 'Identifier already exists'
                ], Response::HTTP_CONFLICT);
            }

            return new JsonResponse([
                'error' => 'Unable to add recipient:  ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/recipient/{recipient}', name: 'get_recipient', methods: ['GET'])]
    public function get(?Recipient $recipient): JsonResponse
    {
        if (!$recipient ) {
            return new JsonResponse([
                'error' => 'Recipient not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $recipient->getId(),
            'identifier' => $recipient->getIdentifier()
        ]);
    }

    #[Route('/recipients', name: 'get_all_recipients', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $recipients = $this->recipientRepository->findAll();

        $data = [];
        foreach ($recipients as $recipient) {
            $data[] = [
                'id' => $recipient->getId(),
                'identifier' => $recipient->getIdentifier()
            ];
        }

        return new JsonResponse($data);
    }
}