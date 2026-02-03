/**
 * CTA Block
 *
 * A call-to-action block with title, description, and button.
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
    title: __('CTA Block', 'gen-blocks'),
    description: __('A call-to-action block with title, description, and button.', 'gen-blocks'),
    edit: Edit,
    save,
});
