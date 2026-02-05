/**
 * Testimonial Block
 *
 * A testimonial with quote, author, and optional avatar.
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Register the block
 */
registerBlockType(metadata.name, {
    ...metadata,
    title: __('Testimonial Block', 'gen-blocks'),
    description: __('Display a testimonial with quote, author, and optional avatar.', 'gen-blocks'),
    edit: Edit,
    save,
});
