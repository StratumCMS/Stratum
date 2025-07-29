<div
    id="context-menu"
    x-show="contextMenu.visible"
    x-transition
    :style="{ top: contextMenu.y + 'px', left: contextMenu.x + 'px' }"
    class="fixed z-50 bg-white dark:bg-muted border shadow-md rounded-md w-40 text-sm"
    @click.outside="contextMenu.hide()"
>
    <ul class="divide-y divide-border">
        <li>
            <button class="w-full text-left px-4 py-2 hover:bg-accent"
                    @click="contextMenu.edit()">
                ✏️ Modifier
            </button>
        </li>
        <li>
            <button class="w-full text-left px-4 py-2 hover:bg-accent"
                    @click="contextMenu.remove()">
                🗑️ Supprimer
            </button>
        </li>
        <li>
            <button class="w-full text-left px-4 py-2 hover:bg-accent"
                    @click="contextMenu.copy()">
                📋 Copier
            </button>
        </li>
        <li>
            <button class="w-full text-left px-4 py-2 hover:bg-accent"
                    @click="contextMenu.paste()">
                📥 Coller
            </button>
        </li>
    </ul>
</div>
