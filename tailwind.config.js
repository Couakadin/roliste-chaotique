const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    darkMode: ['class', '[data-theme="dark"]'],
    theme  : {
        extend: {
            fontFamily   : {
                'merienda': ['Merienda One', ...defaultTheme.fontFamily.sans]
            },
            colors       : {
                'light'       : '#EEEBE2', // White
                'light-shade' : '#D9D6CE',// Grey
                'light-accent': '#90857A', // Bronze
                'light-brand' : '#FFCE6B', // Yellow
                'dark-brand'  : '#573339', // Red
                'dark-accent' : '#333B30', // Green
                'dark-shade'  : '#50392B', // Brown
                'dark'        : '#241F1E' // Black
            },
            listStyleType: {
                'circle': 'circle'
            }
        },
    },
    plugins: [
        require('tw-elements/dist/plugin')
    ]
}
