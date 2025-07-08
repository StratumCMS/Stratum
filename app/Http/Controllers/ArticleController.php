<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'archived') {
                $query->where('archived', true);
            }
        }

        $articles = $query->paginate(10);
        $availableTypes = Article::select('type')->distinct()->pluck('type')->filter()->all();

        $stats = [
            'total' => Article::count(),
            'published' => Article::where('is_published', true)->count(),
            'draft' => Article::where('is_published', false)->count(),
            'archived' => Article::where('archived', true)->count(),
        ];

        return view('admin.articles', compact('articles', 'availableTypes', 'stats'));
    }

    public function indexPub()
    {
        $posts = Article::where('is_published', true)
            ->where(function ($q) {
                $q->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })
            ->latest()
            ->paginate(10);

        return view('theme::posts.index', compact('posts'));
    }

    public function create()
    {
        $mediaItems = Media::latest()->get()->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->hasGeneratedConversion('thumb') ? $m->getFullUrl('thumb') : $m->getFullUrl(),
        ]);

        return view('admin.create-articles', compact('mediaItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'type' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'thumbnail_media_id' => 'nullable|exists:media,id',
        ]);

        $article = new Article($validated);
        $article->user_id = auth()->id();
        $article->tags = $validated['tags']
            ? array_filter(array_map('trim', explode(',', $validated['tags'])))
            : [];

        if ($request->filled('thumbnail_media_id')) {
            $media = Media::findOrFail($request->input('thumbnail_media_id'));
            $article->thumbnail = $media->getFullUrl();
        }

        $article->save();

        return redirect()->route('admin.articles')->with('success', 'Article créé avec succès.');
    }


    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);
        return view('posts.show', compact('article'));
    }

    public function togglePublish(Article $article)
    {
        $article->update(['is_published' => !$article->is_published]);
        return back()->with('success', 'État de publication mis à jour.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Article supprimé.');
    }
}
