export default function useEditMenu(builder) {
    return {
        editingIndex: null,
        editingBlock: null,
        sidebarOpen: false,
        currentSchema: {},

        open(index) {
            this.editingIndex = index;
            this.editingBlock = JSON.parse(JSON.stringify(builder.blocks[index]));

            const schema = builder.availableBlocks.find(b => b.type === this.editingBlock.type);
            this.currentSchema = schema?.settings_schema || {};
            this.sidebarOpen = true;
        },

        close() {
            this.sidebarOpen = false;
        },

        apply() {
            if (this.editingBlock && this.editingIndex !== null) {
                builder.blocks[this.editingIndex] = this.editingBlock;
                this.close();
                builder.updatePreview();
            }
        },

        addRepeaterItem(key, fields) {
            const newItem = {};
            for (const fieldKey in fields) {
                newItem[fieldKey] = fields[fieldKey].default ?? '';
            }

            if (!Array.isArray(this.editingBlock.settings[key])) {
                this.editingBlock.settings[key] = [];
            }

            this.editingBlock.settings[key].push(newItem);
        }
    };
}
