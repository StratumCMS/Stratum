export default function useContextMenu(builder) {
    const contextMenu = {
        visible: false,
        x: 0,
        y: 0,
        index: null,
        clipboard: null,

        show(e, index) {
            e.preventDefault();
            this.index = index;
            this.x = e.pageX;
            this.y = e.pageY;
            this.visible = true;

            // Attacher les écouteurs globaux une seule fois
            document.addEventListener('click', this.outsideHandler);
            document.addEventListener('keydown', this.escapeHandler);
        },

        hide() {
            this.visible = false;
            this.index = null;

            // Nettoyage des écouteurs
            document.removeEventListener('click', this.outsideHandler);
            document.removeEventListener('keydown', this.escapeHandler);
        },

        outsideHandler(e) {
            const menu = document.getElementById('context-menu');
            if (menu && !menu.contains(e.target)) {
                contextMenu.hide();
            }
        },

        edit() {
            if (this.index !== null) {
                builder.editMenu.open(this.index);
                this.hide();
            }
        },

        remove() {
            if (this.index !== null) {
                builder.removeBlock(this.index);
                this.hide();
            }
        },

        copy() {
            if (this.index !== null) {
                this.clipboard = JSON.parse(JSON.stringify(builder.blocks[this.index]));
                this.hide();
            }
        },

        paste() {
            if (this.clipboard !== null && this.index !== null) {
                builder.blocks.splice(this.index + 1, 0, JSON.parse(JSON.stringify(this.clipboard)));
                builder.updatePreview();
                this.hide();
            }
        }
    };

    return contextMenu;
}
