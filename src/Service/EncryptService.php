<?php

namespace App\Service;


class EncryptService
{
    private $env;

    public function __construct()
    {
        $this->env = $private_secret_key = $_ENV['JWT_PASSPHRASE'];
    }


    public function encrypt($message)
    {

        $key = hex2bin($this->env);
        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = openssl_random_pseudo_bytes($nonceSize);
        $ciphertext = openssl_encrypt(
            $message,
            'aes-256-ctr',
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return base64_encode($nonce . $ciphertext);
    }

    public function decrypt($message)
    {
        $key = hex2bin($this->env);
        $message = base64_decode($message);
        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');
        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-ctr',
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return $plaintext;
    }

}
