/**
 * Testimonial Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls,
    BlockControls,
    AlignmentToolbar,
    MediaUpload,
    MediaUploadCheck,
} from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ColorPicker,
    ToggleControl,
    Button,
} from '@wordpress/components';

/**
 * Edit component for Testimonial block
 */
export default function Edit({ attributes, setAttributes }) {
    const {
        quote,
        authorName,
        authorRole,
        authorImage,
        backgroundColor,
        quoteColor,
        authorNameColor,
        authorRoleColor,
        quoteIconColor,
        borderRadius,
        showQuoteIcon,
        textAlign,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-testimonial',
        style: {
            backgroundColor,
            borderRadius: `${borderRadius}px`,
            textAlign,
        },
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Layout', 'gen-blocks')} initialOpen={true}>
                    <RangeControl
                        label={__('Border Radius', 'gen-blocks')}
                        value={borderRadius}
                        onChange={(value) => setAttributes({ borderRadius: value })}
                        min={0}
                        max={50}
                    />
                    <ToggleControl
                        label={__('Show Quote Icon', 'gen-blocks')}
                        checked={showQuoteIcon}
                        onChange={(value) => setAttributes({ showQuoteIcon: value })}
                    />
                </PanelBody>

                <PanelBody title={__('Author Image', 'gen-blocks')} initialOpen={false}>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(media) =>
                                setAttributes({
                                    authorImage: media.url,
                                    authorImageId: media.id,
                                })
                            }
                            allowedTypes={['image']}
                            render={({ open }) => (
                                <div>
                                    {authorImage ? (
                                        <div style={{ marginBottom: '10px' }}>
                                            <img
                                                src={authorImage}
                                                alt=""
                                                style={{
                                                    maxWidth: '100%',
                                                    height: 'auto',
                                                    borderRadius: '50%',
                                                    width: '80px',
                                                    height: '80px',
                                                    objectFit: 'cover',
                                                }}
                                            />
                                            <div style={{ marginTop: '10px' }}>
                                                <Button variant="secondary" onClick={open}>
                                                    {__('Replace Image', 'gen-blocks')}
                                                </Button>
                                                <Button
                                                    isDestructive
                                                    onClick={() =>
                                                        setAttributes({
                                                            authorImage: '',
                                                            authorImageId: 0,
                                                        })
                                                    }
                                                    style={{ marginLeft: '8px' }}
                                                >
                                                    {__('Remove', 'gen-blocks')}
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <Button variant="secondary" onClick={open}>
                                            {__('Select Author Image', 'gen-blocks')}
                                        </Button>
                                    )}
                                </div>
                            )}
                        />
                    </MediaUploadCheck>
                </PanelBody>

                <PanelBody title={__('Colors', 'gen-blocks')} initialOpen={false}>
                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Background Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={backgroundColor}
                            onChange={(color) => setAttributes({ backgroundColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Quote Text Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={quoteColor}
                            onChange={(color) => setAttributes({ quoteColor: color })}
                            enableAlpha
                        />
                    </div>

                    {showQuoteIcon && (
                        <div style={{ marginBottom: '20px' }}>
                            <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                                {__('Quote Icon Color', 'gen-blocks')}
                            </p>
                            <ColorPicker
                                color={quoteIconColor}
                                onChange={(color) => setAttributes({ quoteIconColor: color })}
                                enableAlpha
                            />
                        </div>
                    )}

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Author Name Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={authorNameColor}
                            onChange={(color) => setAttributes({ authorNameColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Author Role Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={authorRoleColor}
                            onChange={(color) => setAttributes({ authorRoleColor: color })}
                            enableAlpha
                        />
                    </div>
                </PanelBody>
            </InspectorControls>

            <BlockControls>
                <AlignmentToolbar
                    value={textAlign}
                    onChange={(align) => setAttributes({ textAlign: align })}
                />
            </BlockControls>

            <div {...blockProps}>
                <div
                    className="wp-block-genblocks-testimonial__content"
                    style={{ padding: '40px 30px', maxWidth: '800px', margin: '0 auto' }}
                >
                    {showQuoteIcon && (
                        <div
                            className="wp-block-genblocks-testimonial__quote-icon"
                            style={{ marginBottom: '20px' }}
                        >
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill={quoteIconColor}
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z" />
                            </svg>
                        </div>
                    )}

                    <RichText
                        tagName="blockquote"
                        className="wp-block-genblocks-testimonial__quote"
                        placeholder={__('Enter testimonial quote...', 'gen-blocks')}
                        value={quote}
                        onChange={(value) => setAttributes({ quote: value })}
                        allowedFormats={['core/bold', 'core/italic']}
                        style={{
                            color: quoteColor,
                            fontSize: '1.25rem',
                            lineHeight: 1.7,
                            fontStyle: 'italic',
                            margin: '0 0 24px 0',
                            border: 'none',
                            padding: 0,
                        }}
                    />

                    <div
                        className="wp-block-genblocks-testimonial__author"
                        style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: textAlign === 'center' ? 'center' : textAlign === 'right' ? 'flex-end' : 'flex-start',
                            gap: '16px',
                        }}
                    >
                        {authorImage && (
                            <img
                                src={authorImage}
                                alt=""
                                className="wp-block-genblocks-testimonial__avatar"
                                style={{
                                    width: '56px',
                                    height: '56px',
                                    borderRadius: '50%',
                                    objectFit: 'cover',
                                }}
                            />
                        )}
                        <div className="wp-block-genblocks-testimonial__author-info">
                            <RichText
                                tagName="cite"
                                className="wp-block-genblocks-testimonial__author-name"
                                placeholder={__('Author name', 'gen-blocks')}
                                value={authorName}
                                onChange={(value) => setAttributes({ authorName: value })}
                                allowedFormats={[]}
                                style={{
                                    color: authorNameColor,
                                    fontWeight: '600',
                                    fontStyle: 'normal',
                                    display: 'block',
                                }}
                            />
                            <RichText
                                tagName="span"
                                className="wp-block-genblocks-testimonial__author-role"
                                placeholder={__('Author role', 'gen-blocks')}
                                value={authorRole}
                                onChange={(value) => setAttributes({ authorRole: value })}
                                allowedFormats={[]}
                                style={{
                                    color: authorRoleColor,
                                    fontSize: '0.875rem',
                                    display: 'block',
                                    marginTop: '4px',
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
