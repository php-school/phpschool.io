/** @type {import('tailwindcss').Config} */
module.exports = {
    //important: "#page-top",
    content: ['./templates/**/*.{phtml,js}', './public/js/cloud.js', './src/PhpWorkshop/**/*.php', './assets/**/*.js', './assets/components/**/*.vue'],
    theme: {
        extend: {
            fontFamily: {
                'open-sans': ['"Open Sans"'],
                'work-sans': ['Work Sans, sans-serif'],
            },
            boxShadow: {
                'brand-shadow': '4px 4px 0px rgba(68, 39, 52, 0.50)',
            },
        },
    },
    plugins: [require('tailwind-scrollbar')({ nocompatible: true })],
    variants: {
        scrollbar: ['rounded'],
    },
};
