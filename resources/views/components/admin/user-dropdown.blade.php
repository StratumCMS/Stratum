<div class="relative">
    <button class="flex items-center focus:outline-none">
        <img src="https://i.pravatar.cc/40" alt="Avatar" class="w-8 h-8 rounded-full mr-2">
        <span>{{ auth()->user()->name }}</span>
    </button>
    <div class="absolute right-0 mt-2 w-48 bg-card rounded-md shadow-glass p-2 text-sm">
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-primary rounded">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left px-4 py-2 hover:bg-primary rounded">Logout</button>
        </form>
    </div>
</div>
