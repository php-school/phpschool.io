/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./templates/**/*.{phtml,js}', './public/js/cloud.js', './src/PhpWorkshop/**/*.php', './assets/**/*.js', './assets/components/**/*.vue'],
    theme: {
        extend: {
            fontFamily: {
                'open-sans': ['Open Sans, sans-serif'],
                'work-sans': ['Work Sans, sans-serif'],
                'orbitron': ['Orbitron, sans-serif'],
            },
            boxShadow: {
                'brand-shadow': '-5px 5px 0px rgba(0, 0, 0, 0.25)',
            },
            backgroundImage: {
                'nav-pattern': "url('/img/svg/header-bg.svg')",
            },
        },
    },
    plugins: [require('tailwind-scrollbar')({ nocompatible: true })],
    variants: {
        scrollbar: ['rounded'],
    },
};
