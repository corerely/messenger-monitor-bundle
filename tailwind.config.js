/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./templates/**/*.twig', './node_modules/flowbite/**/*.js'],
    theme: {
        extend: {},
    },
    plugins: [require('flowbite/plugin')],
};
