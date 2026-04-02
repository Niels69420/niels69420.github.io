/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./**/*.{html,js,php}",
        "!./node_modules/**",
    ],
    theme: {
        extend: {
            fontFamily: {
                poppins: ["Poppins", "Helvetica", "Arial", "Lucida", "sans-serif"],
       
            },
            colors: {
                brand: "#1DA1F2",
            },
        },
    },
    plugins: [],
};
