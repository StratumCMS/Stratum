@extends('admin.layouts.admin')

@section('title', 'CSS / JS personnalisé')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium rounded-md h-9 px-3 hover-glow-purple border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 border-b">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Modifier le CSS & JS personnalisé
                </h2>
                <p class="text-muted-foreground text-sm">
                    Ces scripts seront injectés automatiquement dans le thème actif.
                </p>
            </div>

            <div class="p-6 pt-4">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition
                         class="rounded-md bg-green-100 text-green-800 px-4 py-3 border border-green-300 shadow-sm mb-6">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition
                         class="rounded-md bg-red-100 text-red-800 px-4 py-3 border border-red-300 shadow-sm mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form x-data="customAssetsForm()" method="POST" action="{{ route('admin.custom-assets.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col sm:flex-row gap-2 border-b pb-2">
                        <button type="button" @click="activeTab = 'css'"
                                :class="activeTab === 'css' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-accent'"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-md font-medium transition-colors">
                            <i class="fab fa-css3-alt mr-2"></i> CSS
                        </button>
                        <button type="button" @click="activeTab = 'js'"
                                :class="activeTab === 'js' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-accent'"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-md font-medium transition-colors">
                            <i class="fab fa-js mr-2"></i> JavaScript
                        </button>
                    </div>

                    <div x-show="activeTab === 'css'" x-transition class="space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="text-sm font-medium">CSS personnalisé</label>
                            <div class="flex gap-2">
                                <button type="button" @click="formatCode('css')"
                                        class="text-xs px-3 py-1 rounded-md bg-muted hover:bg-accent transition-colors">
                                    <i class="fas fa-magic mr-1"></i> Formater
                                </button>
                                <button type="button" @click="showClearModal = true; modalType = 'css'"
                                        class="text-xs px-3 py-1 rounded-md bg-muted hover:bg-destructive hover:text-destructive-foreground transition-colors">
                                    <i class="fas fa-trash mr-1"></i> Vider
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <textarea name="css"
                                      x-model="cssCode"
                                      @input="updateLineNumbers('css')"
                                      @scroll="syncScroll('css')"
                                      class="w-full min-h-[300px] md:min-h-[400px] p-4 pl-14 font-mono text-sm bg-slate-900 text-slate-100 rounded-md border border-slate-700 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-y"
                                      placeholder="/* Votre CSS personnalisé ici */&#10;.example {&#10;    color: #fff;&#10;}"
                                      spellcheck="false">{{ old('css', $css) }}</textarea>
                            <div x-ref="cssLineNumbers"
                                 class="absolute left-0 top-0 w-12 h-full bg-slate-800 text-slate-500 text-sm font-mono rounded-l-md border-r border-slate-700 overflow-hidden pointer-events-none select-none">
                                <div class="p-4 text-right leading-6" x-html="lineNumbers.css"></div>
                            </div>
                        </div>
                        @error('css') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        <p class="text-xs text-muted-foreground">
                            <i class="fas fa-info-circle mr-1"></i> Le CSS sera injecté dans la balise &lt;head&gt; de toutes les pages
                        </p>
                    </div>

                    <div x-show="activeTab === 'js'" x-transition class="space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="text-sm font-medium">JavaScript personnalisé</label>
                            <div class="flex gap-2">
                                <button type="button" @click="formatCode('js')"
                                        class="text-xs px-3 py-1 rounded-md bg-muted hover:bg-accent transition-colors">
                                    <i class="fas fa-magic mr-1"></i> Formater
                                </button>
                                <button type="button" @click="showClearModal = true; modalType = 'js'"
                                        class="text-xs px-3 py-1 rounded-md bg-muted hover:bg-destructive hover:text-destructive-foreground transition-colors">
                                    <i class="fas fa-trash mr-1"></i> Vider
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <textarea name="js"
                                      x-model="jsCode"
                                      @input="updateLineNumbers('js')"
                                      @scroll="syncScroll('js')"
                                      class="w-full min-h-[300px] md:min-h-[400px] p-4 pl-14 font-mono text-sm bg-slate-900 text-slate-100 rounded-md border border-slate-700 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-y"
                                      placeholder="// Votre JavaScript personnalisé ici&#10;console.log('Hello World!');"
                                      spellcheck="false">{{ old('js', $js) }}</textarea>
                            <div x-ref="jsLineNumbers"
                                 class="absolute left-0 top-0 w-12 h-full bg-slate-800 text-slate-500 text-sm font-mono rounded-l-md border-r border-slate-700 overflow-hidden pointer-events-none select-none">
                                <div class="p-4 text-right leading-6" x-html="lineNumbers.js"></div>
                            </div>
                        </div>
                        @error('js') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        <p class="text-xs text-muted-foreground">
                            <i class="fas fa-info-circle mr-1"></i> Le JavaScript sera injecté avant la fermeture de la balise &lt;/body&gt;
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                        <button type="submit"
                                class="flex-1 sm:flex-none bg-primary text-white px-6 py-2.5 rounded-md hover:bg-primary/90 hover-glow-purple font-medium transition-all">
                            <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                        </button>
                        <button type="button"
                                @click="showResetModal = true"
                                class="flex-1 sm:flex-none bg-muted text-muted-foreground px-6 py-2.5 rounded-md hover:bg-accent font-medium transition-all">
                            <i class="fas fa-undo mr-2"></i> Réinitialiser
                        </button>
                    </div>

                    <div x-show="showClearModal"
                         x-cloak
                         @keydown.escape.window="showClearModal = false"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;">
                        <div x-show="showClearModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="showClearModal = false"
                             class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

                        <div class="flex min-h-full items-center justify-center p-4">
                            <div x-show="showClearModal"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="relative bg-card rounded-lg shadow-xl max-w-md w-full border border-border">

                                <div class="p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-destructive/10 flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-destructive"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold">Vider l'éditeur</h3>
                                    </div>

                                    <p class="text-muted-foreground mb-6">
                                        Êtes-vous sûr de vouloir vider l'éditeur <span class="font-semibold" x-text="modalType === 'css' ? 'CSS' : 'JavaScript'"></span> ? Cette action ne peut pas être annulée.
                                    </p>

                                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                                        <button type="button"
                                                @click="showClearModal = false"
                                                class="px-4 py-2 rounded-md bg-muted text-muted-foreground hover:bg-accent transition-colors">
                                            Annuler
                                        </button>
                                        <button type="button"
                                                @click="clearCode(modalType); showClearModal = false"
                                                class="px-4 py-2 rounded-md bg-destructive text-white hover:bg-destructive/90 transition-colors">
                                            <i class="fas fa-trash mr-2"></i> Vider
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="showResetModal"
                         x-cloak
                         @keydown.escape.window="showResetModal = false"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;">
                        <div x-show="showResetModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="showResetModal = false"
                             class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

                        <div class="flex min-h-full items-center justify-center p-4">
                            <div x-show="showResetModal"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="relative bg-card rounded-lg shadow-xl max-w-md w-full border border-border">

                                <div class="p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-500/10 flex items-center justify-center">
                                            <i class="fas fa-undo text-yellow-500"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold">Réinitialiser les modifications</h3>
                                    </div>

                                    <p class="text-muted-foreground mb-6">
                                        Êtes-vous sûr de vouloir réinitialiser tous les éditeurs aux valeurs originales ? Toutes vos modifications non sauvegardées seront perdues.
                                    </p>

                                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                                        <button type="button"
                                                @click="showResetModal = false"
                                                class="px-4 py-2 rounded-md bg-muted text-muted-foreground hover:bg-accent transition-colors">
                                            Annuler
                                        </button>
                                        <button type="button"
                                                @click="resetToOriginal(); showResetModal = false"
                                                class="px-4 py-2 rounded-md bg-yellow-500 text-white hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-undo mr-2"></i> Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
            <h3 class="font-semibold mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb text-yellow-500"></i> Conseils d'utilisation
            </h3>
            <ul class="space-y-2 text-sm text-muted-foreground">
                <li class="flex gap-2">
                    <i class="fas fa-check text-green-500 mt-1"></i>
                    <span>Utilisez des commentaires pour organiser votre code</span>
                </li>
                <li class="flex gap-2">
                    <i class="fas fa-check text-green-500 mt-1"></i>
                    <span>Testez vos modifications dans un environnement de développement d'abord</span>
                </li>
                <li class="flex gap-2">
                    <i class="fas fa-check text-green-500 mt-1"></i>
                    <span>Évitez les modifications trop lourdes qui pourraient ralentir votre site</span>
                </li>
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function customAssetsForm() {
            return {
                activeTab: 'css',
                cssCode: `{{ old('css', addslashes($css ?? '')) }}`.replace(/\\n/g, '\n').replace(/\\'/g, "'").replace(/\\"/g, '"'),
                jsCode: `{{ old('js', addslashes($js ?? '')) }}`.replace(/\\n/g, '\n').replace(/\\'/g, "'").replace(/\\"/g, '"'),
                originalCss: `{{ old('css', addslashes($css ?? '')) }}`.replace(/\\n/g, '\n').replace(/\\'/g, "'").replace(/\\"/g, '"'),
                originalJs: `{{ old('js', addslashes($js ?? '')) }}`.replace(/\\n/g, '\n').replace(/\\'/g, "'").replace(/\\"/g, '"'),
                lineNumbers: {
                    css: '',
                    js: ''
                },
                showClearModal: false,
                showResetModal: false,
                modalType: '',

                init() {
                    this.$nextTick(() => {
                        this.updateLineNumbers('css');
                        this.updateLineNumbers('js');
                    });
                },

                updateLineNumbers(type) {
                    const code = type === 'css' ? this.cssCode : this.jsCode;
                    const lines = code.split('\n').length;
                    this.lineNumbers[type] = Array.from({ length: lines }, (_, i) => i + 1).join('\n');
                },

                syncScroll(type) {
                    const textarea = event.target;
                    const lineNumbers = this.$refs[`${type}LineNumbers`];
                    if (lineNumbers) {
                        lineNumbers.scrollTop = textarea.scrollTop;
                    }
                },

                formatCode(type) {
                    if (type === 'css') {
                        this.cssCode = this.formatCSS(this.cssCode);
                    } else {
                        this.jsCode = this.formatJS(this.jsCode);
                    }
                    this.updateLineNumbers(type);
                },

                formatCSS(css) {
                    return css
                        .replace(/\s*{\s*/g, ' {\n    ')
                        .replace(/;\s*/g, ';\n    ')
                        .replace(/\s*}\s*/g, '\n}\n\n')
                        .replace(/\n\s*\n\s*\n/g, '\n\n')
                        .trim();
                },

                formatJS(js) {
                    return js
                        .replace(/\s*{\s*/g, ' {\n    ')
                        .replace(/;\s*/g, ';\n    ')
                        .replace(/\s*}\s*/g, '\n}\n')
                        .replace(/\n\s*\n\s*\n/g, '\n\n')
                        .trim();
                },

                clearCode(type) {
                    if (type === 'css') {
                        this.cssCode = '';
                    } else {
                        this.jsCode = '';
                    }
                    this.updateLineNumbers(type);
                },

                resetToOriginal() {
                    this.cssCode = this.originalCss;
                    this.jsCode = this.originalJs;
                    this.updateLineNumbers('css');
                    this.updateLineNumbers('js');
                }
            }
        }
    </script>

    <style>
        textarea {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.5) rgba(15, 23, 42, 0.5);
        }

        textarea::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        textarea::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 4px;
        }

        textarea::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 4px;
        }

        textarea::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.7);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush
