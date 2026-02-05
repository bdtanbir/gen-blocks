<template>
    <div class="history-page">
        <h1 class="gb-text-2xl gb-font-bold gb-mb-6">Generation History</h1>

        <!-- Loading State -->
        <div v-if="historyStore.loading && !historyStore.hasHistory" class="gb-flex gb-items-center gb-justify-center gb-py-16">
            <svg class="gb-animate-spin gb-h-8 gb-w-8 gb-text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Empty State -->
        <div v-else-if="!historyStore.hasHistory" class="gb-bg-white gb-rounded-lg gb-shadow gb-p-12 gb-text-center">
            <svg class="gb-w-16 gb-h-16 gb-mx-auto gb-text-gray-400 gb-mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="gb-text-lg gb-font-semibold gb-text-gray-900 gb-mb-2">No generation history</h3>
            <p class="gb-text-gray-500">
                Start generating blocks to see your history here.
            </p>
        </div>

        <template v-else>
            <!-- History Table -->
            <div class="gb-bg-white gb-rounded-lg gb-shadow gb-overflow-hidden">
                <div class="gb-overflow-x-auto">
                    <table class="gb-min-w-full gb-divide-y gb-divide-gray-200">
                        <thead class="gb-bg-gray-50">
                            <tr>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Prompt
                                </th>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Block Type
                                </th>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Status
                                </th>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Tokens
                                </th>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Cost
                                </th>
                                <th class="gb-px-6 gb-py-3 gb-text-left gb-text-xs gb-font-medium gb-text-gray-500 gb-uppercase gb-tracking-wider">
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="gb-bg-white gb-divide-y gb-divide-gray-200">
                            <tr
                                v-for="item in historyStore.formattedHistory"
                                :key="item.id"
                                class="hover:gb-bg-gray-50"
                            >
                                <td class="gb-px-6 gb-py-4">
                                    <div class="gb-max-w-xs gb-truncate gb-text-sm gb-text-gray-900" :title="item.prompt">
                                        {{ item.prompt || 'N/A' }}
                                    </div>
                                </td>
                                <td class="gb-px-6 gb-py-4">
                                    <span class="gb-text-sm gb-text-gray-600">
                                        {{ item.block_type || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="gb-px-6 gb-py-4">
                                    <span
                                        :class="[
                                            'gb-inline-flex gb-items-center gb-px-2.5 gb-py-0.5 gb-rounded-full gb-text-xs gb-font-medium',
                                            item.statusClass
                                        ]"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="gb-px-6 gb-py-4 gb-text-sm gb-text-gray-600">
                                    {{ item.formattedTokens }}
                                </td>
                                <td class="gb-px-6 gb-py-4 gb-text-sm gb-text-gray-600">
                                    {{ item.formattedCost }}
                                </td>
                                <td class="gb-px-6 gb-py-4 gb-text-sm gb-text-gray-500">
                                    {{ item.formattedDate }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="gb-bg-gray-50 gb-px-6 gb-py-3 gb-flex gb-items-center gb-justify-between gb-border-t gb-border-gray-200">
                    <div class="gb-flex gb-items-center gb-gap-2">
                        <span class="gb-text-sm gb-text-gray-700">
                            Showing
                            <span class="gb-font-medium">{{ historyStore.paginationInfo.start }}</span>
                            to
                            <span class="gb-font-medium">{{ historyStore.paginationInfo.end }}</span>
                            of
                            <span class="gb-font-medium">{{ historyStore.paginationInfo.total }}</span>
                            results
                        </span>
                    </div>

                    <div class="gb-flex gb-items-center gb-gap-2">
                        <!-- Per Page Selector -->
                        <select
                            :value="historyStore.perPage"
                            @change="changePerPage($event.target.value)"
                            class="gb-border gb-border-gray-300 gb-rounded-md gb-px-2 gb-py-1 gb-text-sm focus:gb-ring-2 focus:gb-ring-blue-500 focus:gb-border-blue-500"
                        >
                            <option value="10">10 / page</option>
                            <option value="20">20 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>

                        <!-- Pagination Buttons -->
                        <nav class="gb-flex gb-items-center gb-gap-1">
                            <button
                                @click="historyStore.previousPage()"
                                :disabled="!historyStore.hasPreviousPage || historyStore.loading"
                                class="gb-px-3 gb-py-1 gb-text-sm gb-border gb-border-gray-300 gb-rounded-md disabled:gb-opacity-50 disabled:gb-cursor-not-allowed hover:gb-bg-gray-100"
                            >
                                Previous
                            </button>

                            <!-- Page Numbers -->
                            <template v-for="page in visiblePages" :key="page">
                                <span v-if="page === '...'" class="gb-px-2 gb-text-gray-500">...</span>
                                <button
                                    v-else
                                    @click="historyStore.goToPage(page)"
                                    :class="[
                                        'gb-px-3 gb-py-1 gb-text-sm gb-border gb-rounded-md',
                                        page === historyStore.page
                                            ? 'gb-bg-blue-600 gb-text-white gb-border-blue-600'
                                            : 'gb-border-gray-300 hover:gb-bg-gray-100'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </template>

                            <button
                                @click="historyStore.nextPage()"
                                :disabled="!historyStore.hasNextPage || historyStore.loading"
                                class="gb-px-3 gb-py-1 gb-text-sm gb-border gb-border-gray-300 gb-rounded-md disabled:gb-opacity-50 disabled:gb-cursor-not-allowed hover:gb-bg-gray-100"
                            >
                                Next
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useHistoryStore } from '../stores/history';

const historyStore = useHistoryStore();

onMounted(async () => {
    await historyStore.loadHistory();
});

const visiblePages = computed(() => {
    const current = historyStore.page;
    const total = historyStore.totalPages;
    const pages = [];

    if (total <= 7) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        if (current > 3) {
            pages.push('...');
        }

        // Show pages around current
        const start = Math.max(2, current - 1);
        const end = Math.min(total - 1, current + 1);

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        if (current < total - 2) {
            pages.push('...');
        }

        // Always show last page
        pages.push(total);
    }

    return pages;
});

function changePerPage(value) {
    historyStore.setPerPage(parseInt(value));
}
</script>
