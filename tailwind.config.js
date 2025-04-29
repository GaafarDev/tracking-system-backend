const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');
const typography = require('@tailwindcss/typography');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    '500': '#EF4444', // Red color instead of indigo
                },
                gray: {
                    '700': '#B91C1C', // Darker red for hover states
                    '800': '#991B1B', // Even darker red for defaults
                    '900': '#7F1D1D', // Darkest red for active states
                },
            },
        },
    },

    plugins: [forms, typography],
};
