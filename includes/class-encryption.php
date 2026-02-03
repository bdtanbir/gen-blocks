<?php
/**
 * Encryption Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Handles encryption and decryption of sensitive data
 */
class Encryption {

    /**
     * Encryption method
     *
     * @var string
     */
    private $method = 'aes-256-cbc';

    /**
     * Get encryption key from WordPress salts
     *
     * @return string
     */
    private function get_key() {
        $key = '';

        if (defined('SECURE_AUTH_KEY') && defined('SECURE_AUTH_SALT')) {
            $key = SECURE_AUTH_KEY . SECURE_AUTH_SALT;
        } elseif (defined('AUTH_KEY') && defined('AUTH_SALT')) {
            $key = AUTH_KEY . AUTH_SALT;
        } else {
            // Fallback - not ideal but better than nothing
            $key = 'genblocks_default_key_' . get_site_url();
        }

        return hash('sha256', $key, true);
    }

    /**
     * Encrypt data
     *
     * @param string $data Data to encrypt.
     * @return string Encrypted data (base64 encoded).
     */
    public function encrypt($data) {
        if (empty($data)) {
            return '';
        }

        // Check if data is already encrypted
        if ($this->is_encrypted($data)) {
            return $data;
        }

        $key = $this->get_key();
        $iv_length = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        $encrypted = openssl_encrypt(
            $data,
            $this->method,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if (false === $encrypted) {
            return $data; // Return original if encryption fails
        }

        // Combine IV and encrypted data
        $combined = $iv . $encrypted;

        // Add a prefix to identify encrypted data
        return 'enc:' . base64_encode($combined);
    }

    /**
     * Decrypt data
     *
     * @param string $data Encrypted data (base64 encoded).
     * @return string Decrypted data.
     */
    public function decrypt($data) {
        if (empty($data)) {
            return '';
        }

        // Check if data is encrypted
        if (!$this->is_encrypted($data)) {
            return $data; // Return as-is if not encrypted
        }

        // Remove prefix
        $data = substr($data, 4);

        $key = $this->get_key();
        $combined = base64_decode($data);

        if (false === $combined) {
            return ''; // Invalid base64
        }

        $iv_length = openssl_cipher_iv_length($this->method);

        // Check if combined data is long enough
        if (strlen($combined) < $iv_length) {
            return ''; // Invalid data
        }

        $iv = substr($combined, 0, $iv_length);
        $encrypted = substr($combined, $iv_length);

        $decrypted = openssl_decrypt(
            $encrypted,
            $this->method,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if (false === $decrypted) {
            return ''; // Decryption failed
        }

        return $decrypted;
    }

    /**
     * Check if data is encrypted
     *
     * @param string $data Data to check.
     * @return bool
     */
    public function is_encrypted($data) {
        return is_string($data) && strpos($data, 'enc:') === 0;
    }
}
