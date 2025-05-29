<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\RSA;

class CryptoHelper
{
    /**
     * Encrypt data using RSA Public Key
     */
    public static function encryptWithRSA(string $data): ?string
    {
        try {
            $publicKeyPath = config('crypto.rsa_public_key');
            $publicKey = file_get_contents($publicKeyPath);
            $rsa = RSA::loadPublicKey($publicKey);
            
            return base64_encode($rsa->encrypt($data));
        } catch (Exception $e) {
            Log::error('RSA Encryption Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypt data using RSA Private Key
     */
    public static function decryptWithRSA(string $encryptedData): ?string
    {
        try {
            $privateKeyPath = config('crypto.rsa_private_key');
            $privateKey = file_get_contents($privateKeyPath);
            $rsa = RSA::loadPrivateKey($privateKey);
            
            return $rsa->decrypt(base64_decode($encryptedData));
        } catch (Exception $e) {
            Log::error('RSA Decryption Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Encrypt data using AES-256
     */
    public static function encryptWithAES(string $data, string $key): ?string
    {
        try {
            $aes = new AES('cbc');
            $aes->setKey(hash('sha256', $key, true)); // Ensure a 256-bit key
            $iv = random_bytes(16); // Generate a random IV
            $aes->setIV($iv);
            
            return base64_encode($iv . $aes->encrypt($data)); // Store IV with encrypted data
        } catch (Exception $e) {
            Log::error('AES Encryption Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypt data using AES-256
     */
    public static function decryptWithAES(string $encryptedData, string $key): ?string
    {
        try {
            $aes = new AES('cbc');
            $aes->setKey(hash('sha256', $key, true));

            $decodedData = base64_decode($encryptedData);
            $iv = substr($decodedData, 0, 16); // Extract IV
            $encryptedText = substr($decodedData, 16);

            $aes->setIV($iv);
            return $aes->decrypt($encryptedText);
        } catch (Exception $e) {
            Log::error('AES Decryption Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate HMAC for integrity check
     */
    public static function generateHMAC(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }
}
