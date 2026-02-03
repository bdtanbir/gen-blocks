/**
 * Hero Block
 *
 * A hero section with heading, subtitle, and buttons.
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
    title: __('Hero Block', 'gen-blocks'),
    description: __('A hero section with heading, subtitle, and buttons.', 'gen-blocks'),
    edit: Edit,
    save,
});
