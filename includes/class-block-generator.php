<?php
/**
 * Block Generator Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Generates and validates Gutenberg block structures
 */
class Block_Generator {

    /**
     * Allowed core block types
     *
     * @var array
     */
    private $core_blocks = [
        'core/group',
        'core/heading',
        'core/paragraph',
        'core/button',
        'core/buttons',
        'core/image',
        'core/columns',
        'core/column',
        'core/spacer',
        'core/separator',
        'core/list',
        'core/list-item',
        'core/quote',
        'core/cover',
        'core/media-text',
        'core/gallery',
        'core/video',
        'core/audio',
        'core/file',
        'core/code',
        'core/preformatted',
        'core/pullquote',
        'core/table',
        'core/verse',
        'core/html',
    ];

    /**
     * Custom GenBlocks block types
     *
     * @var array
     */
    private $custom_blocks = [
        'genblocks/cta',
        'genblocks/hero',
        'genblocks/features',
        'genblocks/testimonial',
    ];

    /**
     * Process AI response into valid block structure
     *
     * @param array $ai_response Raw AI response data.
     * @return array Processed block data.
     */
    public function process($ai_response) {
        // Remove meta information
        $meta = [];
        if (isset($ai_response['_meta'])) {
            $meta = $ai_response['_meta'];
            unset($ai_response['_meta']);
        }

        // Validate and sanitize the block
        $block = $this->validate_block($ai_response);
        $block = $this->sanitize_block($block);

        // Convert to Gutenberg block format
        $gutenberg_block = $this->to_gutenberg_format($block);

        return [
            'block'       => $gutenberg_block,
            'block_json'  => $block,
            'meta'        => $meta,
        ];
    }

    /**
     * Validate block structure
     *
     * @param array $block Block data to validate.
     * @return array Validated block data.
     * @throws \Exception If block is invalid.
     */
    public function validate_block($block) {
        // Check required fields
        if (!isset($block['blockName'])) {
            throw new \Exception(__('Block is missing required blockName field', 'gen-blocks'));
        }

        // Validate block name format
        $block_name = $block['blockName'];
        if (!preg_match('/^[a-z0-9-]+\/[a-z0-9-]+$/', $block_name)) {
            throw new \Exception(__('Invalid block name format', 'gen-blocks'));
        }

        // Ensure block type is allowed
        $allowed_blocks = array_merge($this->core_blocks, $this->custom_blocks);
        if (!in_array($block_name, $allowed_blocks, true)) {
            // Map to closest core block or use group as fallback
            $block['blockName'] = 'core/group';
        }

        // Ensure attrs is an array
        if (!isset($block['attrs']) || !is_array($block['attrs'])) {
            $block['attrs'] = [];
        }

        // Ensure innerBlocks is an array
        if (!isset($block['innerBlocks']) || !is_array($block['innerBlocks'])) {
            $block['innerBlocks'] = [];
        }

        // Recursively validate inner blocks
        foreach ($block['innerBlocks'] as $key => $inner_block) {
            try {
                $block['innerBlocks'][$key] = $this->validate_block($inner_block);
            } catch (\Exception $e) {
                // Remove invalid inner blocks
                unset($block['innerBlocks'][$key]);
            }
        }

        // Re-index array after potential removals
        $block['innerBlocks'] = array_values($block['innerBlocks']);

        return $block;
    }

    /**
     * Sanitize block content
     *
     * @param array $block Block data to sanitize.
     * @return array Sanitized block data.
     */
    public function sanitize_block($block) {
        // Sanitize block name
        $block['blockName'] = sanitize_text_field($block['blockName']);

        // Sanitize attributes
        if (isset($block['attrs']) && is_array($block['attrs'])) {
            $block['attrs'] = $this->sanitize_attributes($block['attrs'], $block['blockName']);
        }

        // Recursively sanitize inner blocks
        if (isset($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $key => $inner_block) {
                $block['innerBlocks'][$key] = $this->sanitize_block($inner_block);
            }
        }

        return $block;
    }

    /**
     * Sanitize block attributes
     *
     * @param array  $attrs      Attributes to sanitize.
     * @param string $block_name Block name for context.
     * @return array Sanitized attributes.
     */
    private function sanitize_attributes($attrs, $block_name) {
        $sanitized = [];

        foreach ($attrs as $key => $value) {
            $key = sanitize_key($key);

            if (is_string($value)) {
                // HTML content fields
                if (in_array($key, ['content', 'citation', 'value', 'text'], true)) {
                    $sanitized[$key] = wp_kses_post($value);
                } elseif ($key === 'url' || $key === 'href') {
                    $sanitized[$key] = esc_url_raw($value);
                } elseif ($key === 'className') {
                    $sanitized[$key] = sanitize_html_class($value, '');
                    // Handle multiple classes
                    if (strpos($value, ' ') !== false) {
                        $classes = explode(' ', $value);
                        $sanitized[$key] = implode(' ', array_map('sanitize_html_class', $classes));
                    }
                } else {
                    $sanitized[$key] = sanitize_text_field($value);
                }
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
     * Convert block to Gutenberg format (serialized)
     *
     * @param array $block Block data.
     * @return string Serialized Gutenberg block.
     */
    public function to_gutenberg_format($block) {
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
            'innerHTML'    => $this->generate_inner_html($block),
            'innerContent' => [],
        ];

        // Process inner blocks
        if (!empty($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $inner_block) {
                $prepared['innerBlocks'][] = $this->prepare_for_serialize($inner_block);
                $prepared['innerContent'][] = null; // Placeholder for inner block
            }
        }

        // Add innerHTML to innerContent if no inner blocks
        if (empty($prepared['innerBlocks']) && !empty($prepared['innerHTML'])) {
            $prepared['innerContent'][] = $prepared['innerHTML'];
        }

        return $prepared;
    }

    /**
     * Generate innerHTML for a block
     *
     * @param array $block Block data.
     * @return string
     */
    private function generate_inner_html($block) {
        $block_name = $block['blockName'];
        $attrs = $block['attrs'] ?? [];

        switch ($block_name) {
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
                return '<div class="wp-block-button"><a class="wp-block-button__link" href="' . esc_url($url) . '">' . esc_html($text) . '</a></div>';

            case 'core/image':
                $url = $attrs['url'] ?? '';
                $alt = $attrs['alt'] ?? '';
                return '<figure class="wp-block-image"><img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"/></figure>';

            case 'core/spacer':
                $height = $attrs['height'] ?? '40px';
                return '<div style="height:' . esc_attr($height) . '" aria-hidden="true" class="wp-block-spacer"></div>';

            case 'core/separator':
                return '<hr class="wp-block-separator"/>';

            default:
                return '';
        }
    }

    /**
     * Convert serialized block back to array
     *
     * @param string $serialized Serialized block string.
     * @return array Block array.
     */
    public function parse_serialized($serialized) {
        $blocks = parse_blocks($serialized);
        return !empty($blocks) ? $blocks[0] : [];
    }

    /**
     * Get list of allowed block types
     *
     * @return array
     */
    public function get_allowed_blocks() {
        return array_merge($this->core_blocks, $this->custom_blocks);
    }

    /**
     * Check if a block type is allowed
     *
     * @param string $block_name Block name to check.
     * @return bool
     */
    public function is_allowed_block($block_name) {
        return in_array($block_name, $this->get_allowed_blocks(), true);
    }
}
