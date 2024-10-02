<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Service\EncryptionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    #[Route('/message', name: 'message', methods: ['GET'])]
    public function push(): Response
    {
        $key = ' ';
        $encrypted = $this->encryptionService->encrypt('Hello Bas!', $key);
        $decrypted = $this->encryptionService->decrypt($encrypted, $key);

        $message = new Message();
        return new Response(
            '<html><body> ' . $encrypted . ' <br>  ' . $decrypted . '</body></html>'
        );
    }
}