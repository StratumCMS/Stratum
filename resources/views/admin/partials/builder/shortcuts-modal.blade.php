<div x-show="openShortcuts" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-background p-6 rounded shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Raccourcis clavier</h2>
            <button @click="openShortcuts = false">&times;</button>
        </div>
        <ul class="text-sm space-y-2">
            <li><kbd class="kbd">G</kbd> : Grille on/off</li>
            <li><kbd class="kbd">Ctrl</kbd> : Ouvrir les raccourcis</li>
            <li><kbd class="kbd">🖱️ Double clic</kbd> : Modifier un bloc</li>
            <li><kbd class="kbd">🖱️ Clic droit</kbd> : Menu contextuel</li>
        </ul>
    </div>
</div>
