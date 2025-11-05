import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'sepia': {
                    50: '#FBF8F3',
                    100: '#F8F1E9',
                    800: '#70543E',
                    900: '#433426'
                },

                'biblioteca': {
                    50: '#fdf8f3',
                    100: '#f7f0e6',
                    200: '#eeddc9',
                    300: '#e2c5a3',
                    400: '#d2a274',
                    500: '#c08550',
                    600: '#b27046',
                    700: '#945a3c',
                    800: '#774a36',
                    900: '#613e2e',
                }
            }
        },
    },

    plugins: [
        forms,
        typography
    ],
};
