import useContextMenu from './contextMenu.js';
import useEditMenu from './editMenu.js';

export default function pageBuilder() {
    const builder = {};
    builder.contextMenu = useContextMenu(builder);
    builder.editMenu = useEditMenu(builder);

    return {
        ...builder,
        title: '',
        slug: '',
        blocks: [],
        gridVisible: false,
        openShortcuts: false,
        availableBlocks: [],
        filteredCategory: 'all',
        searchQuery: '',

        get filteredBlocks() {
            return this.availableBlocks
                .filter(b => this.filteredCategory === 'all' || b.category === this.filteredCategory)
                .filter(b => b.label.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },


        initBuilder() {
            console.log('[Builder] Initialisation...');
            console.log('[Builder] Blocs disponibles :', window.availableBlocks);
            this.availableBlocks = window.availableBlocks ?? [];
            this.updatePreview();
            this.$nextTick(() => this.initSortable());
        },

        toggleGrid() {
            this.gridVisible = !this.gridVisible;
        },

        toggleShortcuts() {
            this.openShortcuts = !this.openShortcuts;
        },

        initSortable() {
            const preview = document.getElementById('builder-preview');
            if (!preview) return;

            new Sortable(preview, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'opacity-50',
                onEnd: (evt) => {
                    const moved = this.blocks.splice(evt.oldIndex, 1)[0];
                    this.blocks.splice(evt.newIndex, 0, moved);
                    this.updatePreview();
                }
            });
        },

        generateSlug() {
            this.slug = this.title.toLowerCase()
                .normalize('NFD').replace(/\u0300-\u036f/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim().replace(/\s+/g, '-');
        },

        addBlock(type) {
            const schema = this.availableBlocks.find(b => b.type === type)?.settings_schema || {};
            const newBlock = { type, settings: {}, children: [] };
            for (const key in schema) {
                newBlock.settings[key] = schema[key].default ?? '';
            }
            this.blocks.push(newBlock);
            this.updatePreview();
        },

        removeBlock(index) {
            this.blocks.splice(index, 1);
            this.updatePreview();
        },

        serializeBlocks() {
            return JSON.stringify(this.blocks);
        },

        updatePreview() {
            fetch('/admin/builder-preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ layout: this.blocks })
            })
                .then(res => res.text())
                .then(html => {
                    const el = document.getElementById('builder-preview');
                    el.innerHTML = html;

                    [...el.children].forEach((child, index) => {
                        child.addEventListener('dblclick', () => this.editMenu.open(index));
                        child.addEventListener('contextmenu', (e) => {
                            this.contextMenu.show(e, index);
                        });
                    });
                });
        }
    };
}
