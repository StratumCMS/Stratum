@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="prose max-w-none">
        {!! $html !!}
    </div>
@endsection
