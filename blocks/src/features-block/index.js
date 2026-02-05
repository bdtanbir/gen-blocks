/**
 * Features Block
 *
 * A grid of features with icons, titles, and descriptions.
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
    title: __('Features Block', 'gen-blocks'),
    description: __('Display a grid of features with icons, titles, and descriptions.', 'gen-blocks'),
    edit: Edit,
    save,
});
