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
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-md bg-green-100 text-green-800 border border-green-200">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                    <form x-data="customAssetsForm()" x-init="initEditors()" @submit.prevent="syncAndSubmit($event)" method="POST" action="{{ route('admin.custom-assets.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-2">
                            <label class="form-label" for="css-editor">CSS personnalisé</label>
                            <textarea name="css" id="css-editor" hidden>{{ old('css', $css) }}</textarea>
                            <div id="codemirror-css" class="border rounded-md bg-muted/10 min-h-[200px] md:min-h-[350px] overflow-auto text-sm"></div>
                            @error('css') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="form-label" for="js-editor">JS personnalisé</label>
                            <textarea name="js" id="js-editor" hidden>{{ old('js', $js) }}</textarea>
                            <div id="codemirror-js" class="border rounded-md bg-muted/10 min-h-[200px] md:min-h-[350px] overflow-auto text-sm"></div>
                            @error('js') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 hover-glow-purple">
                                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/material-darker.min.css" />
    <style>
        .CodeMirror {
            background-color: #1e1e2f;
            color: #f1f1f1;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .CodeMirror-gutters {
            background: transparent;
            border-right: 1px solid #3a3a4a;
            color: #999;
        }
        .CodeMirror-scroll {
            max-height: 500px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/scroll/annotatescrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/search/searchcursor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/search/matchesonscrollbar.min.js"></script>

    <script>
        function customAssetsForm() {
            return {
                cssEditor: null,
                jsEditor: null,

                initEditors() {
                    const cssTextarea = document.getElementById('css-editor');
                    const jsTextarea = document.getElementById('js-editor');

                    this.cssEditor = CodeMirror(document.getElementById('codemirror-css'), {
                        value: cssTextarea.value || '',
                        mode: 'css',
                        theme: 'material-darker',
                        lineNumbers: true,
                        lineWrapping: true,
                        viewportMargin: Infinity,
                    });

                    this.jsEditor = CodeMirror(document.getElementById('codemirror-js'), {
                        value: jsTextarea.value || '',
                        mode: 'javascript',
                        theme: 'material-darker',
                        lineNumbers: true,
                        lineWrapping: true,
                        viewportMargin: Infinity,
                    });
                },

                syncAndSubmit(event) {
                    const css = this.cssEditor?.getValue() || '';
                    const js = this.jsEditor?.getValue() || '';

                    document.getElementById('css-editor').value = css;
                    document.getElementById('js-editor').value = js;

                    event.target.submit();
                }
            }
        }
    </script>

@endpush
