/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./resources/js/**/*.{vue,js,ts,jsx,tsx}'],
    theme: {
        extend: {},
    },
    important: '.nova-media-field',
    plugins: [
        require('tailwind-scrollbar-hide'),
    ],
    darkMode: 'class'
};
