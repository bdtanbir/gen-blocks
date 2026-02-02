import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './assets/style.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);

// Global error handler
app.config.errorHandler = (err, instance, info) => {
    console.error('GenBlocks Admin Error:', err, info);
};

// Make WordPress data available globally
app.config.globalProperties.$wp = window.genBlocksAdmin || {};

app.mount('#genblocks-admin-app');
