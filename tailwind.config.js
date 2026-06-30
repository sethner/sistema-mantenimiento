import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {

            colors: {
                primary: '#2563eb',   // azul sistema
                secondary: '#64748b', // gris elegante
                success: '#16a34a',
                danger: '#dc2626',
                warning: '#f59e0b',
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            boxShadow: {
                soft: '0 2px 8px rgba(0,0,0,0.05)',
            },

            borderRadius: {
                xl: '0.75rem',
            }

        },
    },

    plugins: [],
}