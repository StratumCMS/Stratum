@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-card p-6 rounded-card shadow-glass">
        @csrf
        <div class="mb-4">
            <label for="site_name" class="block text-sm font-medium">Site Name</label>
            <input type="text" id="site_name" name="site_name" value="{{ config('app.name') }}" class="mt-1 block w-full">
        </div>

        <div class="mb-4">
            <label for="site_description" class="block text-sm font-medium">Description</label>
            <textarea id="site_description" name="site_description" class="mt-1 block w-full"></textarea>
        </div>

        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md">Save</button>
    </form>
@endsection
