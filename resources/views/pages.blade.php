@extends('layouts.app')

@section('title', $page->title)

@section('content')
    <div class="prose max-w-none">
        {!! $page->renderer_content !!}
    </div>
@endsection
