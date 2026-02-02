import { createRouter, createWebHashHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Settings from '../views/Settings.vue';
import Analytics from '../views/Analytics.vue';
import Templates from '../views/Templates.vue';
import History from '../views/History.vue';

const routes = [
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard,
    },
    {
        path: '/settings',
        name: 'Settings',
        component: Settings,
    },
    {
        path: '/analytics',
        name: 'Analytics',
        component: Analytics,
    },
    {
        path: '/templates',
        name: 'Templates',
        component: Templates,
    },
    {
        path: '/history',
        name: 'History',
        component: History,
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
