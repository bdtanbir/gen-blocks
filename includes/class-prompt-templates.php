<?php
/**
 * Prompt Templates Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Manages AI prompt templates for block generation
 */
class Prompt_Templates {

    /**
     * Get the system prompt for AI
     *
     * @return string
     */
    public function get_system_prompt() {
        $prompt = <<<'PROMPT'
You are an expert WordPress Gutenberg block generator. Your task is to convert natural language descriptions into valid Gutenberg block JSON structures.

## CRITICAL RULES
1. Return ONLY valid JSON - no markdown, no code blocks, no explanations, no text before or after
2. The response must start with { and end with }
3. Use WordPress core blocks whenever possible
4. Generate semantic, accessible HTML structures
5. Include proper attributes for styling and content

## CORE BLOCK REFERENCE

### Container Blocks
- **core/group**: General container for grouping blocks
  - attrs: { "className": "string", "align": "left|center|right|wide|full", "backgroundColor": "string", "textColor": "string", "style": {...} }
  - Can contain innerBlocks

- **core/columns**: Multi-column layout container
  - attrs: { "columns": number, "align": "wide|full" }
  - Must contain core/column innerBlocks

- **core/column**: Single column (only inside core/columns)
  - attrs: { "width": "33.33%", "verticalAlignment": "top|center|bottom" }

### Content Blocks
- **core/heading**: Headings h1-h6
  - attrs: { "level": 1-6, "content": "text", "textAlign": "left|center|right" }

- **core/paragraph**: Text paragraphs
  - attrs: { "content": "text with <strong>HTML</strong> allowed", "align": "left|center|right", "dropCap": boolean }

- **core/list**: Unordered or ordered lists
  - attrs: { "ordered": boolean, "values": "<li>item</li><li>item</li>" }

- **core/quote**: Blockquotes
  - attrs: { "value": "quote text", "citation": "author name" }

### Button Blocks
- **core/buttons**: Container for button(s) - ALWAYS wrap core/button in this
  - attrs: { "layout": { "type": "flex", "justifyContent": "left|center|right" } }

- **core/button**: Single button (must be inside core/buttons)
  - attrs: { "text": "Button Text", "url": "https://...", "backgroundColor": "string", "textColor": "string" }

### Media Blocks
- **core/image**: Images
  - attrs: { "url": "image-url", "alt": "description", "caption": "optional caption", "align": "left|center|right|wide|full" }

- **core/cover**: Background image with overlay
  - attrs: { "url": "image-url", "dimRatio": 50, "overlayColor": "string", "minHeight": 400 }
  - Can contain innerBlocks for overlay content

### Utility Blocks
- **core/spacer**: Vertical spacing
  - attrs: { "height": "40px" }

- **core/separator**: Horizontal divider line
  - attrs: { "className": "is-style-wide|is-style-dots" }

## STYLING GUIDELINES

### Using Style Attribute
```json
{
  "style": {
    "spacing": {
      "padding": { "top": "40px", "right": "20px", "bottom": "40px", "left": "20px" },
      "margin": { "top": "20px", "bottom": "20px" }
    },
    "color": {
      "background": "#f5f5f5",
      "text": "#333333"
    },
    "border": {
      "radius": "8px",
      "width": "1px",
      "color": "#dddddd"
    },
    "typography": {
      "fontSize": "18px",
      "fontWeight": "600"
    }
  }
}
```

### Common Patterns
- For centered content: use "align": "center" or textAlign
- For full-width sections: use "align": "full" on group/cover
- For card layouts: use core/group with backgroundColor and border radius
- For CTAs: use core/group containing heading, paragraph, and core/buttons

## OUTPUT FORMAT
Return a single JSON object with this structure:
{
  "blockName": "core/group",
  "attrs": {},
  "innerBlocks": []
}

Remember: Return ONLY the JSON object, nothing else.
PROMPT;

        return apply_filters('genblocks_system_prompt', $prompt);
    }

    /**
     * Build user prompt with context
     *
     * @param string $user_input User's natural language request.
     * @param array  $context    Additional context information.
     * @return string
     */
    public function build_user_prompt($user_input, $context = []) {
        $parts = [];

        // Add context if provided
        if (!empty($context)) {
            $context_text = $this->format_context($context);
            if (!empty($context_text)) {
                $parts[] = "CONTEXT:\n" . $context_text;
            }
        }

        // Add the user's request
        $parts[] = "USER REQUEST:\n" . $user_input;

        // Add generation instruction
        $parts[] = "Generate the Gutenberg block JSON now:";

        $full_prompt = implode("\n\n", $parts);

        return apply_filters('genblocks_user_prompt', $full_prompt, $user_input, $context);
    }

    /**
     * Format context information
     *
     * @param array $context Context data.
     * @return string
     */
    private function format_context($context) {
        $formatted = [];

        if (!empty($context['page_type'])) {
            $formatted[] = "- Page type: " . sanitize_text_field($context['page_type']);
        }

        if (!empty($context['existing_blocks'])) {
            $blocks = is_array($context['existing_blocks'])
                ? implode(', ', $context['existing_blocks'])
                : $context['existing_blocks'];
            $formatted[] = "- Existing blocks on page: " . sanitize_text_field($blocks);
        }

        if (!empty($context['theme_colors'])) {
            $colors = is_array($context['theme_colors'])
                ? wp_json_encode($context['theme_colors'])
                : $context['theme_colors'];
            $formatted[] = "- Theme colors available: " . $colors;
        }

        if (!empty($context['site_industry'])) {
            $formatted[] = "- Website industry: " . sanitize_text_field($context['site_industry']);
        }

        if (!empty($context['tone'])) {
            $formatted[] = "- Desired tone: " . sanitize_text_field($context['tone']);
        }

        if (!empty($context['brand_name'])) {
            $formatted[] = "- Brand/Company name: " . sanitize_text_field($context['brand_name']);
        }

        return implode("\n", $formatted);
    }

    /**
     * Get prompt for specific block type
     *
     * @param string $block_type Block type (cta, hero, features, etc.).
     * @param array  $variables  Variables to replace in prompt.
     * @return string
     */
    public function get_block_type_prompt($block_type, $variables = []) {
        $prompts = $this->get_block_type_prompts();

        if (!isset($prompts[$block_type])) {
            return '';
        }

        $prompt = $prompts[$block_type];

        // Replace variables
        foreach ($variables as $key => $value) {
            $prompt = str_replace('{{' . $key . '}}', $value, $prompt);
        }

        return $prompt;
    }

    /**
     * Get all block type specific prompts
     *
     * @return array
     */
    private function get_block_type_prompts() {
        return [
            'cta' => 'Create a call-to-action section with a compelling headline "{{title}}", supporting text "{{description}}", and a primary button labeled "{{button_text}}" linking to "{{button_url}}". Center the content and use a contrasting background color.',

            'hero' => 'Create a hero section with a large headline "{{title}}", subtitle "{{subtitle}}", and {{button_count}} call-to-action buttons. Make it visually impactful with good spacing.',

            'features' => 'Create a {{columns}}-column features section. Each feature should have a heading and description. The features are: {{features_list}}',

            'testimonial' => 'Create a testimonial block with the quote "{{quote}}" attributed to "{{author}}" who is "{{role}}" at "{{company}}". Style it elegantly.',

            'pricing' => 'Create a pricing card for the "{{plan_name}}" plan at "{{price}}" per {{period}}. Include these features: {{features}}. Add a "{{button_text}}" button.',

            'faq' => 'Create an FAQ section with the following questions and answers: {{qa_pairs}}',

            'team' => 'Create a team member card for "{{name}}", "{{role}}". Include their image placeholder and a short bio: "{{bio}}".',

            'stats' => 'Create a statistics section showing these metrics: {{stats}}. Make the numbers prominent and add labels.',

            'contact' => 'Create a contact information section with: email "{{email}}", phone "{{phone}}", and address "{{address}}". Include a call-to-action.',

            'newsletter' => 'Create a newsletter signup section with headline "{{title}}" and description "{{description}}". Note: The form itself will need to be added separately.',
        ];
    }

    /**
     * Get example prompts for UI
     *
     * @return array
     */
    public function get_example_prompts() {
        return [
            [
                'category' => 'Hero Sections',
                'examples' => [
                    'Create a hero section with a large headline, subtitle, and two buttons',
                    'Create a hero with background image, centered white text, and a single CTA button',
                    'Create a split hero with text on the left and image placeholder on the right',
                ],
            ],
            [
                'category' => 'Call to Action',
                'examples' => [
                    'Create a CTA section with title, description, and button',
                    'Create an urgent CTA with countdown-style messaging and prominent button',
                    'Create a minimal CTA with just a headline and button, centered',
                ],
            ],
            [
                'category' => 'Features',
                'examples' => [
                    'Create a 3-column feature section with icons, headings, and descriptions',
                    'Create a features list with checkmarks for a SaaS product',
                    'Create a 2-column feature comparison layout',
                ],
            ],
            [
                'category' => 'Testimonials',
                'examples' => [
                    'Create a testimonial block with quote, author name, and company',
                    'Create a testimonial carousel placeholder with 3 quotes',
                    'Create a large quote testimonial with star rating',
                ],
            ],
            [
                'category' => 'Content Sections',
                'examples' => [
                    'Create an about section with heading, two paragraphs, and a button',
                    'Create a two-column layout with text on left and image on right',
                    'Create a stats section showing 4 key metrics with large numbers',
                ],
            ],
            [
                'category' => 'Contact & Forms',
                'examples' => [
                    'Create a contact section with address, phone, and email',
                    'Create a newsletter signup section with headline and description',
                    'Create a simple contact CTA with heading and button',
                ],
            ],
        ];
    }

    /**
     * Enhance prompt with best practices
     *
     * @param string $prompt Original prompt.
     * @return string
     */
    public function enhance_prompt($prompt) {
        $enhancements = [];

        // Check for vague requests and add specificity hints
        $vague_terms = ['nice', 'good', 'cool', 'awesome', 'beautiful'];
        foreach ($vague_terms as $term) {
            if (stripos($prompt, $term) !== false) {
                $enhancements[] = 'Use professional styling with good contrast and spacing';
                break;
            }
        }

        // Add responsive hint if layout-related
        if (preg_match('/column|grid|layout|side by side/i', $prompt)) {
            $enhancements[] = 'Ensure the layout works well on different screen sizes';
        }

        // Add accessibility hint
        if (preg_match('/button|link|image/i', $prompt)) {
            $enhancements[] = 'Include appropriate accessibility attributes';
        }

        if (empty($enhancements)) {
            return $prompt;
        }

        return $prompt . "\n\nAdditional requirements:\n- " . implode("\n- ", $enhancements);
    }

    /**
     * Validate prompt before sending to AI
     *
     * @param string $prompt Prompt to validate.
     * @return true|\WP_Error
     */
    public function validate_prompt($prompt) {
        // Check length
        if (strlen($prompt) < 10) {
            return new \WP_Error(
                'prompt_too_short',
                __('Please provide a more detailed description of the block you want to create.', 'get-blocks')
            );
        }

        if (strlen($prompt) > 2000) {
            return new \WP_Error(
                'prompt_too_long',
                __('Your description is too long. Please keep it under 2000 characters.', 'get-blocks')
            );
        }

        // Check for potentially harmful content
        $blocked_patterns = [
            '/\b(script|javascript|onclick|onerror)\b/i',
            '/\b(eval|exec|system)\s*\(/i',
            '/<\s*script/i',
        ];

        foreach ($blocked_patterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                return new \WP_Error(
                    'invalid_prompt',
                    __('Your prompt contains invalid content. Please describe the visual block you want to create.', 'get-blocks')
                );
            }
        }

        return true;
    }
}
