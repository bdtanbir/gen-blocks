/** @type {import('tailwindcss').Config} */
const path = require('path');

module.exports = {
    content: [
        path.join(__dirname, 'src/**/*.{vue,js,ts,jsx,tsx}'),
    ],
    theme: {
        extend: {
            colors: {
                'wp-primary': '#0073aa',
                'wp-primary-dark': '#005a87',
                'wp-sidebar': '#23282d',
                'wp-sidebar-hover': '#32373c',
            },
        },
    },
    plugins: [],
    // Prevent conflicts with WordPress admin styles
    corePlugins: {
        preflight: false,
    },
    prefix: 'gb-',
};
