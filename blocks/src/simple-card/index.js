/**
 * Simple Card Block
 *
 * A simple card with title and description.
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
    title: __('Simple Card', 'gen-blocks'),
    description: __('A simple card with title and description.', 'gen-blocks'),
    edit: Edit,
    save,
});
