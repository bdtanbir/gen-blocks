/**
 * AI Sidebar Plugin
 *
 * Adds a sidebar panel to the Gutenberg editor for AI block generation.
 */

import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import {
    PanelBody,
    TextareaControl,
    Button,
    Spinner,
    Notice,
    SelectControl,
    __experimentalDivider as Divider,
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

import { convertToBlock, parseAIResponse } from '../utils/block-converter';

/**
 * Sidebar icon
 */
const SidebarIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
        <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6A4.997 4.997 0 017 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-.8 3.16-2.15 4.1z" />
    </svg>
);

/**
 * Template options
 */
const TEMPLATE_OPTIONS = [
    { value: '', label: __('Select a template...', 'gen-blocks') },
    { value: 'cta-simple', label: __('Simple CTA', 'gen-blocks') },
    { value: 'cta-with-image', label: __('CTA with Image', 'gen-blocks') },
    { value: 'hero-centered', label: __('Centered Hero', 'gen-blocks') },
    { value: 'hero-split', label: __('Split Hero', 'gen-blocks') },
    { value: 'features-grid', label: __('Features Grid', 'gen-blocks') },
    { value: 'testimonial-card', label: __('Testimonial Card', 'gen-blocks') },
];

/**
 * AI Sidebar Component
 */
const AISidebar = () => {
    const [prompt, setPrompt] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);
    const [selectedTemplate, setSelectedTemplate] = useState('');
    const [history, setHistory] = useState([]);

    const { insertBlocks } = useDispatch('core/block-editor');

    // Get the selected block to insert after
    const selectedBlockClientId = useSelect((select) => {
        return select('core/block-editor').getSelectedBlockClientId();
    }, []);

    // Load history from localStorage
    useEffect(() => {
        const savedHistory = localStorage.getItem('genblocks_history');
        if (savedHistory) {
            try {
                setHistory(JSON.parse(savedHistory).slice(0, 10));
            } catch (e) {
                console.error('Failed to load history:', e);
            }
        }
    }, []);

    /**
     * Save to history
     */
    const saveToHistory = (promptText) => {
        const newHistory = [
            { prompt: promptText, timestamp: Date.now() },
            ...history.filter((h) => h.prompt !== promptText),
        ].slice(0, 10);

        setHistory(newHistory);
        localStorage.setItem('genblocks_history', JSON.stringify(newHistory));
    };

    /**
     * Handle AI generation
     */
    const handleGenerate = async () => {
        if (!prompt.trim()) {
            setError(__('Please enter a prompt.', 'gen-blocks'));
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

            if (response.success && response.block) {
                const blockData = parseAIResponse(response);
                const block = convertToBlock(blockData);

                if (block) {
                    insertBlocks(block, undefined, selectedBlockClientId || undefined);
                    saveToHistory(prompt.trim());
                    setSuccess(__('Block generated and inserted!', 'gen-blocks'));
                    setPrompt('');

                    setTimeout(() => setSuccess(null), 3000);
                } else {
                    setError(__('Failed to create block from AI response.', 'gen-blocks'));
                }
            } else {
                setError(response.message || __('Generation failed.', 'gen-blocks'));
            }
        } catch (err) {
            console.error('AI Generation Error:', err);
            setError(err.message || __('An error occurred.', 'gen-blocks'));
        } finally {
            setLoading(false);
        }
    };

    /**
     * Handle template selection
     */
    const handleTemplateSelect = async (templateId) => {
        if (!templateId) return;

        setSelectedTemplate(templateId);
        setLoading(true);
        setError(null);

        try {
            const response = await apiFetch({
                path: `/genblocks/v1/templates/${templateId}/apply`,
                method: 'POST',
                data: {},
            });

            if (response.success && response.block) {
                const block = convertToBlock(response.block);

                if (block) {
                    insertBlocks(block, undefined, selectedBlockClientId || undefined);
                    setSuccess(__('Template applied!', 'gen-blocks'));
                    setSelectedTemplate('');

                    setTimeout(() => setSuccess(null), 3000);
                }
            } else {
                setError(response.message || __('Failed to apply template.', 'gen-blocks'));
            }
        } catch (err) {
            setError(err.message || __('An error occurred.', 'gen-blocks'));
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <PluginSidebarMoreMenuItem target="genblocks-ai-sidebar" icon={<SidebarIcon />}>
                {__('GenBlocks AI', 'gen-blocks')}
            </PluginSidebarMoreMenuItem>

            <PluginSidebar
                name="genblocks-ai-sidebar"
                title={__('GenBlocks AI', 'gen-blocks')}
                icon={<SidebarIcon />}
            >
                <PanelBody title={__('Generate with AI', 'gen-blocks')} initialOpen={true}>
                    {error && (
                        <Notice status="error" isDismissible onDismiss={() => setError(null)}>
                            {error}
                        </Notice>
                    )}

                    {success && (
                        <Notice status="success" isDismissible onDismiss={() => setSuccess(null)}>
                            {success}
                        </Notice>
                    )}

                    <TextareaControl
                        label={__('Describe your block', 'gen-blocks')}
                        value={prompt}
                        onChange={setPrompt}
                        placeholder={__(
                            'e.g., Create a hero section with heading, description, and button',
                            'gen-blocks'
                        )}
                        rows={4}
                        disabled={loading}
                    />

                    <Button
                        variant="primary"
                        onClick={handleGenerate}
                        disabled={loading || !prompt.trim()}
                        isBusy={loading}
                        style={{ width: '100%', justifyContent: 'center' }}
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
                </PanelBody>

                <PanelBody title={__('Quick Templates', 'gen-blocks')} initialOpen={false}>
                    <SelectControl
                        label={__('Choose a template', 'gen-blocks')}
                        value={selectedTemplate}
                        options={TEMPLATE_OPTIONS}
                        onChange={handleTemplateSelect}
                        disabled={loading}
                    />
                    <p style={{ fontSize: '12px', color: '#757575', marginTop: '8px' }}>
                        {__('Templates are pre-built block structures you can customize.', 'gen-blocks')}
                    </p>
                </PanelBody>

                <PanelBody title={__('Recent Prompts', 'gen-blocks')} initialOpen={false}>
                    {history.length === 0 ? (
                        <p style={{ color: '#757575', fontSize: '13px' }}>
                            {__('No recent prompts yet.', 'gen-blocks')}
                        </p>
                    ) : (
                        <ul className="genblocks-history-list">
                            {history.map((item, index) => (
                                <li key={index}>
                                    <button
                                        type="button"
                                        onClick={() => setPrompt(item.prompt)}
                                        disabled={loading}
                                        style={{
                                            background: 'none',
                                            border: 'none',
                                            padding: '8px 0',
                                            textAlign: 'left',
                                            cursor: 'pointer',
                                            width: '100%',
                                            fontSize: '13px',
                                            color: '#1e1e1e',
                                        }}
                                    >
                                        {item.prompt.length > 50
                                            ? item.prompt.substring(0, 50) + '...'
                                            : item.prompt}
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}
                </PanelBody>
            </PluginSidebar>
        </>
    );
};

/**
 * Register the plugin
 */
registerPlugin('genblocks-ai-sidebar', {
    render: AISidebar,
    icon: <SidebarIcon />,
});
