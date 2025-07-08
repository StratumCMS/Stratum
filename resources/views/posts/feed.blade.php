{!! '<?xml version="1.0" encoding="UTF-8" ?>' !!}
<rss version="2.0">
    <channel>
        <title>Articles</title>
        <link>{{ url('/') }}</link>
        <description>Derniers articles publiés</description>

        @foreach($articles as $article)
            <item>
                <title>{{ $article->title }}</title>
                <link>{{ route('posts.show', $article) }}</link>
                <description>{{ $article->description }}</description>
                <pubDate>{{ $article->published_at->toRssString() }}</pubDate>
                <guid>{{ route('posts.show', $article) }}</guid>
            </item>
        @endforeach
    </channel>
</rss>
