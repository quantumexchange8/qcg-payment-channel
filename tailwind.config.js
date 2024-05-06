import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                bilbao: {
                    '50': '#efffe2',
                    '100': '#dbffc0',
                    '200': '#b8ff88',
                    '300': '#89ff44',
                    '400': '#5dff0d',
                    '500': '#3cf500',
                    '600': '#2ac500',
                    '700': '#1f9500',
                    '800': '#1c7800',
                    '900': '#196106',
                    '950': '#083700',
                },
                gray: {
                    '50': '#f7f7f7',
                    '100': '#ededed',
                    '200': '#dddddd',
                    '300': '#c8c8c8',
                    '400': '#adadad',
                    '500': '#999999',
                    '600': '#888888',
                    '700': '#7b7b7b',
                    '800': '#676767',
                    '900': '#545454',
                    '950': '#363636',
                },
            },
        },
    },

    plugins: [forms],
};
