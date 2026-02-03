<?php
/**
 * Settings Management Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Handles plugin settings storage and retrieval
 */
class Settings {

    /**
     * Option name in database
     *
     * @var string
     */
    private $option_name = 'genblocks_settings';

    /**
     * Default settings
     *
     * @var array
     */
    private $defaults = [
        'api_provider'      => 'openai',
        'api_key'           => '',
        'model'             => 'gpt-4',
        'rate_limit'        => 100,
        'cache_enabled'     => true,
        'cache_duration'    => 3600,
        'primary_color'     => '#0073aa',
        'default_alignment' => 'center',
        'max_tokens'        => 8192, // Increased for complex block generation
        'temperature'       => 0.7,
    ];

    /**
     * Encryption instance
     *
     * @var Encryption
     */
    private $encryption;

    /**
     * Constructor
     */
    public function __construct() {
        $this->encryption = new Encryption();
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function get_all() {
        $settings = get_option($this->option_name, $this->defaults);
        $settings = wp_parse_args($settings, $this->defaults);

        // Decrypt API key for use
        if (!empty($settings['api_key'])) {
            $settings['api_key'] = $this->encryption->decrypt($settings['api_key']);
        }

        return $settings;
    }

    /**
     * Get a single setting
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value.
     * @return mixed
     */
    public function get($key, $default = null) {
        $settings = $this->get_all();

        if (isset($settings[$key])) {
            return $settings[$key];
        }

        return $default ?? ($this->defaults[$key] ?? null);
    }

    /**
     * Update settings
     *
     * @param array $new_settings Settings to update.
     * @return bool
     */
    public function update($new_settings) {
        $current = get_option($this->option_name, $this->defaults);

        // Sanitize settings
        $sanitized = $this->sanitize($new_settings);

        // Encrypt API key before storing
        if (isset($sanitized['api_key']) && !empty($sanitized['api_key'])) {
            $sanitized['api_key'] = $this->encryption->encrypt($sanitized['api_key']);
        }

        $updated = array_merge($current, $sanitized);

        return update_option($this->option_name, $updated);
    }

    /**
     * Sanitize settings
     *
     * @param array $settings Raw settings.
     * @return array Sanitized settings.
     */
    private function sanitize($settings) {
        $sanitized = [];

        if (isset($settings['api_provider'])) {
            $allowed_providers = ['openai', 'claude', 'gemini'];
            $sanitized['api_provider'] = in_array($settings['api_provider'], $allowed_providers, true)
                ? $settings['api_provider']
                : 'openai';
        }

        if (isset($settings['api_key'])) {
            $sanitized['api_key'] = sanitize_text_field($settings['api_key']);
        }

        if (isset($settings['model'])) {
            $sanitized['model'] = sanitize_text_field($settings['model']);
        }

        if (isset($settings['rate_limit'])) {
            $sanitized['rate_limit'] = absint($settings['rate_limit']);
            $sanitized['rate_limit'] = max(1, min(1000, $sanitized['rate_limit']));
        }

        if (isset($settings['cache_enabled'])) {
            $sanitized['cache_enabled'] = (bool) $settings['cache_enabled'];
        }

        if (isset($settings['cache_duration'])) {
            $sanitized['cache_duration'] = absint($settings['cache_duration']);
            $sanitized['cache_duration'] = max(60, min(86400, $sanitized['cache_duration']));
        }

        if (isset($settings['primary_color'])) {
            $sanitized['primary_color'] = sanitize_hex_color($settings['primary_color']) ?: '#0073aa';
        }

        if (isset($settings['default_alignment'])) {
            $allowed_alignments = ['left', 'center', 'right', 'wide', 'full'];
            $sanitized['default_alignment'] = in_array($settings['default_alignment'], $allowed_alignments, true)
                ? $settings['default_alignment']
                : 'center';
        }

        if (isset($settings['max_tokens'])) {
            $sanitized['max_tokens'] = absint($settings['max_tokens']);
            $sanitized['max_tokens'] = max(100, min(4000, $sanitized['max_tokens']));
        }

        if (isset($settings['temperature'])) {
            $sanitized['temperature'] = floatval($settings['temperature']);
            $sanitized['temperature'] = max(0, min(2, $sanitized['temperature']));
        }

        return $sanitized;
    }

    /**
     * Get settings for REST API response (without sensitive data)
     *
     * @return array
     */
    public function get_public() {
        $settings = $this->get_all();

        // Mask API key
        if (!empty($settings['api_key'])) {
            $key = $settings['api_key'];
            $settings['api_key'] = substr($key, 0, 8) . '...' . substr($key, -4);
            $settings['api_key_set'] = true;
        } else {
            $settings['api_key'] = '';
            $settings['api_key_set'] = false;
        }

        return $settings;
    }

    /**
     * Check if API key is configured
     *
     * @return bool
     */
    public function has_api_key() {
        $api_key = $this->get('api_key');
        return !empty($api_key);
    }

    /**
     * Reset settings to defaults
     *
     * @return bool
     */
    public function reset() {
        return update_option($this->option_name, $this->defaults);
    }

    /**
     * Delete all settings
     *
     * @return bool
     */
    public function delete() {
        return delete_option($this->option_name);
    }
}
