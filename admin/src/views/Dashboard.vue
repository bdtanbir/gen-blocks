<template>
    <div class="dashboard-page">
        <h1 class="gb-text-2xl gb-font-bold gb-mb-6">Dashboard</h1>

        <!-- API Configuration Notice -->
        <div
            v-if="!settingsStore.isConfigured"
            class="gb-bg-yellow-50 gb-border gb-border-yellow-200 gb-rounded-lg gb-p-4 gb-mb-6"
        >
            <div class="gb-flex gb-items-start gb-gap-3">
                <svg
                    class="gb-w-5 gb-h-5 gb-text-yellow-600 gb-mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                <div>
                    <h3 class="gb-font-semibold gb-text-yellow-800">API Key Required</h3>
                    <p class="gb-text-sm gb-text-yellow-700 gb-mt-1">
                        Configure your API key in
                        <router-link
                            to="/settings"
                            class="gb-underline gb-font-medium"
                        >Settings</router-link>
                        to start generating AI-powered blocks.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="gb-grid gb-grid-cols-1 md:gb-grid-cols-2 lg:gb-grid-cols-4 gb-gap-4 gb-mb-6">
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <div class="gb-flex gb-items-center gb-justify-between">
                    <div>
                        <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Total Generations</p>
                        <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                            {{ analyticsStore.stats.totalGenerations.toLocaleString() }}
                        </p>
                    </div>
                    <div class="gb-bg-blue-100 gb-rounded-full gb-p-3">
                        <svg class="gb-w-6 gb-h-6 gb-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <div class="gb-flex gb-items-center gb-justify-between">
                    <div>
                        <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Tokens Used</p>
                        <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                            {{ analyticsStore.stats.totalTokens.toLocaleString() }}
                        </p>
                    </div>
                    <div class="gb-bg-green-100 gb-rounded-full gb-p-3">
                        <svg class="gb-w-6 gb-h-6 gb-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <div class="gb-flex gb-items-center gb-justify-between">
                    <div>
                        <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Total Cost</p>
                        <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                            {{ analyticsStore.formattedCost }}
                        </p>
                    </div>
                    <div class="gb-bg-purple-100 gb-rounded-full gb-p-3">
                        <svg class="gb-w-6 gb-h-6 gb-text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <div class="gb-flex gb-items-center gb-justify-between">
                    <div>
                        <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Success Rate</p>
                        <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                            {{ analyticsStore.formattedSuccessRate }}
                        </p>
                    </div>
                    <div class="gb-bg-yellow-100 gb-rounded-full gb-p-3">
                        <svg class="gb-w-6 gb-h-6 gb-text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="gb-grid gb-grid-cols-1 lg:gb-grid-cols-2 gb-gap-6">
            <!-- Quick Actions -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Quick Actions</h2>
                <div class="gb-space-y-3">
                    <router-link
                        to="/settings"
                        class="gb-flex gb-items-center gb-gap-3 gb-p-3 gb-rounded-lg gb-border gb-border-gray-200 hover:gb-bg-gray-50 gb-transition-colors"
                    >
                        <div class="gb-bg-blue-100 gb-rounded-lg gb-p-2">
                            <svg class="gb-w-5 gb-h-5 gb-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="gb-font-medium gb-text-gray-900">Configure Settings</p>
                            <p class="gb-text-sm gb-text-gray-500">Set up your API key and preferences</p>
                        </div>
                    </router-link>

                    <router-link
                        to="/templates"
                        class="gb-flex gb-items-center gb-gap-3 gb-p-3 gb-rounded-lg gb-border gb-border-gray-200 hover:gb-bg-gray-50 gb-transition-colors"
                    >
                        <div class="gb-bg-green-100 gb-rounded-lg gb-p-2">
                            <svg class="gb-w-5 gb-h-5 gb-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="gb-font-medium gb-text-gray-900">Browse Templates</p>
                            <p class="gb-text-sm gb-text-gray-500">Explore pre-built block templates</p>
                        </div>
                    </router-link>

                    <router-link
                        to="/analytics"
                        class="gb-flex gb-items-center gb-gap-3 gb-p-3 gb-rounded-lg gb-border gb-border-gray-200 hover:gb-bg-gray-50 gb-transition-colors"
                    >
                        <div class="gb-bg-purple-100 gb-rounded-lg gb-p-2">
                            <svg class="gb-w-5 gb-h-5 gb-text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="gb-font-medium gb-text-gray-900">View Analytics</p>
                            <p class="gb-text-sm gb-text-gray-500">Track usage and performance</p>
                        </div>
                    </router-link>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                <div class="gb-flex gb-items-center gb-justify-between gb-mb-4">
                    <h2 class="gb-text-lg gb-font-semibold">Recent Activity</h2>
                    <router-link to="/history" class="gb-text-sm gb-text-blue-600 hover:gb-underline">
                        View all
                    </router-link>
                </div>

                <div v-if="analyticsStore.loading" class="gb-py-8 gb-text-center gb-text-gray-500">
                    Loading activity...
                </div>

                <div v-else-if="analyticsStore.recentActivity.length === 0" class="gb-py-8 gb-text-center gb-text-gray-500">
                    No recent activity
                </div>

                <div v-else class="gb-space-y-3">
                    <div
                        v-for="(item, index) in analyticsStore.recentActivity.slice(0, 5)"
                        :key="index"
                        class="gb-flex gb-items-center gb-gap-3 gb-p-3 gb-rounded-lg gb-bg-gray-50"
                    >
                        <div
                            :class="[
                                'gb-w-2 gb-h-2 gb-rounded-full',
                                item.status === 'success' ? 'gb-bg-green-500' : 'gb-bg-red-500'
                            ]"
                        ></div>
                        <div class="gb-flex-1 gb-min-w-0">
                            <p class="gb-text-sm gb-font-medium gb-text-gray-900 gb-truncate">
                                {{ item.prompt || 'Block generation' }}
                            </p>
                            <p class="gb-text-xs gb-text-gray-500">
                                {{ item.block_type || 'Unknown' }} &bull; {{ formatRelativeTime(item.created_at) }}
                            </p>
                        </div>
                        <span class="gb-text-xs gb-text-gray-500">
                            {{ item.tokens_used?.toLocaleString() || 0 }} tokens
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useSettingsStore } from '../stores/settings';
import { useAnalyticsStore } from '../stores/analytics';

const settingsStore = useSettingsStore();
const analyticsStore = useAnalyticsStore();

onMounted(async () => {
    await Promise.all([
        settingsStore.loadSettings(),
        analyticsStore.loadStats('month'),
    ]);
});

function formatRelativeTime(dateString) {
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
        return `${diffMins}m ago`;
    } else if (diffHours < 24) {
        return `${diffHours}h ago`;
    } else {
        return `${diffDays}d ago`;
    }
}
</script>
