@extends('layouts.app')

@section('title', $page->title)

@section('content')
    <div class="prose max-w-none">
        {!! $page->content !!}
    </div>
@endsection
