import { defineStore } from 'pinia';

export const useAnalyticsStore = defineStore('analytics', {
    state: () => ({
        stats: {},
        chartData: {},
        blockTypes: {},
        recentActivity: [],
        loading: false,
    }),

    actions: {
        async loadStats(period = 'month') {
            // Will be implemented in Phase 5
        },
    },
});
