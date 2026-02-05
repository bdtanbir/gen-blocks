/**
 * Features Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ColorPicker,
    Button,
    TextControl,
    TextareaControl,
    SelectControl,
    __experimentalHStack as HStack,
    Icon,
} from '@wordpress/components';
import { plus, trash } from '@wordpress/icons';

const ICON_OPTIONS = [
    { label: 'Star', value: 'star-filled' },
    { label: 'Lightbulb', value: 'lightbulb' },
    { label: 'Chart', value: 'chart-line' },
    { label: 'Shield', value: 'shield' },
    { label: 'Admin Users', value: 'admin-users' },
    { label: 'Admin Site', value: 'admin-site' },
    { label: 'Dashboard', value: 'dashboard' },
    { label: 'Heart', value: 'heart' },
    { label: 'Flag', value: 'flag' },
    { label: 'Calendar', value: 'calendar' },
    { label: 'Clock', value: 'clock' },
    { label: 'Email', value: 'email' },
    { label: 'Phone', value: 'phone' },
    { label: 'Location', value: 'location' },
    { label: 'Cart', value: 'cart' },
    { label: 'Money', value: 'money-alt' },
    { label: 'Award', value: 'awards' },
    { label: 'Megaphone', value: 'megaphone' },
    { label: 'Performance', value: 'performance' },
    { label: 'Visibility', value: 'visibility' },
];

/**
 * Edit component for Features block
 */
export default function Edit({ attributes, setAttributes }) {
    const {
        columns,
        features,
        iconColor,
        iconBackgroundColor,
        titleColor,
        descriptionColor,
        backgroundColor,
        iconSize,
        gap,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-features',
        style: {
            backgroundColor,
        },
    });

    const updateFeature = (index, key, value) => {
        const newFeatures = [...features];
        newFeatures[index] = { ...newFeatures[index], [key]: value };
        setAttributes({ features: newFeatures });
    };

    const addFeature = () => {
        const newFeatures = [
            ...features,
            {
                icon: 'star-filled',
                title: 'New Feature',
                description: 'Description of this feature.',
            },
        ];
        setAttributes({ features: newFeatures });
    };

    const removeFeature = (index) => {
        const newFeatures = features.filter((_, i) => i !== index);
        setAttributes({ features: newFeatures });
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Layout', 'gen-blocks')} initialOpen={true}>
                    <RangeControl
                        label={__('Columns', 'gen-blocks')}
                        value={columns}
                        onChange={(value) => setAttributes({ columns: value })}
                        min={2}
                        max={4}
                    />
                    <RangeControl
                        label={__('Gap (px)', 'gen-blocks')}
                        value={gap}
                        onChange={(value) => setAttributes({ gap: value })}
                        min={10}
                        max={60}
                        step={5}
                    />
                    <RangeControl
                        label={__('Icon Size (px)', 'gen-blocks')}
                        value={iconSize}
                        onChange={(value) => setAttributes({ iconSize: value })}
                        min={24}
                        max={96}
                        step={4}
                    />
                </PanelBody>

                <PanelBody title={__('Features', 'gen-blocks')} initialOpen={false}>
                    {features.map((feature, index) => (
                        <div
                            key={index}
                            style={{
                                marginBottom: '20px',
                                padding: '15px',
                                border: '1px solid #ddd',
                                borderRadius: '4px',
                            }}
                        >
                            <HStack alignment="center" style={{ marginBottom: '10px' }}>
                                <span style={{ fontWeight: '600' }}>
                                    {__('Feature', 'gen-blocks')} {index + 1}
                                </span>
                                {features.length > 1 && (
                                    <Button
                                        icon={trash}
                                        isDestructive
                                        onClick={() => removeFeature(index)}
                                        label={__('Remove feature', 'gen-blocks')}
                                    />
                                )}
                            </HStack>

                            <SelectControl
                                label={__('Icon', 'gen-blocks')}
                                value={feature.icon}
                                options={ICON_OPTIONS}
                                onChange={(value) => updateFeature(index, 'icon', value)}
                            />

                            <TextControl
                                label={__('Title', 'gen-blocks')}
                                value={feature.title}
                                onChange={(value) => updateFeature(index, 'title', value)}
                            />

                            <TextareaControl
                                label={__('Description', 'gen-blocks')}
                                value={feature.description}
                                onChange={(value) => updateFeature(index, 'description', value)}
                            />
                        </div>
                    ))}

                    <Button
                        variant="secondary"
                        icon={plus}
                        onClick={addFeature}
                        style={{ width: '100%', justifyContent: 'center' }}
                    >
                        {__('Add Feature', 'gen-blocks')}
                    </Button>
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
                            {__('Icon Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={iconColor}
                            onChange={(color) => setAttributes({ iconColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Icon Background Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={iconBackgroundColor}
                            onChange={(color) => setAttributes({ iconBackgroundColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Title Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={titleColor}
                            onChange={(color) => setAttributes({ titleColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div style={{ marginBottom: '20px' }}>
                        <p style={{ marginBottom: '8px', fontWeight: '500' }}>
                            {__('Description Color', 'gen-blocks')}
                        </p>
                        <ColorPicker
                            color={descriptionColor}
                            onChange={(color) => setAttributes({ descriptionColor: color })}
                            enableAlpha
                        />
                    </div>
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div
                    className="wp-block-genblocks-features__grid"
                    style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${columns}, 1fr)`,
                        gap: `${gap}px`,
                        padding: '40px 20px',
                    }}
                >
                    {features.map((feature, index) => (
                        <div
                            key={index}
                            className="wp-block-genblocks-features__item"
                            style={{
                                textAlign: 'center',
                                padding: '20px',
                            }}
                        >
                            <div
                                className="wp-block-genblocks-features__icon"
                                style={{
                                    width: `${iconSize}px`,
                                    height: `${iconSize}px`,
                                    backgroundColor: iconBackgroundColor,
                                    borderRadius: '50%',
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    marginBottom: '16px',
                                }}
                            >
                                <Icon
                                    icon={feature.icon}
                                    size={iconSize * 0.5}
                                    style={{ fill: iconColor }}
                                />
                            </div>
                            <h3
                                className="wp-block-genblocks-features__title"
                                style={{
                                    color: titleColor,
                                    marginBottom: '8px',
                                    fontSize: '1.25rem',
                                    fontWeight: '600',
                                }}
                            >
                                {feature.title}
                            </h3>
                            <p
                                className="wp-block-genblocks-features__description"
                                style={{
                                    color: descriptionColor,
                                    margin: 0,
                                    lineHeight: 1.6,
                                }}
                            >
                                {feature.description}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
