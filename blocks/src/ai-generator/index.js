/**
 * AI Generator Block
 *
 * A block that allows users to generate other blocks using AI prompts.
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Block icon
 */
const icon = (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
    </svg>
);

/**
 * Register the block
 */
registerBlockType(metadata.name, {
    ...metadata,
    icon,
    title: __('AI Block Generator', 'gen-blocks'),
    description: __('Generate blocks using AI from natural language prompts.', 'gen-blocks'),
    edit: Edit,
    save,
});
