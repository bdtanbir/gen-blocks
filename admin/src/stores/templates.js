import { defineStore } from 'pinia';
import api from '../services/api';

export const useTemplatesStore = defineStore('templates', {
    state: () => ({
        templates: [],
        categories: [],
        selectedCategory: null,
        selectedTemplate: null,
        loading: false,
        applying: false,
        error: null,
    }),

    getters: {
        filteredTemplates: (state) => {
            if (!state.selectedCategory) {
                return state.templates;
            }
            return state.templates.filter(
                (template) => template.category === state.selectedCategory
            );
        },

        templatesByCategory: (state) => {
            const grouped = {};
            state.templates.forEach((template) => {
                const category = template.category || 'Uncategorized';
                if (!grouped[category]) {
                    grouped[category] = [];
                }
                grouped[category].push(template);
            });
            return grouped;
        },

        categoryCount: (state) => {
            const counts = {};
            state.templates.forEach((template) => {
                const category = template.category || 'Uncategorized';
                counts[category] = (counts[category] || 0) + 1;
            });
            return counts;
        },
    },

    actions: {
        async loadTemplates(category = null) {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.getTemplates(category);

                if (response.success) {
                    this.templates = response.templates || [];
                    if (response.categories) {
                        this.categories = response.categories;
                    }
                }
            } catch (error) {
                this.error = error.message || 'Failed to load templates';
                console.error('Error loading templates:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadTemplate(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.getTemplate(id);

                if (response.success && response.template) {
                    this.selectedTemplate = response.template;
                }
                return response;
            } catch (error) {
                this.error = error.message || 'Failed to load template';
                console.error('Error loading template:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async applyTemplate(id, variables = {}) {
            this.applying = true;
            this.error = null;

            try {
                const response = await api.applyTemplate(id, variables);
                return response;
            } catch (error) {
                this.error = error.message || 'Failed to apply template';
                console.error('Error applying template:', error);
                throw error;
            } finally {
                this.applying = false;
            }
        },

        setCategory(category) {
            this.selectedCategory = category;
        },

        clearCategory() {
            this.selectedCategory = null;
        },

        selectTemplate(template) {
            this.selectedTemplate = template;
        },

        clearSelectedTemplate() {
            this.selectedTemplate = null;
        },
    },
});
