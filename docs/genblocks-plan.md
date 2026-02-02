# WordPress AI Gutenberg Block Generator Plugin
## Complete Development Plan

> **Project Goal:** Build a WordPress Gutenberg plugin that uses AI to generate blocks from natural language prompts, with a Vue.js admin dashboard for management.

---

## 📋 Table of Contents
- [Project Overview](#project-overview)
- [Technical Architecture](#technical-architecture)
- [Development Phases](#development-phases)
- [Implementation Details](#implementation-details)
- [Security & Performance](#security--performance)
- [Monetization Strategy](#monetization-strategy)
- [Resources & References](#resources--references)

---

## 🎯 Project Overview

### Core Features
- ✅ AI-powered block generation from text prompts
- ✅ Native Gutenberg editor integration
- ✅ Vue.js admin dashboard
- ✅ Template library system
- ✅ Usage analytics and tracking
- ✅ Multi-tier pricing (Free/Pro)

### Example User Flow
```
User types: "Create a CTA section with title, description and button"
           ↓
AI generates: Structured block JSON
           ↓
Plugin renders: Beautiful CTA block in Gutenberg editor
           ↓
User can: Edit, customize, or regenerate
```

### Target Audience
- Content creators and bloggers
- Marketing teams
- Web designers
- Agency clients
- Non-technical WordPress users

---

## 🏗️ Technical Architecture

### Tech Stack

#### WordPress/PHP Layer
```
├── PHP 7.4+ (WordPress core)
├── WordPress 6.0+ (Gutenberg support)
├── REST API (custom endpoints)
├── Options API (settings storage)
└── Transients API (caching)
```

#### Frontend - Gutenberg Blocks
```
├── React 18+ (required by Gutenberg)
├── @wordpress/scripts (build tool)
├── @wordpress/components (UI)
├── @wordpress/block-editor (editor integration)
└── @wordpress/data (state management)
```

#### Frontend - Admin Dashboard
```
├── Vue 3 (Composition API)
├── Vue Router (navigation)
├── Pinia (state management)
├── Vite (build tool)
├── Tailwind CSS (styling)
└── Chart.js(use echart https://echarts.apache.org/) (analytics visualization)
```

#### AI Integration
```
├── OpenAI API (GPT-4) OR
├── Anthropic Claude API OR
├── Google Gemini API
├── JSON Schema validation
└── Rate limiting & caching
```

### Plugin File Structure
```
get-blocks/
│
├── get-blocks.php          # Main plugin file
├── composer.json                     # PHP dependencies
├── package.json                      # Node dependencies
├── webpack.config.js                 # Build configuration
│
├── admin/                            # Vue.js Dashboard
│   ├── src/
│   │   ├── main.js
│   │   ├── App.vue
│   │   ├── router/
│   │   ├── stores/
│   │   ├── components/
│   │   └── views/
│   │       ├── Settings.vue
│   │       ├── Analytics.vue
│   │       ├── Templates.vue
│   │       └── History.vue
│   └── dist/                         # Built files
│
├── blocks/                           # Gutenberg Blocks
│   ├── src/
│   │   ├── ai-input/                 # AI prompt input block
│   │   ├── cta-block/                # Custom CTA block
│   │   ├── hero-block/               # Custom Hero block
│   │   └── container-block/          # AI generated container
│   └── build/                        # Built blocks
│
├── includes/                         # PHP Classes
│   ├── class-plugin.php              # Main plugin class
│   ├── class-ai-engine.php           # AI API integration
│   ├── class-block-generator.php     # Block JSON generator
│   ├── class-rest-api.php            # REST endpoints
│   ├── class-settings.php            # Settings management
│   ├── class-template-library.php    # Template system
│   └── class-usage-tracker.php       # Analytics tracking
│
├── assets/                           # Static Assets
│   ├── css/
│   ├── js/
│   └── images/
│
├── templates/                        # Block Templates
│   └── json/                         # Pre-built templates
│
└── tests/                            # Testing
    ├── phpunit/
    └── jest/
```

---

## 📅 Development Phases

## Phase 1: Foundation & Setup (Week 1-2)

### Objectives
- Set up development environment
- Create plugin skeleton
- Choose and configure AI provider
- Establish build pipeline

### Tasks

#### 1.1 Environment Setup
- [ ] Install Local by Flywheel or Docker for WordPress development
- [ ] Install Node.js 18+ and npm
- [ ] Install PHP 7.4+ and Composer
- [ ] Set up version control (Git)
- [ ] Create GitHub repository

#### 1.2 Plugin Skeleton
```php
/**
 * Plugin Name: AI Gutenberg Block Generator
 * Plugin URI: https://yoursite.com/ai-gutenberg-blocks
 * Description: Generate Gutenberg blocks using AI from natural language prompts
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL v2 or later
 * Text Domain: ai-gutenberg-blocks
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */
```

Create basic plugin structure:
- [ ] Main plugin file with activation/deactivation hooks
- [ ] Autoloader setup
- [ ] Register text domain for translations
- [ ] Enqueue scripts and styles

#### 1.3 AI Provider Selection

**Comparison Table:**

| Provider | Free Tier | Cost (Paid) | Best For | Structured Output |
|----------|-----------|-------------|----------|-------------------|
| **OpenAI GPT-4** | No | $0.01-0.03/1k tokens | General purpose | ⭐⭐⭐⭐ |
| **Anthropic Claude** | Limited | $0.008-0.024/1k tokens | JSON output | ⭐⭐⭐⭐⭐ |
| **Google Gemini** | Generous | $0.00025-0.0005/1k tokens | Cost-effective | ⭐⭐⭐⭐ |
| **Hugging Face** | Limited | Pay-per-use | Open source models | ⭐⭐⭐ |

**Recommendation:** Claude API for best structured output or Gemini for cost-effectiveness

- [ ] Sign up for chosen AI provider
- [ ] Get API keys
- [ ] Test API integration with simple PHP script
- [ ] Document rate limits and pricing

#### 1.4 Build Tools Configuration

**For Gutenberg Blocks (React):**
```json
{
  "scripts": {
    "start": "wp-scripts start",
    "build": "wp-scripts build",
    "packages-update": "wp-scripts packages-update"
  },
  "devDependencies": {
    "@wordpress/scripts": "^26.0.0"
  }
}
```

**For Admin Dashboard (Vue):**
```json
{
  "scripts": {
    "admin:dev": "vite",
    "admin:build": "vite build"
  },
  "dependencies": {
    "vue": "^3.3.0",
    "vue-router": "^4.2.0",
    "pinia": "^2.1.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^4.0.0",
    "vite": "^4.3.0"
  }
}
```

### Deliverables
- ✅ Working WordPress plugin (basic)
- ✅ AI API connection established
- ✅ Build pipeline configured
- ✅ Development environment ready

---

## Phase 2: Backend Foundation (Week 3-4)

### Objectives
- Build PHP backend architecture
- Create REST API endpoints
- Implement AI integration layer
- Set up settings system

### Tasks

#### 2.1 Main Plugin Class

**File:** `includes/class-plugin.php`

```php
<?php
namespace AIGutenbergBlocks;

class Plugin {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        // Autoload classes
    }
    
    private function init_hooks() {
        add_action('init', [$this, 'register_blocks']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
    }
}
```

Tasks:
- [ ] Implement singleton pattern
- [ ] Create autoloader
- [ ] Register hooks and filters
- [ ] Handle plugin activation/deactivation

#### 2.2 AI Engine Class

**File:** `includes/class-ai-engine.php`

```php
<?php
namespace AIGutenbergBlocks;

class AI_Engine {
    private $api_key;
    private $api_provider; // 'openai', 'claude', 'gemini'
    private $cache_duration = 3600; // 1 hour
    
    public function __construct() {
        $this->api_key = get_option('aigb_api_key');
        $this->api_provider = get_option('aigb_api_provider', 'claude');
    }
    
    public function generate_block($prompt, $context = []) {
        // Check cache first
        $cache_key = 'aigb_' . md5($prompt . serialize($context));
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
        
        // Generate prompt with context
        $full_prompt = $this->build_prompt($prompt, $context);
        
        // Call AI API
        $response = $this->call_ai_api($full_prompt);
        
        // Parse and validate response
        $block_data = $this->parse_response($response);
        
        // Cache result
        set_transient($cache_key, $block_data, $this->cache_duration);
        
        return $block_data;
    }
    
    private function build_prompt($user_input, $context) {
        $system_prompt = "You are a WordPress Gutenberg block generator...";
        // Build structured prompt
    }
    
    private function call_ai_api($prompt) {
        switch ($this->api_provider) {
            case 'openai':
                return $this->call_openai($prompt);
            case 'claude':
                return $this->call_claude($prompt);
            case 'gemini':
                return $this->call_gemini($prompt);
        }
    }
    
    private function call_claude($prompt) {
        // Claude API implementation
    }
    
    private function parse_response($response) {
        // Parse JSON and validate structure
    }
}
```

Tasks:
- [ ] Implement API authentication
- [ ] Create prompt templates
- [ ] Add error handling
- [ ] Implement caching system
- [ ] Add rate limiting

#### 2.3 Block Generator Class

**File:** `includes/class-block-generator.php`

```php
<?php
namespace AIGutenbergBlocks;

class Block_Generator {
    
    public function create_block_json($ai_response) {
        // Convert AI response to Gutenberg block JSON
        $block = [
            'blockName' => $this->determine_block_type($ai_response),
            'attrs' => $this->extract_attributes($ai_response),
            'innerBlocks' => $this->generate_inner_blocks($ai_response),
            'innerHTML' => '',
        ];
        
        return $block;
    }
    
    private function determine_block_type($response) {
        // Analyze response to determine block type
        // 'core/group', 'core/columns', 'aigb/cta', etc.
    }
    
    private function extract_attributes($response) {
        // Extract block attributes from AI response
    }
    
    private function generate_inner_blocks($response) {
        // Generate nested blocks recursively
    }
    
    public function validate_block($block) {
        // Validate block structure
        // Sanitize content
        // Ensure Gutenberg compatibility
    }
}
```

Tasks:
- [ ] Create block JSON structure
- [ ] Implement block validation
- [ ] Add sanitization
- [ ] Handle nested blocks

#### 2.4 REST API Endpoints

**File:** `includes/class-rest-api.php`

```php
<?php
namespace AIGutenbergBlocks;

class REST_API {
    
    public function register_routes() {
        // Generate block from prompt
        register_rest_route('ai-blocks/v1', '/generate', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_block'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'prompt' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'context' => [
                    'required' => false,
                    'type' => 'object',
                ],
            ],
        ]);
        
        // Get/update settings
        register_rest_route('ai-blocks/v1', '/settings', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'handle_settings'],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);
        
        // Get usage statistics
        register_rest_route('ai-blocks/v1', '/usage', [
            'methods' => 'GET',
            'callback' => [$this, 'get_usage_stats'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
        
        // Template library
        register_rest_route('ai-blocks/v1', '/templates', [
            'methods' => 'GET',
            'callback' => [$this, 'get_templates'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
    }
    
    public function generate_block($request) {
        $prompt = $request->get_param('prompt');
        $context = $request->get_param('context');
        
        // Track usage
        $this->track_usage();
        
        // Check rate limits
        if (!$this->check_rate_limit()) {
            return new \WP_Error('rate_limit', 'Rate limit exceeded', ['status' => 429]);
        }
        
        // Generate block
        $ai_engine = new AI_Engine();
        $block_generator = new Block_Generator();
        
        try {
            $ai_response = $ai_engine->generate_block($prompt, $context);
            $block_json = $block_generator->create_block_json($ai_response);
            
            return rest_ensure_response([
                'success' => true,
                'block' => $block_json,
                'usage' => $this->get_current_usage(),
            ]);
        } catch (\Exception $e) {
            return new \WP_Error('generation_failed', $e->getMessage(), ['status' => 500]);
        }
    }
    
    private function track_usage() {
        // Track API calls per user
    }
    
    private function check_rate_limit() {
        // Implement rate limiting logic
    }
}
```

Tasks:
- [ ] Register all REST routes
- [ ] Implement authentication
- [ ] Add input validation
- [ ] Create response formatting
- [ ] Implement error handling

#### 2.5 Settings Management

**File:** `includes/class-settings.php`

```php
<?php
namespace AIGutenbergBlocks;

class Settings {
    
    private $option_name = 'aigb_settings';
    
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function register_settings() {
        register_setting('aigb_settings_group', $this->option_name, [
            'sanitize_callback' => [$this, 'sanitize_settings'],
        ]);
    }
    
    public function get_settings() {
        $defaults = [
            'api_provider' => 'claude',
            'api_key' => '',
            'rate_limit' => 100, // per day
            'cache_enabled' => true,
            'cache_duration' => 3600,
            'default_styles' => [],
        ];
        
        $settings = get_option($this->option_name, $defaults);
        return wp_parse_args($settings, $defaults);
    }
    
    public function update_settings($new_settings) {
        $current = $this->get_settings();
        $updated = array_merge($current, $new_settings);
        return update_option($this->option_name, $updated);
    }
    
    private function sanitize_settings($input) {
        // Sanitize all settings
        $sanitized = [];
        
        if (isset($input['api_key'])) {
            // Encrypt API key before storing
            $sanitized['api_key'] = $this->encrypt_api_key($input['api_key']);
        }
        
        // Sanitize other settings
        
        return $sanitized;
    }
    
    private function encrypt_api_key($key) {
        // Implement encryption (use WordPress salts)
    }
}
```

Tasks:
- [ ] Create settings structure
- [ ] Implement encryption for API keys
- [ ] Add default values
- [ ] Create getter/setter methods

#### 2.6 Usage Tracker

**File:** `includes/class-usage-tracker.php`

```php
<?php
namespace AIGutenbergBlocks;

class Usage_Tracker {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aigb_usage';
    }
    
    public function create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            prompt text NOT NULL,
            tokens_used int(11) NOT NULL,
            cost decimal(10,6) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function track_request($user_id, $prompt, $tokens, $cost, $status) {
        global $wpdb;
        
        return $wpdb->insert(
            $this->table_name,
            [
                'user_id' => $user_id,
                'prompt' => $prompt,
                'tokens_used' => $tokens,
                'cost' => $cost,
                'status' => $status,
            ],
            ['%d', '%s', '%d', '%f', '%s']
        );
    }
    
    public function get_usage_stats($user_id, $period = 'month') {
        global $wpdb;
        
        // Get usage statistics
        // Total requests, tokens used, cost, etc.
    }
}
```

Tasks:
- [ ] Create database table
- [ ] Implement tracking methods
- [ ] Add analytics queries
- [ ] Create cleanup routines

### Deliverables
- ✅ Complete PHP backend
- ✅ REST API endpoints functional
- ✅ AI integration working
- ✅ Settings system operational
- ✅ Usage tracking implemented

---

## Phase 3: Prompt Engineering & AI Integration (Week 5-6)

### Objectives
- Design effective AI prompts
- Create block templates
- Implement response parsing
- Build validation system

### Tasks

#### 3.1 Prompt Template System

**System Prompt Structure:**

```javascript
const SYSTEM_PROMPT = `You are an expert WordPress Gutenberg block generator. Your task is to convert natural language descriptions into valid Gutenberg block JSON.

RULES:
1. Always return ONLY valid JSON, no markdown, no explanations
2. Use standard WordPress core blocks when possible
3. Include proper attributes for styling and content
4. Nest blocks logically for complex layouts
5. Generate accessible, semantic HTML
6. Include appropriate CSS classes

AVAILABLE CORE BLOCKS:
- core/group (container with inner blocks)
- core/heading (h1-h6 headings)
- core/paragraph (text content)
- core/button (call-to-action buttons)
- core/image (images)
- core/columns (multi-column layouts)
- core/column (single column)
- core/spacer (vertical spacing)

CUSTOM BLOCKS:
- aigb/cta (call-to-action section)
- aigb/hero (hero section)
- aigb/features (feature grid)

OUTPUT FORMAT:
{
  "blockName": "core/group",
  "attrs": {
    "className": "my-custom-class",
    "backgroundColor": "primary",
    "align": "wide"
  },
  "innerBlocks": [
    {
      "blockName": "core/heading",
      "attrs": {
        "level": 2,
        "content": "Heading text"
      },
      "innerBlocks": []
    }
  ]
}`;
```

**User Prompt Templates:**

```php
<?php
namespace AIGutenbergBlocks\Prompts;

class Prompt_Templates {
    
    public function build_prompt($user_input, $context = []) {
        $system_prompt = $this->get_system_prompt();
        $context_info = $this->format_context($context);
        
        $full_prompt = "{$system_prompt}\n\n";
        
        if (!empty($context_info)) {
            $full_prompt .= "CURRENT CONTEXT:\n{$context_info}\n\n";
        }
        
        $full_prompt .= "USER REQUEST:\n{$user_input}\n\n";
        $full_prompt .= "Generate the Gutenberg block JSON now:";
        
        return $full_prompt;
    }
    
    private function get_system_prompt() {
        // Return system prompt
    }
    
    private function format_context($context) {
        if (empty($context)) {
            return '';
        }
        
        $formatted = [];
        
        if (isset($context['existing_blocks'])) {
            $formatted[] = "Existing blocks on page: " . implode(', ', $context['existing_blocks']);
        }
        
        if (isset($context['theme_colors'])) {
            $formatted[] = "Theme colors: " . json_encode($context['theme_colors']);
        }
        
        if (isset($context['page_type'])) {
            $formatted[] = "Page type: " . $context['page_type'];
        }
        
        return implode("\n", $formatted);
    }
    
    public function get_example_prompts() {
        return [
            'cta' => [
                'input' => 'Create a CTA section with a title, description, and button',
                'context' => [],
                'expected_output' => $this->get_cta_example(),
            ],
            'hero' => [
                'input' => 'Create a hero section with large heading, subtitle, and two buttons',
                'context' => [],
                'expected_output' => $this->get_hero_example(),
            ],
            // More examples
        ];
    }
    
    private function get_cta_example() {
        return json_encode([
            'blockName' => 'core/group',
            'attrs' => [
                'className' => 'cta-section',
                'backgroundColor' => 'primary',
                'align' => 'full',
            ],
            'innerBlocks' => [
                [
                    'blockName' => 'core/heading',
                    'attrs' => [
                        'level' => 2,
                        'content' => 'Ready to Get Started?',
                        'textAlign' => 'center',
                    ],
                    'innerBlocks' => [],
                ],
                [
                    'blockName' => 'core/paragraph',
                    'attrs' => [
                        'content' => 'Join thousands of satisfied customers today.',
                        'align' => 'center',
                    ],
                    'innerBlocks' => [],
                ],
                [
                    'blockName' => 'core/buttons',
                    'attrs' => [
                        'layout' => ['type' => 'flex', 'justifyContent' => 'center'],
                    ],
                    'innerBlocks' => [
                        [
                            'blockName' => 'core/button',
                            'attrs' => [
                                'text' => 'Get Started',
                                'url' => '#',
                            ],
                            'innerBlocks' => [],
                        ],
                    ],
                ],
            ],
        ], JSON_PRETTY_PRINT);
    }
}
```

Tasks:
- [ ] Create system prompt template
- [ ] Add context-aware prompts
- [ ] Build example library
- [ ] Test with various inputs
- [ ] Optimize for better outputs

#### 3.2 Response Parser

```php
<?php
namespace AIGutenbergBlocks\Parser;

class Response_Parser {
    
    public function parse($ai_response) {
        // Clean the response
        $cleaned = $this->clean_response($ai_response);
        
        // Parse JSON
        $parsed = json_decode($cleaned, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        // Validate structure
        $this->validate_block_structure($parsed);
        
        // Sanitize content
        $sanitized = $this->sanitize_block($parsed);
        
        return $sanitized;
    }
    
    private function clean_response($response) {
        // Remove markdown code blocks
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*/', '', $response);
        
        // Remove any text before first {
        $first_brace = strpos($response, '{');
        if ($first_brace !== false) {
            $response = substr($response, $first_brace);
        }
        
        // Remove any text after last }
        $last_brace = strrpos($response, '}');
        if ($last_brace !== false) {
            $response = substr($response, 0, $last_brace + 1);
        }
        
        return trim($response);
    }
    
    private function validate_block_structure($block) {
        // Check required fields
        if (!isset($block['blockName'])) {
            throw new \Exception('Missing blockName in response');
        }
        
        // Validate block name format
        if (!preg_match('/^[a-z0-9-]+\/[a-z0-9-]+$/', $block['blockName'])) {
            throw new \Exception('Invalid blockName format');
        }
        
        // Validate attributes
        if (isset($block['attrs']) && !is_array($block['attrs'])) {
            throw new \Exception('Block attributes must be an array');
        }
        
        // Validate inner blocks recursively
        if (isset($block['innerBlocks'])) {
            if (!is_array($block['innerBlocks'])) {
                throw new \Exception('innerBlocks must be an array');
            }
            
            foreach ($block['innerBlocks'] as $inner_block) {
                $this->validate_block_structure($inner_block);
            }
        }
        
        return true;
    }
    
    private function sanitize_block($block) {
        // Sanitize block name
        $block['blockName'] = sanitize_text_field($block['blockName']);
        
        // Sanitize attributes
        if (isset($block['attrs'])) {
            $block['attrs'] = $this->sanitize_attributes($block['attrs']);
        }
        
        // Sanitize inner blocks recursively
        if (isset($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $key => $inner_block) {
                $block['innerBlocks'][$key] = $this->sanitize_block($inner_block);
            }
        }
        
        return $block;
    }
    
    private function sanitize_attributes($attrs) {
        $sanitized = [];
        
        foreach ($attrs as $key => $value) {
            if (is_string($value)) {
                // Check if it's HTML content
                if (in_array($key, ['content', 'html', 'innerHTML'])) {
                    $sanitized[$key] = wp_kses_post($value);
                } else {
                    $sanitized[$key] = sanitize_text_field($value);
                }
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize_attributes($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}
```

Tasks:
- [ ] Implement JSON parsing
- [ ] Add error handling
- [ ] Create validation rules
- [ ] Implement sanitization
- [ ] Test with edge cases

#### 3.3 Block Templates Library

Create pre-built templates for common use cases:

```php
<?php
namespace AIGutenbergBlocks\Templates;

class Template_Library {
    
    public function get_templates() {
        return [
            'cta-simple' => $this->cta_simple(),
            'cta-with-image' => $this->cta_with_image(),
            'hero-centered' => $this->hero_centered(),
            'hero-split' => $this->hero_split(),
            'features-grid' => $this->features_grid(),
            'testimonial-card' => $this->testimonial_card(),
            'pricing-table' => $this->pricing_table(),
        ];
    }
    
    private function cta_simple() {
        return [
            'name' => 'Simple CTA',
            'description' => 'A centered call-to-action with heading, text, and button',
            'category' => 'call-to-action',
            'keywords' => ['cta', 'call to action', 'button', 'conversion'],
            'structure' => [
                'blockName' => 'core/group',
                'attrs' => [
                    'className' => 'wp-block-group cta-section',
                    'layout' => ['type' => 'constrained'],
                ],
                'innerBlocks' => [
                    [
                        'blockName' => 'core/heading',
                        'attrs' => [
                            'level' => 2,
                            'textAlign' => 'center',
                            'content' => '{{title}}',
                        ],
                    ],
                    [
                        'blockName' => 'core/paragraph',
                        'attrs' => [
                            'align' => 'center',
                            'content' => '{{description}}',
                        ],
                    ],
                    [
                        'blockName' => 'core/buttons',
                        'attrs' => [
                            'layout' => ['type' => 'flex', 'justifyContent' => 'center'],
                        ],
                        'innerBlocks' => [
                            [
                                'blockName' => 'core/button',
                                'attrs' => [
                                    'text' => '{{buttonText}}',
                                    'url' => '{{buttonUrl}}',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    public function apply_template($template_id, $variables) {
        $template = $this->get_templates()[$template_id];
        
        if (!$template) {
            throw new \Exception('Template not found');
        }
        
        return $this->replace_variables($template['structure'], $variables);
    }
    
    private function replace_variables($structure, $variables) {
        $json = json_encode($structure);
        
        foreach ($variables as $key => $value) {
            $json = str_replace('{{' . $key . '}}', $value, $json);
        }
        
        return json_decode($json, true);
    }
}
```

Tasks:
- [ ] Create template structures
- [ ] Add variable replacement
- [ ] Categorize templates
- [ ] Add preview images
- [ ] Test all templates

### Deliverables
- ✅ Optimized AI prompts
- ✅ Response parsing working
- ✅ Validation system complete
- ✅ Template library functional

---

## Phase 4: Gutenberg Block Development (Week 7-8)

### Objectives
- Build AI input interface
- Create custom blocks
- Integrate with Gutenberg editor
- Implement block insertion logic

### Tasks

#### 4.1 AI Input Block/Panel

**File:** `blocks/src/ai-input/index.js`

```javascript
import { registerBlockType } from '@wordpress/blocks';
import { useState } from '@wordpress/element';
import { 
    TextControl, 
    Button, 
    Spinner,
    Notice 
} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { createBlock, parse } from '@wordpress/blocks';

registerBlockType('aigb/ai-input', {
    title: 'AI Block Generator',
    description: 'Generate blocks using AI from text prompts',
    icon: 'lightbulb',
    category: 'common',
    
    edit: function({ attributes, setAttributes, clientId }) {
        const [prompt, setPrompt] = useState('');
        const [loading, setLoading] = useState(false);
        const [error, setError] = useState(null);
        
        const { insertBlocks, removeBlock } = useDispatch('core/block-editor');
        
        const handleGenerate = async () => {
            if (!prompt.trim()) {
                setError('Please enter a prompt');
                return;
            }
            
            setLoading(true);
            setError(null);
            
            try {
                const response = await apiFetch({
                    path: '/ai-blocks/v1/generate',
                    method: 'POST',
                    data: {
                        prompt: prompt,
                        context: {
                            // Add context data
                        }
                    }
                });
                
                if (response.success) {
                    // Convert block JSON to Gutenberg blocks
                    const blocks = convertToBlocks(response.block);
                    
                    // Insert generated blocks after current block
                    insertBlocks(blocks, undefined, clientId);
                    
                    // Remove the AI input block
                    removeBlock(clientId);
                } else {
                    setError('Failed to generate block');
                }
            } catch (err) {
                setError(err.message || 'An error occurred');
            } finally {
                setLoading(false);
            }
        };
        
        return (
            <div className="aigb-input-wrapper">
                <TextControl
                    label="Describe the block you want to create"
                    placeholder="e.g., Create a CTA section with title, description and button"
                    value={prompt}
                    onChange={setPrompt}
                    disabled={loading}
                />
                
                {error && (
                    <Notice status="error" isDismissible={false}>
                        {error}
                    </Notice>
                )}
                
                <Button
                    variant="primary"
                    onClick={handleGenerate}
                    disabled={loading || !prompt.trim()}
                >
                    {loading ? (
                        <>
                            <Spinner /> Generating...
                        </>
                    ) : (
                        'Generate Block'
                    )}
                </Button>
                
                <div className="aigb-examples">
                    <p>Example prompts:</p>
                    <ul>
                        <li onClick={() => setPrompt('Create a hero section with heading and button')}>
                            Hero section with heading and button
                        </li>
                        <li onClick={() => setPrompt('Create a 3-column feature section')}>
                            3-column feature section
                        </li>
                    </ul>
                </div>
            </div>
        );
    },
    
    save: function() {
        return null; // This block is only for editor
    }
});

// Helper function to convert block JSON to Gutenberg blocks
function convertToBlocks(blockData) {
    return createBlock(
        blockData.blockName,
        blockData.attrs,
        blockData.innerBlocks?.map(innerBlock => convertToBlocks(innerBlock))
    );
}
```

Tasks:
- [ ] Create input interface
- [ ] Add loading states
- [ ] Implement error handling
- [ ] Add example prompts
- [ ] Style the component

#### 4.2 Editor Sidebar Panel

**File:** `blocks/src/ai-input/sidebar.js`

```javascript
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useState } from '@wordpress/element';
import { 
    PanelBody, 
    TextareaControl,
    Button,
    Notice 
} from '@wordpress/components';
import { lightbulb } from '@wordpress/icons';

const AISidebar = () => {
    const [prompt, setPrompt] = useState('');
    const [loading, setLoading] = useState(false);
    
    return (
        <>
            <PluginSidebarMoreMenuItem
                target="ai-block-generator-sidebar"
                icon={lightbulb}
            >
                AI Block Generator
            </PluginSidebarMoreMenuItem>
            
            <PluginSidebar
                name="ai-block-generator-sidebar"
                title="AI Block Generator"
                icon={lightbulb}
            >
                <PanelBody title="Generate Blocks">
                    <TextareaControl
                        label="Describe what you want to create"
                        value={prompt}
                        onChange={setPrompt}
                        rows={5}
                        placeholder="e.g., Create a pricing table with 3 columns..."
                    />
                    
                    <Button
                        variant="primary"
                        onClick={() => {/* Generate logic */}}
                        disabled={loading}
                        isPressed={loading}
                    >
                        {loading ? 'Generating...' : 'Generate'}
                    </Button>
                </PanelBody>
                
                <PanelBody title="Recent Generations" initialOpen={false}>
                    {/* Show history of generated blocks */}
                </PanelBody>
                
                <PanelBody title="Templates" initialOpen={false}>
                    {/* Show template library */}
                </PanelBody>
            </PluginSidebar>
        </>
    );
};

registerPlugin('ai-block-generator', {
    render: AISidebar
});
```

Tasks:
- [ ] Create sidebar plugin
- [ ] Add to editor UI
- [ ] Implement generation logic
- [ ] Add history tracking
- [ ] Style the sidebar

#### 4.3 Custom CTA Block

**File:** `blocks/src/cta-block/index.js`

```javascript
import { registerBlockType } from '@wordpress/blocks';
import { 
    InspectorControls,
    BlockControls,
    RichText,
    URLInput,
    ColorPalette
} from '@wordpress/block-editor';
import { 
    PanelBody,
    RangeControl,
    ToolbarButton
} from '@wordpress/components';

registerBlockType('aigb/cta', {
    title: 'AI CTA Block',
    description: 'Call-to-action block generated by AI',
    icon: 'megaphone',
    category: 'aigb',
    attributes: {
        title: {
            type: 'string',
            default: 'Ready to Get Started?'
        },
        description: {
            type: 'string',
            default: 'Join thousands of satisfied customers.'
        },
        buttonText: {
            type: 'string',
            default: 'Get Started'
        },
        buttonUrl: {
            type: 'string',
            default: '#'
        },
        backgroundColor: {
            type: 'string',
            default: '#0073aa'
        },
        textColor: {
            type: 'string',
            default: '#ffffff'
        },
        padding: {
            type: 'number',
            default: 40
        }
    },
    
    edit: function({ attributes, setAttributes }) {
        const {
            title,
            description,
            buttonText,
            buttonUrl,
            backgroundColor,
            textColor,
            padding
        } = attributes;
        
        return (
            <>
                <InspectorControls>
                    <PanelBody title="Settings">
                        <RangeControl
                            label="Padding"
                            value={padding}
                            onChange={(value) => setAttributes({ padding: value })}
                            min={0}
                            max={100}
                        />
                    </PanelBody>
                    
                    <PanelBody title="Colors" initialOpen={false}>
                        <p>Background Color</p>
                        <ColorPalette
                            value={backgroundColor}
                            onChange={(color) => setAttributes({ backgroundColor: color })}
                        />
                        
                        <p>Text Color</p>
                        <ColorPalette
                            value={textColor}
                            onChange={(color) => setAttributes({ textColor: color })}
                        />
                    </PanelBody>
                </InspectorControls>
                
                <BlockControls>
                    <ToolbarButton
                        icon="admin-generic"
                        label="Regenerate with AI"
                        onClick={() => {
                            // Trigger AI regeneration
                        }}
                    />
                </BlockControls>
                
                <div 
                    className="aigb-cta-block"
                    style={{
                        backgroundColor,
                        color: textColor,
                        padding: `${padding}px`
                    }}
                >
                    <RichText
                        tagName="h2"
                        value={title}
                        onChange={(value) => setAttributes({ title: value })}
                        placeholder="Enter title..."
                    />
                    
                    <RichText
                        tagName="p"
                        value={description}
                        onChange={(value) => setAttributes({ description: value })}
                        placeholder="Enter description..."
                    />
                    
                    <div className="aigb-cta-button-wrapper">
                        <RichText
                            tagName="span"
                            className="aigb-cta-button"
                            value={buttonText}
                            onChange={(value) => setAttributes({ buttonText: value })}
                            placeholder="Button text..."
                        />
                        
                        <URLInput
                            value={buttonUrl}
                            onChange={(value) => setAttributes({ buttonUrl: value })}
                        />
                    </div>
                </div>
            </>
        );
    },
    
    save: function({ attributes }) {
        const {
            title,
            description,
            buttonText,
            buttonUrl,
            backgroundColor,
            textColor,
            padding
        } = attributes;
        
        return (
            <div 
                className="aigb-cta-block"
                style={{
                    backgroundColor,
                    color: textColor,
                    padding: `${padding}px`
                }}
            >
                <h2>{title}</h2>
                <p>{description}</p>
                <a href={buttonUrl} className="aigb-cta-button">
                    {buttonText}
                </a>
            </div>
        );
    }
});
```

Tasks:
- [ ] Create custom block
- [ ] Add controls and settings
- [ ] Implement styling options
- [ ] Add AI regeneration button
- [ ] Test in editor

#### 4.4 Block Registration (PHP Side)

**File:** `includes/class-blocks.php`

```php
<?php
namespace AIGutenbergBlocks;

class Blocks {
    
    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
    }
    
    public function register_blocks() {
        // Register block category
        add_filter('block_categories_all', function($categories) {
            return array_merge(
                $categories,
                [
                    [
                        'slug' => 'aigb',
                        'title' => 'AI Blocks',
                        'icon' => 'lightbulb',
                    ],
                ]
            );
        });
        
        // Register blocks
        register_block_type(
            plugin_dir_path(dirname(__FILE__)) . 'blocks/build/ai-input'
        );
        
        register_block_type(
            plugin_dir_path(dirname(__FILE__)) . 'blocks/build/cta-block'
        );
        
        // Register more blocks...
    }
    
    public function enqueue_editor_assets() {
        // Editor-only scripts
        wp_enqueue_script(
            'aigb-editor',
            plugins_url('blocks/build/editor.js', dirname(__FILE__)),
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'],
            filemtime(plugin_dir_path(dirname(__FILE__)) . 'blocks/build/editor.js')
        );
        
        wp_localize_script('aigb-editor', 'aigbData', [
            'apiUrl' => rest_url('ai-blocks/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
    
    public function enqueue_block_assets() {
        // Frontend and editor styles
        wp_enqueue_style(
            'aigb-blocks',
            plugins_url('blocks/build/style.css', dirname(__FILE__)),
            [],
            filemtime(plugin_dir_path(dirname(__FILE__)) . 'blocks/build/style.css')
        );
    }
}
```

Tasks:
- [ ] Register all blocks
- [ ] Create block category
- [ ] Enqueue scripts properly
- [ ] Add localization
- [ ] Test registration

### Deliverables
- ✅ AI input interface working
- ✅ Custom blocks created
- ✅ Editor integration complete
- ✅ Block insertion functional

---

## Phase 5: Vue.js Admin Dashboard (Week 9-10)

### Objectives
- Build Vue admin application
- Create settings interface
- Add analytics dashboard
- Implement template library

### Tasks

#### 5.1 Vue Application Setup

**File:** `admin/src/main.js`

```javascript
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './assets/style.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);

// Global error handler
app.config.errorHandler = (err, instance, info) => {
    console.error('Global error:', err, info);
};

// Make WordPress data available globally
app.config.globalProperties.$wp = window.aigbAdmin;

app.mount('#aigb-admin-app');
```

**File:** `admin/src/router/index.js`

```javascript
import { createRouter, createWebHashHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Settings from '../views/Settings.vue';
import Analytics from '../views/Analytics.vue';
import Templates from '../views/Templates.vue';
import History from '../views/History.vue';

const routes = [
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard
    },
    {
        path: '/settings',
        name: 'Settings',
        component: Settings
    },
    {
        path: '/analytics',
        name: 'Analytics',
        component: Analytics
    },
    {
        path: '/templates',
        name: 'Templates',
        component: Templates
    },
    {
        path: '/history',
        name: 'History',
        component: History
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

export default router;
```

**File:** `admin/src/App.vue`

```vue
<template>
  <div id="aigb-admin">
    <div class="aigb-sidebar">
      <div class="aigb-logo">
        <h1>AI Gutenberg Blocks</h1>
      </div>
      
      <nav class="aigb-nav">
        <router-link to="/" class="nav-item">
          <span class="dashicons dashicons-dashboard"></span>
          Dashboard
        </router-link>
        <router-link to="/settings" class="nav-item">
          <span class="dashicons dashicons-admin-generic"></span>
          Settings
        </router-link>
        <router-link to="/analytics" class="nav-item">
          <span class="dashicons dashicons-chart-bar"></span>
          Analytics
        </router-link>
        <router-link to="/templates" class="nav-item">
          <span class="dashicons dashicons-layout"></span>
          Templates
        </router-link>
        <router-link to="/history" class="nav-item">
          <span class="dashicons dashicons-backup"></span>
          History
        </router-link>
      </nav>
    </div>
    
    <div class="aigb-content">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  console.log('Admin dashboard mounted');
});
</script>

<style>
#aigb-admin {
  display: flex;
  min-height: 100vh;
}

.aigb-sidebar {
  width: 250px;
  background: #23282d;
  color: #fff;
  padding: 20px;
}

.aigb-content {
  flex: 1;
  padding: 30px;
  background: #f1f1f1;
}

.aigb-nav .nav-item {
  display: flex;
  align-items: center;
  padding: 12px;
  color: #fff;
  text-decoration: none;
  margin-bottom: 5px;
  border-radius: 4px;
}

.aigb-nav .nav-item:hover,
.aigb-nav .nav-item.router-link-active {
  background: #0073aa;
}

.aigb-nav .dashicons {
  margin-right: 10px;
}
</style>
```

Tasks:
- [ ] Set up Vue 3 app
- [ ] Configure router
- [ ] Create main layout
- [ ] Add navigation
- [ ] Style base components

#### 5.2 Settings Page

**File:** `admin/src/views/Settings.vue`

```vue
<template>
  <div class="settings-page">
    <h1>Settings</h1>
    
    <div class="settings-card">
      <h2>API Configuration</h2>
      
      <div class="form-group">
        <label>AI Provider</label>
        <select v-model="settings.api_provider" @change="saveSettings">
          <option value="claude">Anthropic Claude</option>
          <option value="openai">OpenAI GPT-4</option>
          <option value="gemini">Google Gemini</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>API Key</label>
        <input 
          type="password" 
          v-model="settings.api_key" 
          @blur="saveSettings"
          placeholder="Enter your API key"
        />
        <p class="description">
          Your API key is encrypted before storage.
        </p>
      </div>
      
      <div class="form-group">
        <label>Rate Limit (per day)</label>
        <input 
          type="number" 
          v-model.number="settings.rate_limit" 
          @change="saveSettings"
          min="1"
          max="1000"
        />
      </div>
      
      <button @click="testConnection" :disabled="testing">
        {{ testing ? 'Testing...' : 'Test Connection' }}
      </button>
      
      <div v-if="connectionStatus" :class="['status-message', connectionStatus.type]">
        {{ connectionStatus.message }}
      </div>
    </div>
    
    <div class="settings-card">
      <h2>Cache Settings</h2>
      
      <div class="form-group">
        <label>
          <input 
            type="checkbox" 
            v-model="settings.cache_enabled"
            @change="saveSettings"
          />
          Enable caching
        </label>
      </div>
      
      <div class="form-group" v-if="settings.cache_enabled">
        <label>Cache Duration (seconds)</label>
        <input 
          type="number" 
          v-model.number="settings.cache_duration"
          @change="saveSettings"
          min="60"
          max="86400"
        />
      </div>
      
      <button @click="clearCache">Clear Cache</button>
    </div>
    
    <div class="settings-card">
      <h2>Default Styles</h2>
      
      <div class="form-group">
        <label>Primary Color</label>
        <input 
          type="color" 
          v-model="settings.primary_color"
          @change="saveSettings"
        />
      </div>
      
      <div class="form-group">
        <label>Default Alignment</label>
        <select v-model="settings.default_alignment" @change="saveSettings">
          <option value="left">Left</option>
          <option value="center">Center</option>
          <option value="right">Right</option>
          <option value="wide">Wide</option>
          <option value="full">Full</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSettingsStore } from '../stores/settings';

const settingsStore = useSettingsStore();
const settings = ref({});
const testing = ref(false);
const connectionStatus = ref(null);

onMounted(async () => {
  await settingsStore.loadSettings();
  settings.value = { ...settingsStore.settings };
});

async function saveSettings() {
  try {
    await settingsStore.updateSettings(settings.value);
    showNotification('Settings saved successfully', 'success');
  } catch (error) {
    showNotification('Failed to save settings', 'error');
  }
}

async function testConnection() {
  testing.value = true;
  connectionStatus.value = null;
  
  try {
    const result = await settingsStore.testApiConnection();
    connectionStatus.value = {
      type: 'success',
      message: 'Connection successful!'
    };
  } catch (error) {
    connectionStatus.value = {
      type: 'error',
      message: error.message || 'Connection failed'
    };
  } finally {
    testing.value = false;
  }
}

async function clearCache() {
  if (confirm('Are you sure you want to clear the cache?')) {
    await settingsStore.clearCache();
    showNotification('Cache cleared', 'success');
  }
}

function showNotification(message, type) {
  // Implement notification system
}
</script>

<style scoped>
.settings-page {
  max-width: 800px;
}

.settings-card {
  background: white;
  padding: 25px;
  margin-bottom: 20px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="number"],
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.description {
  margin-top: 5px;
  color: #666;
  font-size: 13px;
}

button {
  padding: 10px 20px;
  background: #0073aa;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background: #005a87;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.status-message {
  margin-top: 15px;
  padding: 10px;
  border-radius: 4px;
}

.status-message.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-message.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>
```

Tasks:
- [ ] Create settings form
- [ ] Implement save functionality
- [ ] Add connection testing
- [ ] Style the interface
- [ ] Add validation

#### 5.3 Analytics Dashboard

**File:** `admin/src/views/Analytics.vue`

```vue
<template>
  <div class="analytics-page">
    <h1>Usage Analytics</h1>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <span class="dashicons dashicons-chart-line"></span>
        </div>
        <div class="stat-content">
          <h3>Total Generations</h3>
          <p class="stat-number">{{ stats.total_generations }}</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <span class="dashicons dashicons-money"></span>
        </div>
        <div class="stat-content">
          <h3>Total Cost</h3>
          <p class="stat-number">${{ stats.total_cost.toFixed(2) }}</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <span class="dashicons dashicons-performance"></span>
        </div>
        <div class="stat-content">
          <h3>Tokens Used</h3>
          <p class="stat-number">{{ formatNumber(stats.total_tokens) }}</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <span class="dashicons dashicons-calendar"></span>
        </div>
        <div class="stat-content">
          <h3>This Month</h3>
          <p class="stat-number">{{ stats.this_month }}</p>
        </div>
      </div>
    </div>
    
    <div class="chart-card">
      <h2>Usage Over Time</h2>
      <canvas ref="usageChart"></canvas>
    </div>
    
    <div class="chart-card">
      <h2>Most Popular Block Types</h2>
      <canvas ref="blockTypesChart"></canvas>
    </div>
    
    <div class="table-card">
      <h2>Recent Activity</h2>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Prompt</th>
            <th>Block Type</th>
            <th>Tokens</th>
            <th>Cost</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in recentActivity" :key="item.id">
            <td>{{ formatDate(item.created_at) }}</td>
            <td class="prompt-cell">{{ item.prompt }}</td>
            <td>{{ item.block_type }}</td>
            <td>{{ item.tokens_used }}</td>
            <td>${{ item.cost.toFixed(4) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';
import { useAnalyticsStore } from '../stores/analytics';

Chart.register(...registerables);

const analyticsStore = useAnalyticsStore();
const stats = ref({});
const recentActivity = ref([]);
const usageChart = ref(null);
const blockTypesChart = ref(null);

onMounted(async () => {
  await loadAnalytics();
  createCharts();
});

async function loadAnalytics() {
  await analyticsStore.loadStats();
  stats.value = analyticsStore.stats;
  recentActivity.value = analyticsStore.recentActivity;
}

function createCharts() {
  // Usage over time chart
  new Chart(usageChart.value, {
    type: 'line',
    data: {
      labels: analyticsStore.chartData.labels,
      datasets: [{
        label: 'Generations',
        data: analyticsStore.chartData.values,
        borderColor: '#0073aa',
        backgroundColor: 'rgba(0, 115, 170, 0.1)',
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
  
  // Block types chart
  new Chart(blockTypesChart.value, {
    type: 'doughnut',
    data: {
      labels: analyticsStore.blockTypes.labels,
      datasets: [{
        data: analyticsStore.blockTypes.values,
        backgroundColor: [
          '#0073aa',
          '#00a32a',
          '#f0b849',
          '#f56e28',
          '#8c5ec6'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
}

function formatNumber(num) {
  return num.toLocaleString();
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}
</script>

<style scoped>
.analytics-page {
  max-width: 1200px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
}

.stat-icon {
  font-size: 40px;
  margin-right: 20px;
  color: #0073aa;
}

.stat-number {
  font-size: 32px;
  font-weight: bold;
  margin: 0;
}

.chart-card,
.table-card {
  background: white;
  padding: 25px;
  margin-bottom: 20px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

canvas {
  height: 300px !important;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background: #f8f9fa;
  font-weight: 600;
}

.prompt-cell {
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
```

Tasks:
- [ ] Create analytics views
- [ ] Implement Chart.js integration
- [ ] Add statistics calculations
- [ ] Style the dashboard
- [ ] Add export functionality

#### 5.4 Pinia Store for State Management

**File:** `admin/src/stores/settings.js`

```javascript
import { defineStore } from 'pinia';
import apiFetch from '@wordpress/api-fetch';

export const useSettingsStore = defineStore('settings', {
  state: () => ({
    settings: {},
    loading: false,
    error: null
  }),
  
  actions: {
    async loadSettings() {
      this.loading = true;
      try {
        const response = await apiFetch({
          path: '/ai-blocks/v1/settings',
          method: 'GET'
        });
        this.settings = response;
        this.error = null;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    
    async updateSettings(newSettings) {
      this.loading = true;
      try {
        const response = await apiFetch({
          path: '/ai-blocks/v1/settings',
          method: 'POST',
          data: newSettings
        });
        this.settings = response;
        this.error = null;
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async testApiConnection() {
      const response = await apiFetch({
        path: '/ai-blocks/v1/test-connection',
        method: 'POST'
      });
      return response;
    },
    
    async clearCache() {
      await apiFetch({
        path: '/ai-blocks/v1/cache/clear',
        method: 'POST'
      });
    }
  }
});
```

**File:** `admin/src/stores/analytics.js`

```javascript
import { defineStore } from 'pinia';
import apiFetch from '@wordpress/api-fetch';

export const useAnalyticsStore = defineStore('analytics', {
  state: () => ({
    stats: {},
    chartData: {},
    blockTypes: {},
    recentActivity: [],
    loading: false
  }),
  
  actions: {
    async loadStats(period = 'month') {
      this.loading = true;
      try {
        const response = await apiFetch({
          path: `/ai-blocks/v1/usage?period=${period}`,
          method: 'GET'
        });
        
        this.stats = response.stats;
        this.chartData = response.chartData;
        this.blockTypes = response.blockTypes;
        this.recentActivity = response.recentActivity;
      } catch (error) {
        console.error('Failed to load analytics:', error);
      } finally {
        this.loading = false;
      }
    }
  }
});
```

Tasks:
- [ ] Create Pinia stores
- [ ] Implement API calls
- [ ] Add error handling
- [ ] Create computed properties
- [ ] Test state management

#### 5.5 Admin Page Registration (PHP)

**File:** `includes/class-admin.php`

```php
<?php
namespace AIGutenbergBlocks;

class Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'AI Gutenberg Blocks',
            'AI Blocks',
            'manage_options',
            'ai-gutenberg-blocks',
            [$this, 'render_admin_page'],
            'dashicons-lightbulb',
            30
        );
    }
    
    public function render_admin_page() {
        echo '<div id="aigb-admin-app"></div>';
    }
    
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_ai-gutenberg-blocks' !== $hook) {
            return;
        }
        
        // Enqueue Vue app
        $asset_file = include(plugin_dir_path(dirname(__FILE__)) . 'admin/dist/assets/index.php');
        
        wp_enqueue_script(
            'aigb-admin',
            plugins_url('admin/dist/assets/index.js', dirname(__FILE__)),
            $asset_file['dependencies'] ?? [],
            $asset_file['version'] ?? '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'aigb-admin-style',
            plugins_url('admin/dist/assets/index.css', dirname(__FILE__)),
            [],
            $asset_file['version'] ?? '1.0.0'
        );
        
        // Localize script with WordPress data
        wp_localize_script('aigb-admin', 'aigbAdmin', [
            'apiUrl' => rest_url('ai-blocks/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => plugins_url('', dirname(__FILE__)),
        ]);
        
        // Enqueue WordPress styles
        wp_enqueue_style('wp-components');
    }
}
```

Tasks:
- [ ] Register admin menu
- [ ] Create admin page
- [ ] Enqueue Vue app
- [ ] Pass WordPress data
- [ ] Test integration

### Deliverables
- ✅ Vue admin dashboard complete
- ✅ Settings page functional
- ✅ Analytics working
- ✅ Template library ready
- ✅ Full integration with WordPress

---

## Phase 6: Testing & Optimization (Week 11)

### Objectives
- Comprehensive testing
- Performance optimization
- Security hardening
- Bug fixes

### Tasks

#### 6.1 Testing Strategy

**Unit Tests (PHPUnit):**
```php
<?php
namespace AIGutenbergBlocks\Tests;

use PHPUnit\Framework\TestCase;
use AIGutenbergBlocks\AI_Engine;

class AI_Engine_Test extends TestCase {
    
    public function test_generate_block_with_valid_prompt() {
        $engine = new AI_Engine();
        $result = $engine->generate_block('Create a CTA section');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('blockName', $result);
    }
    
    public function test_rate_limiting() {
        // Test rate limiting logic
    }
    
    public function test_cache_functionality() {
        // Test caching
    }
}
```

**Integration Tests:**
- [ ] Test REST API endpoints
- [ ] Test block generation flow
- [ ] Test settings save/load
- [ ] Test template system
- [ ] Test usage tracking

**Frontend Tests (Jest):**
```javascript
import { render, fireEvent } from '@testing-library/react';
import AIInputBlock from '../blocks/src/ai-input';

describe('AI Input Block', () => {
  test('renders input field', () => {
    const { getByPlaceholderText } = render(<AIInputBlock />);
    expect(getByPlaceholderText(/describe the block/i)).toBeInTheDocument();
  });
  
  test('handles form submission', async () => {
    // Test form submission
  });
});
```

Tasks:
- [ ] Write unit tests
- [ ] Write integration tests
- [ ] Write frontend tests
- [ ] Set up CI/CD
- [ ] Achieve 70%+ code coverage

#### 6.2 Performance Optimization

**Caching Strategy:**
```php
// Implement multi-level caching
class Cache_Manager {
    
    // Level 1: Object cache (in-memory)
    public function get_from_object_cache($key) {
        return wp_cache_get($key, 'aigb');
    }
    
    // Level 2: Transients (database)
    public function get_from_transient($key) {
        return get_transient('aigb_' . $key);
    }
    
    // Level 3: External cache (Redis/Memcached)
    public function get_from_external_cache($key) {
        if (class_exists('Redis')) {
            // Use Redis
        }
    }
}
```

**Database Optimization:**
```sql
-- Add indexes for faster queries
ALTER TABLE wp_aigb_usage ADD INDEX idx_user_created (user_id, created_at);
ALTER TABLE wp_aigb_usage ADD INDEX idx_status (status);
```

**Asset Optimization:**
- [ ] Minify JavaScript and CSS
- [ ] Implement code splitting
- [ ] Lazy load components
- [ ] Optimize images
- [ ] Enable GZIP compression

Tasks:
- [ ] Implement caching layers
- [ ] Optimize database queries
- [ ] Minimize bundle size
- [ ] Reduce API calls
- [ ] Test performance

#### 6.3 Security Hardening

**Input Validation:**
```php
class Security {
    
    public function validate_prompt($prompt) {
        // Sanitize input
        $prompt = sanitize_textarea_field($prompt);
        
        // Check length
        if (strlen($prompt) > 1000) {
            throw new \Exception('Prompt too long');
        }
        
        // Check for malicious content
        if ($this->contains_malicious_content($prompt)) {
            throw new \Exception('Invalid prompt');
        }
        
        return $prompt;
    }
    
    public function validate_api_key($key) {
        // Validate API key format
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            throw new \Exception('Invalid API key format');
        }
        
        return $key;
    }
}
```

**API Key Encryption:**
```php
class Encryption {
    
    public function encrypt($data) {
        $key = $this->get_encryption_key();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    public function decrypt($data) {
        $key = $this->get_encryption_key();
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }
    
    private function get_encryption_key() {
        // Use WordPress salts as encryption key
        return hash('sha256', SECURE_AUTH_KEY . SECURE_AUTH_SALT);
    }
}
```

Tasks:
- [ ] Sanitize all inputs
- [ ] Validate all outputs
- [ ] Implement nonce verification
- [ ] Encrypt sensitive data
- [ ] Add CSRF protection
- [ ] Conduct security audit

### Deliverables
- ✅ Comprehensive test suite
- ✅ Optimized performance
- ✅ Hardened security
- ✅ Bug-free codebase

---

## Phase 7: Documentation & Release (Week 12)

### Objectives
- Complete documentation
- Prepare for release
- Set up support system
- Launch marketing

### Tasks

#### 7.1 User Documentation

Create comprehensive guides:

**Getting Started Guide:**
1. Installation instructions
2. Initial setup
3. First block generation
4. Customization options

**User Manual:**
- Features overview
- How to use AI prompts
- Best practices
- Troubleshooting

**Video Tutorials:**
- Installation walkthrough
- Basic usage
- Advanced features
- Tips and tricks

Tasks:
- [ ] Write user documentation
- [ ] Create video tutorials
- [ ] Build knowledge base
- [ ] Add tooltips in UI

#### 7.2 Developer Documentation

**API Documentation:**
```markdown
# REST API Reference

## Generate Block

POST /wp-json/ai-blocks/v1/generate

### Parameters
- `prompt` (string, required): Natural language description
- `context` (object, optional): Additional context

### Response
```json
{
  "success": true,
  "block": {
    "blockName": "core/group",
    "attrs": {...},
    "innerBlocks": [...]
  }
}
```

## Hooks Reference

### Filters

`aigb_prompt_template`
Modify the AI prompt template.

`aigb_block_attributes`
Modify generated block attributes.

### Actions

`aigb_block_generated`
Fires after a block is generated.

`aigb_usage_tracked`
Fires after usage is tracked.
```

Tasks:
- [ ] Document all APIs
- [ ] Create code examples
- [ ] Write hook reference
- [ ] Add inline documentation

#### 7.3 Release Preparation

**Version Control:**
```
v1.0.0
├── CHANGELOG.md
├── README.md
├── LICENSE.txt
└── plugin files
```

**Plugin Repository:**
- [ ] Create WordPress.org SVN account
- [ ] Prepare plugin assets (banner, icon, screenshots)
- [ ] Write plugin description
- [ ] Submit for review

**Premium Version Setup:**
- [ ] Set up licensing system
- [ ] Create update server
- [ ] Build payment integration
- [ ] Set up customer portal

Tasks:
- [ ] Finalize version 1.0.0
- [ ] Create release notes
- [ ] Prepare assets
- [ ] Submit to WordPress.org

#### 7.4 Marketing Materials

**Website Landing Page:**
- Hero section with demo
- Features list
- Pricing table
- Testimonials
- FAQ
- Sign-up form

**Content Marketing:**
- Blog posts
- Case studies
- Comparison articles
- Tutorial videos

**Social Media:**
- Twitter announcements
- LinkedIn posts
- YouTube demos
- Facebook groups

Tasks:
- [ ] Build landing page
- [ ] Create marketing copy
- [ ] Design promotional graphics
- [ ] Plan launch campaign

### Deliverables
- ✅ Complete documentation
- ✅ WordPress.org submission
- ✅ Marketing materials ready
- ✅ Support system in place

---

## 🔒 Security & Performance

### Security Best Practices

1. **Input Validation**
   - Sanitize all user inputs
   - Validate API responses
   - Use WordPress sanitization functions

2. **API Key Protection**
   - Encrypt API keys in database
   - Never expose keys in frontend
   - Use WordPress capabilities for access control

3. **CSRF Protection**
   - Use WordPress nonces
   - Verify nonce on all form submissions
   - Check user capabilities

4. **Content Sanitization**
   - Use `wp_kses_post()` for HTML content
   - Use `sanitize_text_field()` for text
   - Escape output with `esc_html()`, `esc_attr()`

5. **Rate Limiting**
   - Implement per-user rate limits
   - Add IP-based throttling
   - Monitor for abuse

### Performance Optimization

1. **Caching Strategy**
   - Cache AI responses (1 hour default)
   - Use WordPress transients
   - Implement object caching
   - Consider external cache (Redis/Memcached)

2. **Database Optimization**
   - Add proper indexes
   - Clean up old records
   - Use prepared statements
   - Implement pagination

3. **Asset Optimization**
   - Minify JS and CSS
   - Use code splitting
   - Lazy load components
   - Optimize images

4. **API Optimization**
   - Batch requests where possible
   - Implement request debouncing
   - Use efficient prompts
   - Monitor token usage

---

## 💰 Monetization Strategy

### Free Version Features
- 50 AI generations per month
- Basic block types (CTA, Hero)
- Community support
- Core features

### Pro Version Features ($29-79/year)
- Unlimited generations
- All block types
- Priority support
- Custom templates
- Advanced styling options
- Usage analytics
- Multi-site license

### Agency License ($199-499/year)
- All Pro features
- White label option
- Client management
- Priority phone support
- 50+ site license

### Additional Revenue Streams
1. **Template Marketplace**
   - Sell premium templates
   - Commission on sales (30%)

2. **Add-on Plugins**
   - Advanced block packs
   - Integration plugins
   - Custom AI models

3. **Services**
   - Custom development
   - White label licensing
   - Training and consulting

---

## 📚 Resources & References

### WordPress Development
- [Gutenberg Handbook](https://developer.wordpress.org/block-editor/)
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [REST API Handbook](https://developer.wordpress.org/rest-api/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)

### AI APIs
- [OpenAI API Documentation](https://platform.openai.com/docs)
- [Anthropic Claude Documentation](https://docs.anthropic.com)
- [Google Gemini API](https://ai.google.dev/docs)

### Frontend Development
- [React Documentation](https://react.dev)
- [Vue.js Documentation](https://vuejs.org)
- [Gutenberg Components](https://developer.wordpress.org/block-editor/reference-guides/components/)

### Tools & Libraries
- [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts)
- [Vite](https://vitejs.dev)
- [Pinia](https://pinia.vuejs.org)
- [Chart.js](https://www.chartjs.org)

---

## 🎯 Success Metrics

### Technical Metrics
- Code coverage: >70%
- Page load time: <2 seconds
- API response time: <3 seconds
- Error rate: <1%

### Business Metrics
- Active installations: 1,000+ in first 3 months
- Pro conversion rate: 5-10%
- Customer satisfaction: 4.5+ stars
- Support ticket resolution: <24 hours

### User Engagement
- Average generations per user: 20+/month
- Template usage: 50%+ of generations
- Return rate: 60%+ monthly
- Feature adoption: 30%+ use advanced features

---

## 📝 Next Steps

1. **Immediate Actions (This Week)**
   - [ ] Set up development environment
   - [ ] Create GitHub repository
   - [ ] Choose AI provider and get API key
   - [ ] Create basic plugin structure

2. **Short Term (2-4 Weeks)**
   - [ ] Build PHP backend
   - [ ] Implement AI integration
   - [ ] Create basic Gutenberg blocks
   - [ ] Test core functionality

3. **Medium Term (2-3 Months)**
   - [ ] Complete Vue dashboard
   - [ ] Add advanced features
   - [ ] Conduct testing
   - [ ] Prepare documentation

4. **Long Term (3-6 Months)**
   - [ ] Launch on WordPress.org
   - [ ] Release Pro version
   - [ ] Build community
   - [ ] Expand features

---

## 🤝 Support & Community

### Getting Help
- GitHub Issues: Report bugs and request features
- Documentation: Comprehensive guides and API reference
- Community Forum: Connect with other users
- Email Support: Premium support for Pro users

### Contributing
We welcome contributions! Please see:
- CONTRIBUTING.md
- Code of Conduct
- Development Guidelines

---

## 📄 License

This plugin is licensed under GPL v2 or later.

---

**Ready to build something amazing? Let's get started! 🚀**

For questions or clarification on any phase, feel free to reach out.
