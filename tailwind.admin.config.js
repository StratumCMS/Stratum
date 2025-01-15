/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/admin/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#3b82f6',
                secondary: '#1e293b',
                background: '#0f172a',
                card: 'rgba(255, 255, 255, 0.1)',
                text: '#f8fafc',
                accent: '#14b8a6',
            },
            backdropBlur: {
                sm: '4px',
                md: '8px',
                lg: '12px',
            },
            boxShadow: {
                glass: '0 4px 30px rgba(0, 0, 0, 0.1)',
            },
            borderRadius: {
                card: '16px',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
