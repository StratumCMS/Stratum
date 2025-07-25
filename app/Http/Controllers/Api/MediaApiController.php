<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Article;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaApiController extends Controller
{
    public function index(){

        return MediaResource::collection(
            Media::latest()->paginate(15)
        );
    }

    public function show(Media $media){

        return new MediaResource($media);
    }

    public function forArticle(Article $article){

        return MediaResource::collection(
            $article->getMedia()
        );
    }

    public function mediaItems(){

        $media = MediaItem::with('media')->get()->flatMap(fn ($item) => $item->getMedia());
        return MediaResource::collection($media);
    }
}
