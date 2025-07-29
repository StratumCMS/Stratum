<div x-show="editMenu.sidebarOpen"
     class="fixed top-0 right-0 w-full sm:w-80 h-full bg-white dark:bg-background border-l shadow-lg z-50 p-4 overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Modifier le bloc</h3>
        <button @click="editMenu.close()" class="text-gray-500 hover:text-black">&times;</button>
    </div>

    <template x-if="editMenu.editingBlock">
        <div class="space-y-4">
            <template x-for="(schema, key) in editMenu.currentSchema" :key="key">
                <div class="space-y-1">
                    <label class="label" x-text="schema.label"></label>

                    <!-- TEXT -->
                    <template x-if="schema.type === 'text'">
                        <input type="text" class="input" x-model="editMenu.editingBlock.settings[key]">
                    </template>

                    <!-- TEXTAREA -->
                    <template x-if="schema.type === 'textarea'">
                        <textarea class="input" rows="3" x-model="editMenu.editingBlock.settings[key]"></textarea>
                    </template>

                    <!-- COLOR -->
                    <template x-if="schema.type === 'color'">
                        <input type="color" class="w-full h-10" x-model="editMenu.editingBlock.settings[key]">
                    </template>

                    <!-- NUMBER -->
                    <template x-if="schema.type === 'number'">
                        <input type="number" class="input" x-model="editMenu.editingBlock.settings[key]">
                    </template>

                    <!-- SELECT -->
                    <template x-if="schema.type === 'select'">
                        <select class="input" x-model="editMenu.editingBlock.settings[key]">
                            <template x-for="opt in schema.options" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                    </template>

                    <!-- BOOLEAN -->
                    <template x-if="schema.type === 'boolean'">
                        <input type="checkbox" class="form-checkbox" x-model="editMenu.editingBlock.settings[key]">
                    </template>

                    <!-- IMAGE -->
                    <template x-if="schema.type === 'image'">
                        <div>
                            <input type="text" class="input mb-2" placeholder="URL de l'image"
                                   x-model="editMenu.editingBlock.settings[key]">
                            <img :src="editMenu.editingBlock.settings[key]" class="w-full rounded border" x-show="editMenu.editingBlock.settings[key]">
                        </div>
                    </template>

                    <!-- REPEATER -->
                    <template x-if="schema.type === 'repeater'">
                        <div class="space-y-2">
                            <template x-for="(item, i) in editMenu.editingBlock.settings[key]" :key="i">
                                <div class="border p-2 rounded bg-muted space-y-2">
                                    <template x-for="(field, fieldKey) in schema.fields" :key="fieldKey">
                                        <div>
                                            <label class="text-sm font-medium" x-text="field.label"></label>
                                            <template x-if="field.type === 'text'">
                                                <input type="text" class="input" x-model="item[fieldKey]">
                                            </template>
                                            <template x-if="field.type === 'color'">
                                                <input type="color" x-model="item[fieldKey]">
                                            </template>
                                        </div>
                                    </template>
                                    <button class="btn-outline w-full" @click.prevent="editMenu.editingBlock.settings[key].splice(i, 1)">Supprimer</button>
                                </div>
                            </template>
                            <button class="btn w-full btn-sm mt-2" @click.prevent="editMenu.addRepeaterItem(key, schema.fields)">Ajouter un élément</button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </template>

    <div class="flex justify-end mt-4 gap-2">
        <button class="btn-outline" @click="editMenu.close()">Annuler</button>
        <button class="btn-primary" @click="editMenu.apply()">Appliquer</button>
    </div>
</div>
