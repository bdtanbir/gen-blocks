/**
 * Hero Block - Editor Component
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
    URLInput,
} from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ColorPicker,
    ToggleControl,
    Button,
} from '@wordpress/components';

/**
 * Edit component for Hero block
 */
export default function Edit({ attributes, setAttributes }) {
    const {
        heading,
        subtitle,
        primaryButtonText,
        primaryButtonUrl,
        secondaryButtonText,
        secondaryButtonUrl,
        showSecondaryButton,
        backgroundColor,
        textColor,
        primaryButtonColor,
        primaryButtonTextColor,
        secondaryButtonColor,
        secondaryButtonTextColor,
        textAlign,
        minHeight,
        overlayOpacity,
        backgroundImage,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-hero',
        style: {
            backgroundColor,
            color: textColor,
            minHeight: `${minHeight}px`,
            textAlign,
            backgroundImage: backgroundImage ? `url(${backgroundImage})` : undefined,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            position: 'relative',
        },
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Layout', 'gen-blocks')} initialOpen={true}>
                    <RangeControl
                        label={__('Minimum Height', 'gen-blocks')}
                        value={minHeight}
                        onChange={(value) => setAttributes({ minHeight: value })}
                        min={200}
                        max={1000}
                        step={10}
                    />
                </PanelBody>

                <PanelBody title={__('Background Image', 'gen-blocks')} initialOpen={false}>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(media) => setAttributes({ backgroundImage: media.url })}
                            allowedTypes={['image']}
                            render={({ open }) => (
                                <div>
                                    {backgroundImage ? (
                                        <div style={{ marginBottom: '10px' }}>
                                            <img
                                                src={backgroundImage}
                                                alt=""
                                                style={{ maxWidth: '100%', height: 'auto' }}
                                            />
                                            <Button
                                                isDestructive
                                                onClick={() => setAttributes({ backgroundImage: '' })}
                                                style={{ marginTop: '10px' }}
                                            >
                                                {__('Remove Image', 'gen-blocks')}
                                            </Button>
                                        </div>
                                    ) : (
                                        <Button variant="secondary" onClick={open}>
                                            {__('Select Background Image', 'gen-blocks')}
                                        </Button>
                                    )}
                                </div>
                            )}
                        />
                    </MediaUploadCheck>

                    {backgroundImage && (
                        <RangeControl
                            label={__('Overlay Opacity', 'gen-blocks')}
                            value={overlayOpacity}
                            onChange={(value) => setAttributes({ overlayOpacity: value })}
                            min={0}
                            max={100}
                            step={5}
                        />
                    )}
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
                            {__('Text Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={textColor}
                            onChange={(color) => setAttributes({ textColor: color })}
                            enableAlpha
                        />
                    </div>
                </PanelBody>

                <PanelBody title={__('Buttons', 'gen-blocks')} initialOpen={false}>
                    <ToggleControl
                        label={__('Show Secondary Button', 'gen-blocks')}
                        checked={showSecondaryButton}
                        onChange={(value) => setAttributes({ showSecondaryButton: value })}
                    />

                    <div style={{ marginTop: '20px' }}>
                        <p style={{ fontWeight: '600', marginBottom: '10px' }}>
                            {__('Primary Button', 'gen-blocks')}
                        </p>
                        <URLInput
                            label={__('URL', 'gen-blocks')}
                            value={primaryButtonUrl}
                            onChange={(url) => setAttributes({ primaryButtonUrl: url })}
                        />
                        <div style={{ marginTop: '10px' }}>
                            <p style={{ marginBottom: '8px' }}>{__('Background', 'gen-blocks')}</p>
                            <ColorPicker
                                color={primaryButtonColor}
                                onChange={(color) => setAttributes({ primaryButtonColor: color })}
                            />
                        </div>
                        <div style={{ marginTop: '10px' }}>
                            <p style={{ marginBottom: '8px' }}>{__('Text Color', 'gen-blocks')}</p>
                            <ColorPicker
                                color={primaryButtonTextColor}
                                onChange={(color) => setAttributes({ primaryButtonTextColor: color })}
                            />
                        </div>
                    </div>

                    {showSecondaryButton && (
                        <div style={{ marginTop: '20px' }}>
                            <p style={{ fontWeight: '600', marginBottom: '10px' }}>
                                {__('Secondary Button', 'gen-blocks')}
                            </p>
                            <URLInput
                                label={__('URL', 'gen-blocks')}
                                value={secondaryButtonUrl}
                                onChange={(url) => setAttributes({ secondaryButtonUrl: url })}
                            />
                            <div style={{ marginTop: '10px' }}>
                                <p style={{ marginBottom: '8px' }}>{__('Background', 'gen-blocks')}</p>
                                <ColorPicker
                                    color={secondaryButtonColor}
                                    onChange={(color) => setAttributes({ secondaryButtonColor: color })}
                                />
                            </div>
                            <div style={{ marginTop: '10px' }}>
                                <p style={{ marginBottom: '8px' }}>{__('Text Color', 'gen-blocks')}</p>
                                <ColorPicker
                                    color={secondaryButtonTextColor}
                                    onChange={(color) =>
                                        setAttributes({ secondaryButtonTextColor: color })
                                    }
                                />
                            </div>
                        </div>
                    )}
                </PanelBody>
            </InspectorControls>

            <BlockControls>
                <AlignmentToolbar
                    value={textAlign}
                    onChange={(align) => setAttributes({ textAlign: align })}
                />
            </BlockControls>

            <div {...blockProps}>
                {backgroundImage && overlayOpacity > 0 && (
                    <div
                        className="wp-block-genblocks-hero__overlay"
                        style={{
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            backgroundColor: `rgba(0, 0, 0, ${overlayOpacity / 100})`,
                            pointerEvents: 'none',
                        }}
                    />
                )}

                <div
                    className="wp-block-genblocks-hero__content"
                    style={{ position: 'relative', zIndex: 1 }}
                >
                    <RichText
                        tagName="h1"
                        className="wp-block-genblocks-hero__heading"
                        placeholder={__('Enter heading...', 'gen-blocks')}
                        value={heading}
                        onChange={(value) => setAttributes({ heading: value })}
                        allowedFormats={['core/bold', 'core/italic']}
                        style={{ color: textColor }}
                    />

                    <RichText
                        tagName="p"
                        className="wp-block-genblocks-hero__subtitle"
                        placeholder={__('Enter subtitle...', 'gen-blocks')}
                        value={subtitle}
                        onChange={(value) => setAttributes({ subtitle: value })}
                        allowedFormats={['core/bold', 'core/italic', 'core/link']}
                        style={{ color: textColor }}
                    />

                    <div className="wp-block-genblocks-hero__buttons">
                        <RichText
                            tagName="span"
                            className="wp-block-genblocks-hero__button wp-block-genblocks-hero__button--primary"
                            placeholder={__('Primary button...', 'gen-blocks')}
                            value={primaryButtonText}
                            onChange={(value) => setAttributes({ primaryButtonText: value })}
                            allowedFormats={[]}
                            style={{
                                backgroundColor: primaryButtonColor,
                                color: primaryButtonTextColor,
                            }}
                        />

                        {showSecondaryButton && (
                            <RichText
                                tagName="span"
                                className="wp-block-genblocks-hero__button wp-block-genblocks-hero__button--secondary"
                                placeholder={__('Secondary button...', 'gen-blocks')}
                                value={secondaryButtonText}
                                onChange={(value) => setAttributes({ secondaryButtonText: value })}
                                allowedFormats={[]}
                                style={{
                                    backgroundColor: secondaryButtonColor,
                                    color: secondaryButtonTextColor,
                                    border: `2px solid ${secondaryButtonTextColor}`,
                                }}
                            />
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
