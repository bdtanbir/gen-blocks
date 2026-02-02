<template>
    <div id="genblocks-admin" class="gb-flex gb-min-h-screen">
        <aside class="gb-w-60 gb-bg-wp-sidebar gb-text-white gb-p-5">
            <div class="gb-mb-8">
                <h1 class="gb-text-xl gb-font-bold">GenBlocks</h1>
                <p class="gb-text-sm gb-text-gray-400">AI Block Generator</p>
            </div>

            <nav class="gb-space-y-1">
                <router-link
                    v-for="item in navItems"
                    :key="item.path"
                    :to="item.path"
                    class="gb-flex gb-items-center gb-px-3 gb-py-2 gb-rounded gb-text-white gb-no-underline hover:gb-bg-wp-sidebar-hover"
                    active-class="gb-bg-wp-primary"
                >
                    <span :class="['dashicons', item.icon, 'gb-mr-3']"></span>
                    {{ item.label }}
                </router-link>
            </nav>
        </aside>

        <main class="gb-flex-1 gb-bg-gray-100 gb-p-8">
            <router-view />
        </main>
    </div>
</template>

<script setup>
const navItems = [
    { path: '/', label: 'Dashboard', icon: 'dashicons-dashboard' },
    { path: '/settings', label: 'Settings', icon: 'dashicons-admin-generic' },
    { path: '/analytics', label: 'Analytics', icon: 'dashicons-chart-bar' },
    { path: '/templates', label: 'Templates', icon: 'dashicons-layout' },
    { path: '/history', label: 'History', icon: 'dashicons-backup' },
];
</script>

<style>
#genblocks-admin {
    margin-left: -20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
}
</style>
