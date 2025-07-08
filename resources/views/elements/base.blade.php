@if($keywords = setting('keywords')) @push('meta')
    <meta name="keywords" content="{{ $keywords }}">
@endpush @endif
