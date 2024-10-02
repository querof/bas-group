<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipientController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     */
    #[Route('/recipient', name: 'recipient', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $identifier = $requestBody['identifier'];
        if (!isset($identifier)) {
            return new JsonResponse([
                'error' => 'Identifier is required'
            ], 400);
        }

        $recipient = new Recipient($identifier);
        $this->entityManager->persist($recipient);

        try {
            $this->entityManager->flush();
        } catch (ORMException $e) {
            return new JsonResponse([
                'error' => 'Could not save recipient'
            ], 500);
        }

        return new  JsonResponse([
            'id' => $recipient->getId(),
            'identifier' => $recipient->getIdentifier()
        ]);
    }
}