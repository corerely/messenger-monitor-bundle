/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./templates/**/*.twig', './node_modules/flowbite/**/*.js'],
    theme: {
        extend: {},
    },
    plugins: [require('flowbite/plugin')],

    safelist: [
        'text-green-800',
        'rounded-lg bg-green-50',
        'dark:bg-gray-800',
        'dark:text-green-400',
        'border border-green-300',
        'text-red-800',
        'rounded-lg bg-red-50',
        'dark:bg-gray-800',
        'dark:text-red-400',
        'border border-red-300',
    ],
};
