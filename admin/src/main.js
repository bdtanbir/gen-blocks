import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './assets/style.css';

// ECharts imports for Analytics page
import * as echarts from 'echarts/core';
import { LineChart, BarChart, PieChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';

// Register ECharts components globally
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

const app = createApp(App);

app.use(createPinia());
app.use(router);

// Make ECharts available globally
app.config.globalProperties.$echarts = echarts;

// Global error handler
app.config.errorHandler = (err, instance, info) => {
    console.error('GenBlocks Admin Error:', err, info);
};

// Make WordPress data available globally
app.config.globalProperties.$wp = window.genBlocksAdmin || {};

app.mount('#genblocks-admin-app');
