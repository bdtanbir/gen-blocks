/**
 * AI Generator Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import {
    TextareaControl,
    Button,
    Spinner,
    Notice,
    Flex,
    FlexItem,
} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

import { convertToBlock, parseAIResponse } from '../utils/block-converter';

/**
 * Example prompts for users
 */
const EXAMPLE_PROMPTS = [
    __('Create a CTA section with title, description and button', 'gen-blocks'),
    __('Create a hero section with large heading and two buttons', 'gen-blocks'),
    __('Create a 3-column features section with icons', 'gen-blocks'),
    __('Create a testimonial card with quote and author', 'gen-blocks'),
    __('Create a pricing table with 3 tiers', 'gen-blocks'),
];

/**
 * Edit component for AI Generator block
 */
export default function Edit({ attributes, setAttributes, clientId }) {
    const { prompt } = attributes;
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);

    const { replaceBlocks } = useDispatch('core/block-editor');

    /**
     * Handle AI generation
     */
    const handleGenerate = async () => {
        if (!prompt.trim()) {
            setError(__('Please enter a prompt describing the block you want to create.', 'gen-blocks'));
            return;
        }

        setLoading(true);
        setError(null);
        setSuccess(null);

        try {
            const response = await apiFetch({
                path: '/genblocks/v1/generate',
                method: 'POST',
                data: {
                    prompt: prompt.trim(),
                    context: {},
                },
            });

            if (response.success && (response.block || response.serialized)) {
                let blocksToInsert = [];

                // Prefer block JSON: API returns well-formed block/attrs/innerBlocks.
                // The "serialized" string is often malformed (broken JSON in comments), so don't rely on it first.
                if (response.block) {
                    const block = convertToBlock(parseAIResponse(response));
                    if (block) {
                        blocksToInsert = [block];
                    }
                }

                // Fallback: parse serialized only when block JSON is missing (e.g. legacy API)
                if (blocksToInsert.length === 0 && response.serialized && typeof response.serialized === 'string') {
                    try {
                        const parsed = parse(response.serialized.trim());
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            blocksToInsert = parsed;
                        }
                    } catch (parseErr) {
                        console.warn('Parse serialized failed:', parseErr);
                    }
                }

                if (blocksToInsert.length > 0) {
                    // Replace this AI generator block with the generated block(s) in one shot
                    replaceBlocks(clientId, blocksToInsert);
                    setSuccess(__('Block generated successfully!', 'gen-blocks'));
                } else {
                    setError(__('Failed to create block from AI response.', 'gen-blocks'));
                }
            } else {
                setError(response.message || __('Failed to generate block. Please try again.', 'gen-blocks'));
            }
        } catch (err) {
            console.error('AI Generation Error:', err);
            setError(
                err.message || __('An error occurred while generating the block.', 'gen-blocks')
            );
        } finally {
            setLoading(false);
        }
    };

    /**
     * Handle example prompt click
     */
    const handleExampleClick = (examplePrompt) => {
        setAttributes({ prompt: examplePrompt });
        setError(null);
    };

    const blockProps = useBlockProps({
        className: 'wp-block-genblocks-ai-generator',
    });

    return (
        <div {...blockProps}>
            <div className="genblocks-ai-generator__header">
                <span className="genblocks-ai-generator__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6A4.997 4.997 0 017 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-.8 3.16-2.15 4.1z" />
                    </svg>
                </span>
                <span className="genblocks-ai-generator__title">
                    {__('AI Block Generator', 'gen-blocks')}
                </span>
            </div>

            <div className="genblocks-ai-generator__content">
                {error && (
                    <Notice status="error" isDismissible onDismiss={() => setError(null)}>
                        {error}
                    </Notice>
                )}

                {success && (
                    <Notice status="success" isDismissible={false}>
                        {success}
                    </Notice>
                )}

                <TextareaControl
                    label={__('Describe the block you want to create', 'gen-blocks')}
                    placeholder={__(
                        'e.g., Create a hero section with a large heading, subtitle, and call-to-action button',
                        'gen-blocks'
                    )}
                    value={prompt}
                    onChange={(value) => setAttributes({ prompt: value })}
                    disabled={loading}
                    rows={4}
                />

                <Flex justify="flex-start" gap={2}>
                    <FlexItem>
                        <Button
                            variant="primary"
                            onClick={handleGenerate}
                            disabled={loading || !prompt.trim()}
                            isBusy={loading}
                        >
                            {loading ? (
                                <>
                                    <Spinner />
                                    {__('Generating...', 'gen-blocks')}
                                </>
                            ) : (
                                __('Generate Block', 'gen-blocks')
                            )}
                        </Button>
                    </FlexItem>
                    <FlexItem>
                        <Button
                            variant="secondary"
                            onClick={() => {
                                setAttributes({ prompt: '' });
                                setError(null);
                            }}
                            disabled={loading || !prompt}
                        >
                            {__('Clear', 'gen-blocks')}
                        </Button>
                    </FlexItem>
                </Flex>

                <div className="genblocks-ai-generator__examples">
                    <p className="genblocks-ai-generator__examples-title">
                        {__('Try an example:', 'gen-blocks')}
                    </p>
                    <ul className="genblocks-ai-generator__examples-list">
                        {EXAMPLE_PROMPTS.map((example, index) => (
                            <li key={index}>
                                <button
                                    type="button"
                                    onClick={() => handleExampleClick(example)}
                                    disabled={loading}
                                >
                                    {example}
                                </button>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </div>
    );
}
