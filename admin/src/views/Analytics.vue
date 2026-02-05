<template>
    <div class="analytics-page">
        <div class="gb-flex gb-items-center gb-justify-between gb-mb-6">
            <h1 class="gb-text-2xl gb-font-bold">Analytics</h1>

            <!-- Period Selector -->
            <div class="gb-flex gb-items-center gb-gap-2">
                <button
                    v-for="period in periods"
                    :key="period.value"
                    @click="changePeriod(period.value)"
                    :class="[
                        'gb-px-3 gb-py-1.5 gb-text-sm gb-rounded-md gb-transition-colors',
                        analyticsStore.currentPeriod === period.value
                            ? 'gb-bg-blue-600 gb-text-white'
                            : 'gb-bg-gray-100 gb-text-gray-700 hover:gb-bg-gray-200'
                    ]"
                >
                    {{ period.label }}
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="analyticsStore.loading" class="gb-flex gb-items-center gb-justify-center gb-py-16">
            <svg class="gb-animate-spin gb-h-8 gb-w-8 gb-text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="gb-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="gb-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <template v-else>
            <!-- Stats Cards -->
            <div class="gb-grid gb-grid-cols-1 md:gb-grid-cols-2 lg:gb-grid-cols-4 gb-gap-4 gb-mb-6">
                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Total Generations</p>
                    <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                        {{ analyticsStore.stats.totalGenerations.toLocaleString() }}
                    </p>
                </div>

                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Tokens Used</p>
                    <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                        {{ analyticsStore.stats.totalTokens.toLocaleString() }}
                    </p>
                </div>

                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Total Cost</p>
                    <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                        {{ analyticsStore.formattedCost }}
                    </p>
                </div>

                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <p class="gb-text-sm gb-text-gray-500 gb-font-medium">Success Rate</p>
                    <p class="gb-text-2xl gb-font-bold gb-text-gray-900 gb-mt-1">
                        {{ analyticsStore.formattedSuccessRate }}
                    </p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="gb-grid gb-grid-cols-1 lg:gb-grid-cols-2 gb-gap-6 gb-mb-6">
                <!-- Usage Over Time (Line Chart) -->
                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Generations Over Time</h2>
                    <div ref="lineChartRef" class="gb-h-64"></div>
                </div>

                <!-- Tokens Usage (Bar Chart) -->
                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Token Usage</h2>
                    <div ref="barChartRef" class="gb-h-64"></div>
                </div>
            </div>

            <!-- Block Types (Pie Chart) -->
            <div class="gb-grid gb-grid-cols-1 lg:gb-grid-cols-2 gb-gap-6">
                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Block Types Distribution</h2>
                    <div ref="pieChartRef" class="gb-h-64"></div>
                </div>

                <!-- Block Types List -->
                <div class="gb-bg-white gb-rounded-lg gb-shadow gb-p-6">
                    <h2 class="gb-text-lg gb-font-semibold gb-mb-4">Block Types Breakdown</h2>

                    <div v-if="analyticsStore.blockTypesList.length === 0" class="gb-py-8 gb-text-center gb-text-gray-500">
                        No block types data available
                    </div>

                    <div v-else class="gb-space-y-3">
                        <div
                            v-for="(item, index) in analyticsStore.blockTypesList"
                            :key="item.name"
                            class="gb-flex gb-items-center gb-gap-3"
                        >
                            <div
                                class="gb-w-3 gb-h-3 gb-rounded-full"
                                :style="{ backgroundColor: getColor(index) }"
                            ></div>
                            <span class="gb-flex-1 gb-text-sm gb-text-gray-700">{{ item.name }}</span>
                            <span class="gb-text-sm gb-font-medium gb-text-gray-900">{{ item.count }}</span>
                            <span class="gb-text-xs gb-text-gray-500">
                                ({{ ((item.count / analyticsStore.stats.totalGenerations) * 100).toFixed(1) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import { useAnalyticsStore } from '../stores/analytics';
import * as echarts from 'echarts/core';
import { LineChart, BarChart, PieChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';

// Register ECharts components
echarts.use([
    LineChart,
    BarChart,
    PieChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    CanvasRenderer,
]);

const analyticsStore = useAnalyticsStore();

const lineChartRef = ref(null);
const barChartRef = ref(null);
const pieChartRef = ref(null);

let lineChart = null;
let barChart = null;
let pieChart = null;

const periods = [
    { value: 'day', label: 'Today' },
    { value: 'week', label: 'Week' },
    { value: 'month', label: 'Month' },
    { value: 'year', label: 'Year' },
];

const colors = [
    '#3b82f6', // blue
    '#10b981', // green
    '#f59e0b', // amber
    '#ef4444', // red
    '#8b5cf6', // purple
    '#ec4899', // pink
    '#06b6d4', // cyan
    '#f97316', // orange
];

function getColor(index) {
    return colors[index % colors.length];
}

onMounted(async () => {
    await analyticsStore.loadStats('month');
    await nextTick();
    initCharts();
});

watch(() => analyticsStore.chartData, () => {
    updateCharts();
}, { deep: true });

watch(() => analyticsStore.blockTypes, () => {
    updatePieChart();
}, { deep: true });

function initCharts() {
    if (lineChartRef.value) {
        lineChart = echarts.init(lineChartRef.value);
        updateLineChart();
    }

    if (barChartRef.value) {
        barChart = echarts.init(barChartRef.value);
        updateBarChart();
    }

    if (pieChartRef.value) {
        pieChart = echarts.init(pieChartRef.value);
        updatePieChart();
    }

    // Handle resize
    window.addEventListener('resize', handleResize);
}

function handleResize() {
    lineChart?.resize();
    barChart?.resize();
    pieChart?.resize();
}

function updateCharts() {
    updateLineChart();
    updateBarChart();
}

function updateLineChart() {
    if (!lineChart) return;

    const data = analyticsStore.chartData;

    lineChart.setOption({
        tooltip: {
            trigger: 'axis',
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true,
        },
        xAxis: {
            type: 'category',
            data: data.labels || [],
            axisLine: { lineStyle: { color: '#e5e7eb' } },
            axisLabel: { color: '#6b7280' },
        },
        yAxis: {
            type: 'value',
            axisLine: { show: false },
            splitLine: { lineStyle: { color: '#e5e7eb' } },
            axisLabel: { color: '#6b7280' },
        },
        series: [
            {
                name: 'Generations',
                type: 'line',
                smooth: true,
                data: data.generations || [],
                itemStyle: { color: '#3b82f6' },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(59, 130, 246, 0.3)' },
                        { offset: 1, color: 'rgba(59, 130, 246, 0)' },
                    ]),
                },
            },
        ],
    });
}

function updateBarChart() {
    if (!barChart) return;

    const data = analyticsStore.chartData;

    barChart.setOption({
        tooltip: {
            trigger: 'axis',
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true,
        },
        xAxis: {
            type: 'category',
            data: data.labels || [],
            axisLine: { lineStyle: { color: '#e5e7eb' } },
            axisLabel: { color: '#6b7280' },
        },
        yAxis: {
            type: 'value',
            axisLine: { show: false },
            splitLine: { lineStyle: { color: '#e5e7eb' } },
            axisLabel: { color: '#6b7280' },
        },
        series: [
            {
                name: 'Tokens',
                type: 'bar',
                data: data.tokens || [],
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#10b981' },
                        { offset: 1, color: '#059669' },
                    ]),
                    borderRadius: [4, 4, 0, 0],
                },
            },
        ],
    });
}

function updatePieChart() {
    if (!pieChart) return;

    const types = Object.entries(analyticsStore.blockTypes);

    if (types.length === 0) {
        pieChart.setOption({
            title: {
                text: 'No data',
                left: 'center',
                top: 'center',
                textStyle: { color: '#9ca3af', fontSize: 14 },
            },
            series: [],
        });
        return;
    }

    pieChart.setOption({
        title: null,
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c} ({d}%)',
        },
        series: [
            {
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2,
                },
                label: {
                    show: false,
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 14,
                        fontWeight: 'bold',
                    },
                },
                data: types.map(([name, value], index) => ({
                    value,
                    name,
                    itemStyle: { color: getColor(index) },
                })),
            },
        ],
    });
}

async function changePeriod(period) {
    await analyticsStore.setPeriod(period);
}

// Cleanup
import { onUnmounted } from 'vue';
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    lineChart?.dispose();
    barChart?.dispose();
    pieChart?.dispose();
});
</script>
