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
                    ink: '#1a1a1a',
                    charcoal: '#2d2d2d',
                    paper: '#ffffff',
                    cream: '#f7f6f3',
                    sand: '#edeae4',
                    muted: '#777777',
                    subtle: '#999999',
                    border: '#e5e2dc',
                    gold: '#b8965c',
                    'gold-light': '#d4b87a',
                    'gold-dark': '#9a7b45',
                },
            },
            fontFamily: {
                sans: ['Jost', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            maxWidth: {
                site: '1320px',
            },
            boxShadow: {
                product: '0 2px 20px rgba(0,0,0,0.06)',
                hover: '0 8px 30px rgba(0,0,0,0.1)',
            },
        },
    },

    plugins: [forms],
};
