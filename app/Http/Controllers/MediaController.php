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

        if (request()->wantsJson() || request()->has('json')) {
            return response()->json([
                'media' => $mediaItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'url' => $item->getFirstMediaUrl(),
                        'type' => $item->file_type,
                        'created_at' => $item->created_at->toDateTimeString(),
                    ];
                })
            ]);
        }

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

        if (str_starts_with($media->mime_type, 'image/')) {
            $optimizer->optimize($media->getPath());
        }

        log_activity('media', 'Upload', "Fichier « {$name} » ajouté");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès.',
                'media' => [
                    'id' => $mediaItem->id,
                    'name' => $mediaItem->name,
                    'url' => $mediaItem->getFirstMediaUrl(),
                    'type' => $mediaItem->file_type,
                ]
            ]);
        }

        return back()->with('success', 'Fichier uploadé avec succès.');
    }

    public function syncStorageLink()
    {
        try {
            $publicStoragePath = public_path('storage');

            if (file_exists($publicStoragePath)) {
                if (is_link($publicStoragePath)) {
                    unlink($publicStoragePath);
                } else {
                    $message = 'Un dossier/fichier "storage" existe déjà dans public/. Supprimez-le manuellement.';

                    if (request()->wantsJson()) {
                        return response()->json(['error' => $message], 400);
                    }
                    return back()->with('error', $message);
                }
            }

            \Artisan::call('storage:link');

            log_activity('media', 'System', 'Lien symbolique storage resynchronisé');

            if (request()->wantsJson()) {
                return response()->json(['success' => 'Lien symbolique storage resynchronisé avec succès.']);
            }

            return back()->with('success', 'Lien symbolique storage resynchronisé avec succès.');

        } catch (\Exception $e) {
            $message = 'Erreur lors de la resynchronisation : ' . $e->getMessage();

            if (request()->wantsJson()) {
                return response()->json(['error' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }

    public function delete(MediaItem $mediaItem)
    {
        log_activity('media', 'Upload', "Fichier « {$mediaItem->name} » supprimé");
        $mediaItem->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => 'Fichier supprimé avec succès.']);
        }

        return back()->with('success', 'Fichier supprimé.');
    }
}
