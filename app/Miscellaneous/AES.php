<?php

namespace App\Miscellaneous;

use Exception;
use Throwable;

class AES
{
    private static $cipher = "AES-256-CBC";
    private $key;

    // Singleton instance
    private static $instance;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
        $this->key = file_get_contents(__DIR__ . "/../.data/globkey.pem");
        if ($this->key === false) {
            throw new Exception('Failed to read the encryption key');
        }
    }

    // Get the singleton instance
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Encrypt a string
    public function encrypt($string)
    {
        $ivlen = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($string, self::$cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            return false;
        }
        $hmac = hash_hmac('sha256', $ciphertext, $this->key, true);
        return base64_encode($iv . $hmac . $ciphertext);
    }

    // Decrypt a string
    public function decrypt($string)
    {
        try {
            $c = base64_decode($string);
            $ivlen = openssl_cipher_iv_length(self::$cipher);
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext = substr($c, $ivlen + $sha2len);
            if ($ciphertext) {
                $original_plaintext = openssl_decrypt($ciphertext, self::$cipher, $this->key, OPENSSL_RAW_DATA, $iv);
                if ($original_plaintext === false) {
                    return false;
                }
                $calcmac = hash_hmac('sha256', $ciphertext, $this->key, true);
                if (hash_equals($hmac, $calcmac)) {
                    return $original_plaintext;
                }
            }
            throw new Exception('Hash verification failed');
        } catch (Throwable $t) {
            return false;
        }
    }
}
