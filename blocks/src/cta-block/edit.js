/**
 * CTA Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls,
    BlockControls,
    AlignmentToolbar,
    URLInput,
} from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ColorPicker,
    __experimentalBoxControl as BoxControl,
} from '@wordpress/components';

/**
 * Edit component for CTA block
 */
export default function Edit({ attributes, setAttributes }) {
    const {
        title,
        description,
        buttonText,
        buttonUrl,
        backgroundColor,
        textColor,
        buttonColor,
        buttonTextColor,
        textAlign,
        padding,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-cta',
        style: {
            backgroundColor,
            color: textColor,
            padding: `${padding}px`,
            textAlign,
        },
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Layout', 'gen-blocks')} initialOpen={true}>
                    <RangeControl
                        label={__('Padding', 'gen-blocks')}
                        value={padding}
                        onChange={(value) => setAttributes({ padding: value })}
                        min={20}
                        max={120}
                        step={5}
                    />
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

                <PanelBody title={__('Button Colors', 'gen-blocks')} initialOpen={false}>
                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Button Background', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={buttonColor}
                            onChange={(color) => setAttributes({ buttonColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Button Text Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={buttonTextColor}
                            onChange={(color) => setAttributes({ buttonTextColor: color })}
                            enableAlpha
                        />
                    </div>
                </PanelBody>

                <PanelBody title={__('Button Link', 'gen-blocks')} initialOpen={false}>
                    <URLInput
                        label={__('Button URL', 'gen-blocks')}
                        value={buttonUrl}
                        onChange={(url) => setAttributes({ buttonUrl: url })}
                    />
                </PanelBody>
            </InspectorControls>

            <BlockControls>
                <AlignmentToolbar
                    value={textAlign}
                    onChange={(align) => setAttributes({ textAlign: align })}
                />
            </BlockControls>

            <div {...blockProps}>
                <RichText
                    tagName="h2"
                    className="wp-block-genblocks-cta__title"
                    placeholder={__('Enter title...', 'gen-blocks')}
                    value={title}
                    onChange={(value) => setAttributes({ title: value })}
                    allowedFormats={['core/bold', 'core/italic']}
                    style={{ color: textColor }}
                />

                <RichText
                    tagName="p"
                    className="wp-block-genblocks-cta__description"
                    placeholder={__('Enter description...', 'gen-blocks')}
                    value={description}
                    onChange={(value) => setAttributes({ description: value })}
                    allowedFormats={['core/bold', 'core/italic', 'core/link']}
                    style={{ color: textColor }}
                />

                <div className="wp-block-genblocks-cta__button-wrapper">
                    <RichText
                        tagName="span"
                        className="wp-block-genblocks-cta__button"
                        placeholder={__('Button text...', 'gen-blocks')}
                        value={buttonText}
                        onChange={(value) => setAttributes({ buttonText: value })}
                        allowedFormats={[]}
                        style={{
                            backgroundColor: buttonColor,
                            color: buttonTextColor,
                        }}
                    />
                </div>
            </div>
        </>
    );
}
