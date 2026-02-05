import { defineStore } from 'pinia';
import api from '../services/api';

export const useHistoryStore = defineStore('history', {
    state: () => ({
        history: [],
        total: 0,
        page: 1,
        perPage: 20,
        totalPages: 0,
        loading: false,
        error: null,
    }),

    getters: {
        hasHistory: (state) => {
            return state.history.length > 0;
        },

        hasNextPage: (state) => {
            return state.page < state.totalPages;
        },

        hasPreviousPage: (state) => {
            return state.page > 1;
        },

        paginationInfo: (state) => {
            const start = (state.page - 1) * state.perPage + 1;
            const end = Math.min(state.page * state.perPage, state.total);
            return {
                start,
                end,
                total: state.total,
                page: state.page,
                totalPages: state.totalPages,
            };
        },

        formattedHistory: (state) => {
            return state.history.map((item) => ({
                ...item,
                formattedDate: formatDate(item.created_at),
                formattedTokens: formatNumber(item.tokens_used),
                formattedCost: formatCost(item.cost),
                statusClass: getStatusClass(item.status),
            }));
        },
    },

    actions: {
        async loadHistory(page = 1, perPage = 20) {
            this.loading = true;
            this.error = null;
            this.page = page;
            this.perPage = perPage;

            try {
                const response = await api.getHistory(page, perPage);

                if (response.success) {
                    this.history = response.history || [];
                    this.total = response.total || 0;
                    this.totalPages = response.total_pages || 0;
                }
            } catch (error) {
                this.error = error.message || 'Failed to load history';
                console.error('Error loading history:', error);
            } finally {
                this.loading = false;
            }
        },

        async nextPage() {
            if (this.hasNextPage) {
                return this.loadHistory(this.page + 1, this.perPage);
            }
        },

        async previousPage() {
            if (this.hasPreviousPage) {
                return this.loadHistory(this.page - 1, this.perPage);
            }
        },

        async goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                return this.loadHistory(page, this.perPage);
            }
        },

        async setPerPage(perPage) {
            this.perPage = perPage;
            return this.loadHistory(1, perPage);
        },

        async refresh() {
            return this.loadHistory(this.page, this.perPage);
        },
    },
});

// Helper functions

function formatDate(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) {
        return 'Just now';
    } else if (diffMins < 60) {
        return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
    } else if (diffHours < 24) {
        return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    } else if (diffDays < 7) {
        return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }
}

function formatNumber(num) {
    if (!num) return '0';
    return num.toLocaleString();
}

function formatCost(cost) {
    if (!cost) return '$0.0000';
    return `$${parseFloat(cost).toFixed(4)}`;
}

function getStatusClass(status) {
    switch (status) {
        case 'success':
            return 'gb-text-green-600 gb-bg-green-100';
        case 'failed':
            return 'gb-text-red-600 gb-bg-red-100';
        case 'pending':
            return 'gb-text-yellow-600 gb-bg-yellow-100';
        default:
            return 'gb-text-gray-600 gb-bg-gray-100';
    }
}
