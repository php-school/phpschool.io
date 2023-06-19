/** @type {import('tailwindcss').Config} */
module.exports = {
  //important: "#page-top",
  content: ["./templates/**/*.{phtml,js}", "./public/js/cloud.js", "./src/PhpWorkshop/**/*.php", "./assets/**/*.js", "./assets/components/**/*.vue"],
  theme: {
    extend: {
      fontFamily: {
        'open-sans': ['"Open Sans"'],
      },
    },
  },
  plugins: [require("tailwind-scrollbar")({ nocompatible: true })],
  variants: {
    scrollbar: ["rounded"],
  },
};
