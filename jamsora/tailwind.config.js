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
                jamsora: {
                    ink: '#0a0a0a',
                    charcoal: '#141414',
                    paper: '#fafaf9',
                    cream: '#f5f4f2',
                    muted: '#6b6b6b',
                    subtle: '#9a9a9a',
                    border: '#e8e8e6',
                    champagne: '#c5a572',
                    'champagne-dark': '#a68b5b',
                },
            },
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                logo: ['Montserrat', ...defaultTheme.fontFamily.sans],
                display: ['Cormorant Garamond', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            letterSpacing: {
                logo: '0.35em',
            },
            boxShadow: {
                soft: '0 4px 24px -4px rgba(10, 10, 10, 0.08)',
                card: '0 1px 3px rgba(10, 10, 10, 0.06), 0 8px 24px -8px rgba(10, 10, 10, 0.1)',
            },
        },
    },

    plugins: [forms],
};
