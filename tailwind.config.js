import colors from "tailwindcss/colors";
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector',
    variants: {
        extend: {
            opacity: ['disabled'],
            cursor: ['disabled'],
        },
    },

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                teal: colors.teal,
                cyan: colors.cyan,
            },
            fontFamily: {
                sans: ['Inter var'],
            },
        },
    },

    plugins: [forms, typography],
};
