import { defineStore } from 'pinia';
import api from '../services/api';

export const useAnalyticsStore = defineStore('analytics', {
    state: () => ({
        stats: {
            totalGenerations: 0,
            totalTokens: 0,
            totalCost: 0,
            successRate: 0,
            averageTokens: 0,
        },
        chartData: {
            labels: [],
            generations: [],
            tokens: [],
        },
        blockTypes: {},
        recentActivity: [],
        loading: false,
        error: null,
        currentPeriod: 'month',
    }),

    getters: {
        formattedCost: (state) => {
            return `$${state.stats.totalCost.toFixed(4)}`;
        },

        formattedSuccessRate: (state) => {
            return `${state.stats.successRate.toFixed(1)}%`;
        },

        blockTypesList: (state) => {
            return Object.entries(state.blockTypes).map(([name, count]) => ({
                name,
                count,
            })).sort((a, b) => b.count - a.count);
        },

        lineChartData: (state) => {
            return {
                labels: state.chartData.labels || [],
                datasets: [
                    {
                        name: 'Generations',
                        data: state.chartData.generations || [],
                    },
                ],
            };
        },

        barChartData: (state) => {
            return {
                labels: state.chartData.labels || [],
                datasets: [
                    {
                        name: 'Tokens',
                        data: state.chartData.tokens || [],
                    },
                ],
            };
        },

        pieChartData: (state) => {
            const types = Object.entries(state.blockTypes);
            return {
                labels: types.map(([name]) => name),
                data: types.map(([, count]) => count),
            };
        },
    },

    actions: {
        async loadStats(period = 'month') {
            this.loading = true;
            this.error = null;
            this.currentPeriod = period;

            try {
                const response = await api.getUsage(period);

                if (response.success) {
                    if (response.stats) {
                        this.stats = {
                            totalGenerations: response.stats.total_generations || 0,
                            totalTokens: response.stats.total_tokens || 0,
                            totalCost: parseFloat(response.stats.total_cost) || 0,
                            successRate: parseFloat(response.stats.success_rate) || 0,
                            averageTokens: parseFloat(response.stats.average_tokens) || 0,
                        };
                    }

                    if (response.chartData) {
                        this.chartData = {
                            labels: response.chartData.labels || [],
                            generations: response.chartData.generations || [],
                            tokens: response.chartData.tokens || [],
                        };
                    }

                    if (response.blockTypes) {
                        this.blockTypes = response.blockTypes;
                    }

                    if (response.recentActivity) {
                        this.recentActivity = response.recentActivity;
                    }
                }
            } catch (error) {
                this.error = error.message || 'Failed to load analytics';
                console.error('Error loading analytics:', error);
            } finally {
                this.loading = false;
            }
        },

        setPeriod(period) {
            this.currentPeriod = period;
            return this.loadStats(period);
        },
    },
});
