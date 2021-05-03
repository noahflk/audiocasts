module.exports = {
    purge: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                "theme-light-blue": "#F1F4F9",
                "theme-medium-blue": "#E5EFFF",
                "theme-dark-blue": "#1D3557",
                "theme-blue": "#524EF2",
            },
            spacing: {
                "112": "32rem"
            },
            screens: {
                'sm-h': {'raw': '(min-height: 720px)'},
            }
        },
    },
    variants: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio')
    ],
};
