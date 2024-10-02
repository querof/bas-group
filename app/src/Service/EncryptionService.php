<?php

declare(strict_types=1);

namespace App\Service;

class EncryptionService
{
    private string $method = 'AES-256-CBC';

    public function encrypt(string $data, string $key): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
        $encrypted = openssl_encrypt($data, $this->method, $key, 0, $iv);

        return base64_encode($iv . '::' . $encrypted);
    }

    public function decrypt(string $data, string $key): string
    {
        list($iv, $encrypted) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted, $this->method, $key, 0, $iv);
    }

    public function getKey(): string
    {
        return openssl_random_pseudo_bytes(32);
    }
}
