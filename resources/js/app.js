import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.loadTinyMCE = (() => {
    let promise = null;
    return (src = '/vendor/tinymce/tinymce.min.js') => {
        if (promise) return promise;
        promise = new Promise((resolve, reject) => {
            if (window.tinymce && typeof window.tinymce.init === 'function') {
                resolve(window.tinymce);
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = () => {
                if (window.tinymce && typeof window.tinymce.init === 'function') {
                    resolve(window.tinymce);
                } else {
                    setTimeout(() => {
                        if (window.tinymce && typeof window.tinymce.init === 'function') {
                            resolve(window.tinymce);
                        } else {
                            reject(new Error('TinyMCE chargé mais window.tinymce non trouvé'));
                        }
                    }, 50);
                }
            };
            script.onerror = (e) => reject(new Error('Erreur chargement TinyMCE: ' + e.message));
            document.head.appendChild(script);
        });
        return promise;
    };
})();

window.tinyLoadAndInit = async (config = {}) => {
    try {
        const tinymce = await window.loadTinyMCE();
        const selector = config.selector || 'textarea.tinymce';
        if (/^#/.test(selector)) {
            const id = selector.replace(/^#/, '');
            const old = tinymce.get(id);
            if (old) old.remove();
        } else {
            document.querySelectorAll(selector).forEach(el => {
                const ed = tinymce.get(el.id || el.getAttribute('id') || el.name);
                if (ed) ed.remove();
            });
        }

        const defaultConfig = {
            selector: 'textarea.tinymce',
            height: 400,
            menubar: false,
            plugins: 'link lists code fullscreen table',
            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link table | code fullscreen',
            branding: false,
            base_url: '/vendor/tinymce',
            suffix: '.min',
        };

        tinymce.init(Object.assign({}, defaultConfig, config));
        return tinymce;
    } catch (err) {
        console.error('Impossible de charger TinyMCE:', err);
        throw err;
    }
};

window.tinyRemove = (selector = 'textarea.tinymce') => {
    if (!window.tinymce) return;
    if (/^#/.test(selector)) {
        const id = selector.replace(/^#/, '');
        const ed = window.tinymce.get(id);
        if (ed) ed.remove();
        return;
    }
    document.querySelectorAll(selector).forEach(el => {
        const id = el.id || el.getAttribute('id') || el.name;
        const ed = window.tinymce.get(id);
        if (ed) ed.remove();
    });
};
