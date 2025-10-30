@extends('theme::layouts.app')

@section('title', $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="prose max-w-none">
        {!! $page->content !!}
    </div>
@endsection
