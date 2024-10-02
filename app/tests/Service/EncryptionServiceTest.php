<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\EncryptionService;
use PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function testEncrypt(): void
    {
        $encryptionService = new EncryptionService();
        $key = $encryptionService->generateKey();
        $data = 'This is a test string';
        $encrypted = $encryptionService->encrypt($data, $key);
        static::assertNotEquals($data, $encrypted);
    }

    public function testDecrypt(): void
    {
        $encryptionService = new EncryptionService();
        $key = $encryptionService->generateKey();
        $data = 'This is a test string';
        $encrypted = $encryptionService->encrypt($data, $key);
        $decrypted = $encryptionService->decrypt($encrypted, $key);
        static::assertEquals($data, $decrypted);
    }
}