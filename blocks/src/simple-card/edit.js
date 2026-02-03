/**
 * Simple Card Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Edit component for Simple Card block
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {JSX.Element} Block editor component
 */
export default function Edit({ attributes, setAttributes }) {
    const { title, description } = attributes;

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-simple-card',
    });

    return (
        <div {...blockProps}>
            <RichText
                tagName="h3"
                className="wp-block-genblocks-simple-card__title"
                placeholder={__('Enter title...', 'gen-blocks')}
                value={title}
                onChange={(value) => setAttributes({ title: value })}
                allowedFormats={['core/bold', 'core/italic']}
            />
            <RichText
                tagName="p"
                className="wp-block-genblocks-simple-card__description"
                placeholder={__('Enter description...', 'gen-blocks')}
                value={description}
                onChange={(value) => setAttributes({ description: value })}
                allowedFormats={['core/bold', 'core/italic', 'core/link']}
            />
        </div>
    );
}
