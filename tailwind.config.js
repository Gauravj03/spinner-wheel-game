import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                    primary: {
                        light: '#ebf8ff', // similar to Tailwind's blue-50
                        DEFAULT: '#3b82f6', // similar to blue-500
                        dark: '#1e40af', // similar to blue-900
                    }
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans], // Optional: use with class "font-inter"
            },
        },
    },

    plugins: [forms],
};
