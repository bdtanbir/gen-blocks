<?php
/**
 * Response Parser Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Parses and validates AI responses for block generation
 */
class Response_Parser {

    /**
     * Maximum allowed nesting depth for blocks
     *
     * @var int
     */
    private const MAX_DEPTH = 10;

    /**
     * Valid core block names
     *
     * @var array
     */
    private $valid_core_blocks = [
        'core/group',
        'core/columns',
        'core/column',
        'core/heading',
        'core/paragraph',
        'core/list',
        'core/list-item',
        'core/quote',
        'core/pullquote',
        'core/buttons',
        'core/button',
        'core/image',
        'core/gallery',
        'core/cover',
        'core/media-text',
        'core/video',
        'core/audio',
        'core/file',
        'core/spacer',
        'core/separator',
        'core/table',
        'core/code',
        'core/preformatted',
        'core/verse',
        'core/html',
        'core/shortcode',
        'core/embed',
        'core/social-links',
        'core/social-link',
    ];

    /**
     * Custom GenBlocks block names
     *
     * @var array
     */
    private $valid_custom_blocks = [
        'genblocks/cta',
        'genblocks/hero',
        'genblocks/features',
        'genblocks/testimonial',
        'genblocks/pricing',
    ];

    /**
     * Parse AI response content
     *
     * @param string $content Raw AI response content.
     * @return array|\WP_Error Parsed block data or error.
     */
    public function parse($content) {
        // Extend execution time for complex block processing
        $this->extend_execution_time();

        // Clean the response
        $cleaned = $this->clean_response($content);

        if (empty($cleaned)) {
            return new \WP_Error(
                'empty_response',
                __('AI returned an empty response', 'gen-blocks')
            );
        }

        // Extract JSON
        $json_string = $this->extract_json($cleaned);

        if (is_wp_error($json_string)) {
            return $json_string;
        }

        // Parse JSON
        $data = json_decode($json_string, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to identify the issue
            $error_details = [
                'json_error'   => json_last_error_msg(),
                'json_string'  => substr($json_string, 0, 500),
                'raw_response' => substr($content, 0, 500),
            ];

            // Check for common issues
            $debug_info = '';
            if (strpos($json_string, '```') !== false) {
                $debug_info = ' The response still contains markdown code fences.';
            }
            if (strpos($content, '```') !== false && strpos($json_string, '```') === false) {
                $debug_info = ' Original response had markdown fences that were partially cleaned.';
            }

            return new \WP_Error(
                'json_parse_error',
                sprintf(
                    __('Failed to parse response as JSON: %s%s', 'gen-blocks'),
                    json_last_error_msg(),
                    $debug_info
                ),
                $error_details
            );
        }

        // Process block in a single optimized pass (validate + sanitize + fix)
        $processed = $this->process_block($data, 0);

        if (is_wp_error($processed)) {
            return $processed;
        }

        return $processed;
    }

    /**
     * Extend PHP execution time for complex operations
     *
     * @return void
     */
    private function extend_execution_time() {
        // Only extend if we can and if current limit is less than 120 seconds
        $current_limit = (int) ini_get('max_execution_time');
        if ($current_limit > 0 && $current_limit < 120) {
            // Use set_time_limit to extend (resets the counter)
            if (function_exists('set_time_limit') && !ini_get('safe_mode')) {
                @set_time_limit(120);
            }
        }
    }

    /**
     * Process block in a single pass: validate, sanitize, and fix issues
     * This combines three separate recursive operations into one for efficiency
     *
     * @param array $block Block data.
     * @param int   $depth Current nesting depth.
     * @return array|\WP_Error Processed block or error.
     */
    private function process_block($block, $depth = 0) {
        // Check depth limit to prevent infinite recursion
        if ($depth > self::MAX_DEPTH) {
            return new \WP_Error(
                'max_depth_exceeded',
                sprintf(
                    __('Block nesting exceeds maximum depth of %d levels', 'gen-blocks'),
                    self::MAX_DEPTH
                )
            );
        }

        // Validate: Check if it's an array
        if (!is_array($block)) {
            return new \WP_Error(
                'invalid_structure',
                __('Block data must be an object', 'gen-blocks')
            );
        }

        // Validate: Check for required blockName
        if (!isset($block['blockName'])) {
            return new \WP_Error(
                'missing_block_name',
                __('Block is missing required blockName field', 'gen-blocks')
            );
        }

        // Validate: Check blockName format
        if (!$this->is_valid_block_name($block['blockName'])) {
            return new \WP_Error(
                'invalid_block_name',
                sprintf(
                    __('Invalid block name: %s', 'gen-blocks'),
                    $block['blockName']
                )
            );
        }

        // Sanitize: Block name
        $block['blockName'] = sanitize_text_field($block['blockName']);

        // Sanitize: Ensure attrs exists and is array
        if (!isset($block['attrs']) || !is_array($block['attrs'])) {
            $block['attrs'] = [];
        }

        // Sanitize: Attributes
        $block['attrs'] = $this->sanitize_attributes($block['attrs'], $block['blockName']);

        // Fix: Heading level validation
        if ($block['blockName'] === 'core/heading' && isset($block['attrs']['level'])) {
            $level = intval($block['attrs']['level']);
            $block['attrs']['level'] = max(1, min(6, $level));
        }

        // Sanitize: Ensure innerBlocks exists and is array
        if (!isset($block['innerBlocks']) || !is_array($block['innerBlocks'])) {
            $block['innerBlocks'] = [];
        }

        // Fix: Ensure innerBlocks for container blocks
        $container_blocks = ['core/group', 'core/columns', 'core/buttons', 'core/cover'];
        if (in_array($block['blockName'], $container_blocks, true) && !isset($block['innerBlocks'])) {
            $block['innerBlocks'] = [];
        }

        // Process inner blocks recursively (single pass)
        $processed_inner_blocks = [];
        foreach ($block['innerBlocks'] as $index => $inner_block) {
            $processed_inner = $this->process_block($inner_block, $depth + 1);
            if (is_wp_error($processed_inner)) {
                return new \WP_Error(
                    'invalid_inner_block',
                    sprintf(
                        __('Invalid inner block at index %d: %s', 'gen-blocks'),
                        $index,
                        $processed_inner->get_error_message()
                    )
                );
            }
            $processed_inner_blocks[] = $processed_inner;
        }
        $block['innerBlocks'] = $processed_inner_blocks;

        // Fix: Button without buttons wrapper (only at root level to avoid re-wrapping)
        if ($depth === 0 && $block['blockName'] === 'core/button') {
            $block = [
                'blockName'   => 'core/buttons',
                'attrs'       => [
                    'layout' => [
                        'type'           => 'flex',
                        'justifyContent' => 'center',
                    ],
                ],
                'innerBlocks' => [$block],
            ];
        }

        // Fix: Column without columns wrapper (only at root level to avoid re-wrapping)
        if ($depth === 0 && $block['blockName'] === 'core/column') {
            $block = [
                'blockName'   => 'core/columns',
                'attrs'       => [],
                'innerBlocks' => [$block],
            ];
        }

        return $block;
    }

    /**
     * Clean raw AI response
     *
     * @param string $content Raw content.
     * @return string Cleaned content.
     */
    private function clean_response($content) {
        // Trim whitespace
        $content = trim($content);

        // Remove any BOM or special characters at start
        $content = preg_replace('/^\x{FEFF}/u', '', $content);

        // Remove markdown code block markers (various formats)
        // Handles: ```json, ``` json, ```JSON, ```\n, etc.
        $content = preg_replace('/^[\s]*```[\s]*(json|JSON)?[\s]*/s', '', $content);
        $content = preg_replace('/[\s]*```[\s]*$/s', '', $content);

        // Also handle if there are multiple code blocks - take content between first ``` and last ```
        if (preg_match('/```(?:json|JSON)?[\s\n]*([\s\S]*?)[\s\n]*```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        // Remove leading/trailing backticks that might remain
        $content = trim($content, '`');
        $content = trim($content);

        // Remove common AI prefixes
        $prefixes = [
            'Here is the JSON:',
            'Here\'s the JSON:',
            'Here is the block:',
            'Here\'s the block:',
            'Here is the Gutenberg block JSON:',
            'Here\'s the Gutenberg block:',
            'JSON output:',
            'Output:',
            'Result:',
        ];

        foreach ($prefixes as $prefix) {
            if (stripos($content, $prefix) === 0) {
                $content = trim(substr($content, strlen($prefix)));
            }
        }

        // Remove any text before the first { (common with chatty AI responses)
        $first_brace = strpos($content, '{');
        if ($first_brace !== false && $first_brace > 0) {
            // Check if there's meaningful JSON starting with {
            $potential_json = substr($content, $first_brace);
            // Verify it looks like JSON (starts with { and ends with })
            if (preg_match('/^\{.*\}$/s', trim($potential_json))) {
                $content = $potential_json;
            }
        }

        return trim($content);
    }

    /**
     * Extract JSON from content
     *
     * @param string $content Cleaned content.
     * @return string|\WP_Error JSON string or error.
     */
    private function extract_json($content) {
        // If content starts with {, assume it's JSON
        if (strpos(trim($content), '{') === 0) {
            $content = trim($content);
            // Make sure it ends with }
            if (substr($content, -1) === '}') {
                return $content;
            }
        }

        // Find first { and last }
        $first_brace = strpos($content, '{');
        $last_brace = strrpos($content, '}');

        if ($first_brace === false || $last_brace === false) {
            return new \WP_Error(
                'no_json_found',
                __('Could not find valid JSON in AI response. The response may have been truncated or the AI did not return JSON.', 'gen-blocks'),
                ['raw_response' => substr($content, 0, 1000)]
            );
        }

        // Extract the JSON portion
        $json_string = substr($content, $first_brace, $last_brace - $first_brace + 1);

        // Basic validation - count braces to check for balance
        $open_braces = substr_count($json_string, '{');
        $close_braces = substr_count($json_string, '}');

        if ($open_braces !== $close_braces) {
            return new \WP_Error(
                'json_truncated',
                sprintf(
                    __('JSON appears to be truncated or malformed. Open braces: %d, Close braces: %d', 'gen-blocks'),
                    $open_braces,
                    $close_braces
                ),
                ['raw_response' => substr($content, 0, 1000)]
            );
        }

        return $json_string;
    }

    /**
     * Check if block name is valid
     *
     * @param string $name Block name.
     * @return bool
     */
    private function is_valid_block_name($name) {
        // Check format (namespace/name)
        if (!preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $name)) {
            return false;
        }

        // Check if it's a known valid block
        $all_valid = array_merge($this->valid_core_blocks, $this->valid_custom_blocks);

        // Allow any core/* block
        if (strpos($name, 'core/') === 0) {
            return true;
        }

        // Allow genblocks/* blocks
        if (strpos($name, 'genblocks/') === 0) {
            return true;
        }

        return in_array($name, $all_valid, true);
    }

    /**
     * Sanitize block attributes
     *
     * @param array  $attrs      Attributes.
     * @param string $block_name Block name for context.
     * @return array Sanitized attributes.
     */
    private function sanitize_attributes($attrs, $block_name) {
        $sanitized = [];

        foreach ($attrs as $key => $value) {
            $key = sanitize_key($key);

            if (is_string($value)) {
                $sanitized[$key] = $this->sanitize_string_attribute($key, $value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize_attributes($value, $block_name);
            } elseif (is_bool($value)) {
                $sanitized[$key] = $value;
            } elseif (is_numeric($value)) {
                $sanitized[$key] = is_float($value) ? floatval($value) : intval($value);
            } elseif (is_null($value)) {
                $sanitized[$key] = null;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize string attribute based on key
     *
     * @param string $key   Attribute key.
     * @param string $value Attribute value.
     * @return string Sanitized value.
     */
    private function sanitize_string_attribute($key, $value) {
        // HTML content fields
        $html_fields = ['content', 'citation', 'value', 'text', 'values', 'caption'];
        if (in_array($key, $html_fields, true)) {
            return wp_kses_post($value);
        }

        // URL fields
        $url_fields = ['url', 'href', 'src', 'mediaLink'];
        if (in_array($key, $url_fields, true)) {
            return esc_url_raw($value);
        }

        // Class name
        if ($key === 'className') {
            // Handle multiple classes
            $classes = explode(' ', $value);
            $sanitized_classes = array_map('sanitize_html_class', $classes);
            return implode(' ', array_filter($sanitized_classes));
        }

        // Color fields (hex colors)
        $color_fields = ['backgroundColor', 'textColor', 'overlayColor', 'color', 'background', 'text'];
        if (in_array($key, $color_fields, true)) {
            // Allow both hex and WordPress color slug names
            if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $value)) {
                return $value;
            }
            if (preg_match('/^[a-z0-9-]+$/', $value)) {
                return sanitize_text_field($value);
            }
        }

        // Alignment fields
        $align_fields = ['align', 'textAlign', 'verticalAlignment', 'justifyContent'];
        if (in_array($key, $align_fields, true)) {
            $allowed = ['left', 'center', 'right', 'wide', 'full', 'top', 'bottom', 'space-between'];
            return in_array($value, $allowed, true) ? $value : '';
        }

        // Default: sanitize as text field
        return sanitize_text_field($value);
    }

    /**
     * Convert parsed block to serialized Gutenberg format
     *
     * @param array $block Block data.
     * @return string Serialized block.
     */
    public function to_serialized($block) {
        return serialize_block($this->prepare_for_serialize($block));
    }

    /**
     * Prepare block for serialization
     *
     * @param array $block Block data.
     * @return array Prepared block.
     */
    private function prepare_for_serialize($block) {
        $prepared = [
            'blockName'    => $block['blockName'],
            'attrs'        => $block['attrs'] ?? [],
            'innerBlocks'  => [],
            'innerHTML'    => '',
            'innerContent' => [],
        ];

        // Generate innerHTML based on block type
        $prepared['innerHTML'] = $this->generate_inner_html($block);

        // Process inner blocks
        if (!empty($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $inner_block) {
                $prepared['innerBlocks'][] = $this->prepare_for_serialize($inner_block);
                $prepared['innerContent'][] = null;
            }
        }

        // Add innerHTML to innerContent if no inner blocks
        if (empty($prepared['innerBlocks']) && !empty($prepared['innerHTML'])) {
            $prepared['innerContent'][] = $prepared['innerHTML'];
        }

        return $prepared;
    }

    /**
     * Generate innerHTML for block
     *
     * @param array $block Block data.
     * @return string
     */
    private function generate_inner_html($block) {
        $name = $block['blockName'];
        $attrs = $block['attrs'] ?? [];

        switch ($name) {
            case 'core/paragraph':
                $content = $attrs['content'] ?? '';
                $align = isset($attrs['align']) ? ' style="text-align:' . esc_attr($attrs['align']) . '"' : '';
                return '<p' . $align . '>' . $content . '</p>';

            case 'core/heading':
                $content = $attrs['content'] ?? '';
                $level = isset($attrs['level']) ? intval($attrs['level']) : 2;
                $level = max(1, min(6, $level));
                $align = isset($attrs['textAlign']) ? ' style="text-align:' . esc_attr($attrs['textAlign']) . '"' : '';
                return '<h' . $level . $align . '>' . $content . '</h' . $level . '>';

            case 'core/button':
                $text = $attrs['text'] ?? '';
                $url = $attrs['url'] ?? '#';
                return '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url($url) . '">' . esc_html($text) . '</a></div>';

            case 'core/image':
                $url = $attrs['url'] ?? '';
                $alt = $attrs['alt'] ?? '';
                $caption = isset($attrs['caption']) ? '<figcaption>' . wp_kses_post($attrs['caption']) . '</figcaption>' : '';
                return '<figure class="wp-block-image"><img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"/>' . $caption . '</figure>';

            case 'core/spacer':
                $height = $attrs['height'] ?? '40px';
                return '<div style="height:' . esc_attr($height) . '" aria-hidden="true" class="wp-block-spacer"></div>';

            case 'core/separator':
                return '<hr class="wp-block-separator has-alpha-channel-opacity"/>';

            case 'core/quote':
                $value = $attrs['value'] ?? '';
                $citation = isset($attrs['citation']) ? '<cite>' . wp_kses_post($attrs['citation']) . '</cite>' : '';
                return '<blockquote class="wp-block-quote"><p>' . wp_kses_post($value) . '</p>' . $citation . '</blockquote>';

            default:
                return '';
        }
    }
}
