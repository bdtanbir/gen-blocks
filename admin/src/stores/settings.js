import { defineStore } from 'pinia';
import api from '../services/api';

export const useSettingsStore = defineStore('settings', {
    state: () => ({
        settings: {
            api_provider: 'openai',
            api_key: '',
            model: 'gpt-4',
            max_tokens: 2000,
            temperature: 0.7,
            rate_limit: 100,
            cache_enabled: true,
            cache_duration: 3600,
            primary_color: '#0073aa',
            default_alignment: 'center',
        },
        loading: false,
        saving: false,
        error: null,
        connectionStatus: null,
        connectionTesting: false,
        cacheClearing: false,
    }),

    getters: {
        isConfigured: (state) => {
            return !!state.settings.api_key;
        },

        hasApiKey: (state) => {
            return state.settings.api_key && state.settings.api_key.length > 0;
        },

        providerName: (state) => {
            const providers = {
                openai: 'OpenAI',
                anthropic: 'Anthropic',
            };
            return providers[state.settings.api_provider] || state.settings.api_provider;
        },
    },

    actions: {
        async loadSettings() {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.getSettings();
                if (response.success && response.settings) {
                    this.settings = { ...this.settings, ...response.settings };
                }
            } catch (error) {
                this.error = error.message || 'Failed to load settings';
                console.error('Error loading settings:', error);
            } finally {
                this.loading = false;
            }
        },

        async updateSettings(newSettings) {
            this.saving = true;
            this.error = null;

            try {
                const response = await api.updateSettings(newSettings);
                if (response.success && response.settings) {
                    this.settings = { ...this.settings, ...response.settings };
                }
                return response;
            } catch (error) {
                this.error = error.message || 'Failed to save settings';
                console.error('Error saving settings:', error);
                throw error;
            } finally {
                this.saving = false;
            }
        },

        async testApiConnection() {
            this.connectionTesting = true;
            this.connectionStatus = null;

            try {
                const response = await api.testConnection();
                this.connectionStatus = {
                    success: response.success,
                    message: response.message || 'Connection successful!',
                };
                return response;
            } catch (error) {
                this.connectionStatus = {
                    success: false,
                    message: error.message || 'Connection failed',
                };
                throw error;
            } finally {
                this.connectionTesting = false;
            }
        },

        async clearCache() {
            this.cacheClearing = true;

            try {
                const response = await api.clearCache();
                return response;
            } catch (error) {
                console.error('Error clearing cache:', error);
                throw error;
            } finally {
                this.cacheClearing = false;
            }
        },

        resetConnectionStatus() {
            this.connectionStatus = null;
        },
    },
});
