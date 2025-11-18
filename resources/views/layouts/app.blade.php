<!DOCTYPE html>
@include('elements.base')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="StratumCMS">

    <title>@yield('title') | {{ site_name() }}</title>
    <meta property="og:title" content="@yield('title')">
    <meta property="og:type" content="@yield('type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ favicon() }}">
    <meta property="og:description" content="@yield('description', setting('description', ''))">
    <meta property="og:site_name" content="{{ site_name() }}">

    <link rel="shortcut icon" href="{{ favicon() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/default.css', 'resources/js/app.js'])

    @stack('scripts')
</head>
<body class="min-h-screen flex flex-col transition-colors duration-300 dark:bg-slate-900 bg-gray-50"
      x-data="notificationManager()"
      @form-success.window="showNotification('success', $event.detail.message)"
      @form-error.window="showNotification('error', $event.detail.message)">

<!-- Toast Container -->
<div class="fixed top-20 right-6 z-[9999] space-y-2 w-96" x-cloak>
    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-show="notification.visible"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0 scale-95"
            x-transition:enter-end="translate-x-0 opacity-100 scale-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100 scale-100"
            x-transition:leave-end="translate-x-full opacity-0 scale-95"
            class="relative rounded-lg shadow-lg backdrop-blur-xl border overflow-hidden"
            :class="{
                'bg-white/80 dark:bg-slate-800/80 border-green-200 dark:border-green-800': notification.type === 'success',
                'bg-white/80 dark:bg-slate-800/80 border-red-200 dark:border-red-800': notification.type === 'error'
            }"
        >
            <!-- Accent bar -->
            <div class="absolute left-0 top-0 bottom-0 w-1"
                 :class="{
                     'bg-green-500': notification.type === 'success',
                     'bg-red-500': notification.type === 'error'
                 }">
            </div>

            <div class="p-4 pl-5 flex items-start gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <template x-if="notification.type === 'success'">
                        <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </template>
                    <template x-if="notification.type === 'error'">
                        <div class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                            <i class="fas fa-exclamation text-white text-xs"></i>
                        </div>
                    </template>
                </div>

                <!-- Message -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white"
                       x-text="notification.message">
                    </p>
                </div>

                <!-- Close button -->
                <button
                    @click="removeNotification(notification.id)"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                >
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Progress bar -->
            <div class="h-1 w-full bg-gray-100 dark:bg-slate-700">
                <div class="h-full transition-all ease-linear"
                     :class="{
                         'bg-green-500': notification.type === 'success',
                         'bg-red-500': notification.type === 'error'
                     }"
                     :style="`width: ${notification.progress}%; transition-duration: ${notification.duration}ms;`">
                </div>
            </div>
        </div>
    </template>
</div>

@include('layouts.navigation')

<main class="flex-1 container mx-auto px-4 py-8">
    @yield('content')
</main>

@include('elements.footer')
</body>

<script src="https://kit.fontawesome.com/91664c67de.js" crossorigin="anonymous"></script>
<script>
    function notificationManager() {
        return {
            notifications: [],
            nextId: 1,

            showNotification(type, message, duration = 5000) {
                const id = this.nextId++;
                const notification = {
                    id,
                    type,
                    message,
                    visible: true,
                    progress: 100,
                    duration
                };

                this.notifications.push(notification);

                // Animate progress bar
                setTimeout(() => {
                    notification.progress = 0;
                }, 50);

                // Auto remove
                setTimeout(() => {
                    this.removeNotification(id);
                }, duration);
            },

            removeNotification(id) {
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.visible = false;
                    setTimeout(() => {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    }, 300);
                }
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.store('darkMode', {
            enabled: localStorage.getItem('darkMode') === 'true',
            toggle() {
                this.enabled = !this.enabled;
                localStorage.setItem('darkMode', this.enabled);
                document.documentElement.classList.toggle('dark', this.enabled);
            },
            init() {
                document.documentElement.classList.toggle('dark', this.enabled);
            }
        });
    });

    (function () {
        try {
            const theme = localStorage.getItem('stratum-theme') || 'dark';
            document.documentElement.classList.add(theme);
        } catch (e) {}
    })();
</script>
</html>
