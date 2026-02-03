/**
 * GenBlocks - Gutenberg Block Entry Point
 *
 * This file is the main entry point for all Gutenberg blocks.
 */

import './style.scss';
import './editor.scss';

/**
 * Import and register blocks
 */
import './simple-card';
import './ai-generator';
import './cta-block';
import './hero-block';

/**
 * Import and register plugins
 */
import './plugins/ai-sidebar';

console.log('GenBlocks: Editor scripts loaded');
