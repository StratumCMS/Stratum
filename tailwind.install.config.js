/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/install/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#3b82f6',
                card: '#1e1e2d', // Couleur sombre pour les cartes
                background: '#111827', // Fond principal
                shadowLight: '#2a2a3e', // Ombre claire interne
                shadowDark: '#0b0b12', // Ombre foncée externe
                text: '#d1d5db', // Texte clair
                input: '#292938', // Couleur des champs
                inputBorder: '#3e3e4e', // Bordures des champs
            },
            boxShadow: {
                skeuo: '6px 6px 12px rgba(0, 0, 0, 0.7), -6px -6px 12px rgba(255, 255, 255, 0.1)',
                skeuoInset: 'inset 6px 6px 12px rgba(0, 0, 0, 0.7), inset -6px -6px 12px rgba(255, 255, 255, 0.1)',
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
