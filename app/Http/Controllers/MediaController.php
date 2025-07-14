<?php

namespace App\Http\Controllers;

use App\Models\MediaItem;
use Illuminate\Http\Request;
use App\Services\ImageOptimizerService;

class MediaController extends Controller
{
    public function index()
    {
        $mediaItems = MediaItem::with('media')->latest()->get();

        $mediaCount = $mediaItems->count();
        $imageCount = $mediaItems->filter(fn ($item) => $item->file_type === 'image')->count();
        $videoCount = $mediaItems->filter(fn ($item) => $item->file_type === 'video')->count();

        return view('admin.media', compact('mediaItems', 'mediaCount', 'imageCount', 'videoCount'));
    }

    public function upload(Request $request, ImageOptimizerService $optimizer)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,webm,ico|max:10240',
            'name' => 'nullable|string|max:255'
        ]);

        $file = $request->file('media');
        $name = $request->input('name') ?: $file->getClientOriginalName();

        $mediaItem = MediaItem::create(['name' => $name]);

        $media = $mediaItem->addMedia($file)->toMediaCollection('default');

        $optimizer->optimize($media->getPath());

        log_activity('media', 'Upload', "Fichier « {$name} » ajouté");

        return back()->with('success', 'Fichier uploadé avec succès.');
    }

    public function delete(MediaItem $mediaItem)
    {
        log_activity('media', 'Upload', "Fichier « {$mediaItem->name} » supprimé");
        $mediaItem->delete();
        return back()->with('success', 'Fichier supprimé.');
    }
}
