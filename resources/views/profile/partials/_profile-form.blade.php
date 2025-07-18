<div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-card/80 border-border/50 shadow-lg">
    <div class="flex flex-col space-y-1.5 p-6">
        <h2 class="text-2xl font-semibold leading-none tracking-tight">Informations du profil</h2>
    </div>
    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Avatar --}}
            <div class="flex items-center space-x-6">
                <div class="relative flex h-24 w-24 shrink-0 overflow-hidden rounded-full bg-muted">
                    <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $user->name }}" class="h-full w-full object-cover rounded-full" />
                </div>
                <div>
                    <label for="avatar" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-sm font-medium border rounded-md hover:bg-accent hover:text-accent-foreground transition">
                        <i class="fa-solid fa-camera text-sm"></i>
                        Changer la photo
                    </label>
                    <input id="avatar" type="file" name="avatar" accept="image/*" class="hidden">
                    <p class="text-sm text-muted-foreground mt-1">JPG, PNG ou GIF. Max 2MB.</p>
                </div>
            </div>

            {{-- Form fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="space-y-2 md:col-span-2">
                    <label for="name" class="block text-sm font-medium">Nom d’utilisateur</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="Nom d'utilisateur" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="votre@email.com" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label for="bio" class="block text-sm font-medium">Bio</label>
                    <input id="bio" name="bio" type="text" value="{{ old('bio', $user->bio) }}" placeholder="Parlez-nous de vous..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                </div>

                <div class="space-y-2">
                    <label for="location" class="block text-sm font-medium">Localisation</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $user->location) }}" placeholder="Votre ville, pays" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                </div>

                <div class="space-y-2">
                    <label for="website" class="block text-sm font-medium">Site web</label>
                    <input id="website" name="website" type="url" value="{{ old('website', $user->website) }}" placeholder="https://votre-site.com" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium">Réseaux sociaux</label>
                    <div id="social-links-wrapper" class="space-y-2">
                        @php
                            $socialLinks = old('social_links', $user->social_links ?? []);
                        @endphp

                        @foreach($socialLinks as $index => $link)
                            <div class="flex items-center gap-2">
                                <input type="url" name="social_links[]" value="{{ $link }}" placeholder="https://..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                                <button type="button" class="remove-social-link px-2 py-1 text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach

                        {{-- Placeholder if none --}}
                        @if(count($socialLinks) === 0)
                            <div class="flex items-center gap-2">
                                <input type="url" name="social_links[]" placeholder="https://..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                                <button type="button" class="remove-social-link px-2 py-1 text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <button type="button" id="add-social-link" class="inline-flex items-center gap-2 mt-2 px-3 py-2 text-sm font-medium border rounded-md hover:bg-accent hover:text-accent-foreground transition">
                        <i class="fa-solid fa-plus"></i>
                        Ajouter un lien social
                    </button>

                    <p class="text-sm text-muted-foreground">Maximum 5 liens sociaux (TikTok, Twitter, LinkedIn, etc.).</p>
                </div>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-primary text-white rounded-md hover:bg-primary/90 transition">
                <i class="fa-solid fa-floppy-disk text-sm"></i>
                Sauvegarder les modifications
            </button>
        </form>
    </div>
</div>

@push('scripts')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const wrapper = document.getElementById('social-links-wrapper');
                const addBtn = document.getElementById('add-social-link');

                function createSocialInput(value = '') {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2';

                    const input = document.createElement('input');
                    input.type = 'url';
                    input.name = 'social_links[]';
                    input.value = value;
                    input.placeholder = 'https://...';
                    input.className = 'w-full px-3 py-2 border rounded-md';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-social-link px-2 py-1 text-red-600 hover:text-red-800';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    removeBtn.addEventListener('click', () => div.remove());

                    div.appendChild(input);
                    div.appendChild(removeBtn);
                    return div;
                }

                addBtn.addEventListener('click', () => {
                    const currentCount = wrapper.querySelectorAll('input[name="social_links[]"]').length;
                    if (currentCount < 5) {
                        wrapper.appendChild(createSocialInput());
                    } else {
                        alert('Vous pouvez ajouter jusqu’à 5 réseaux sociaux.');
                    }
                });

                wrapper.querySelectorAll('.remove-social-link').forEach(btn => {
                    btn.addEventListener('click', function () {
                        btn.closest('div').remove();
                    });
                });
            });
        </script>
    @endpush

@endpush
