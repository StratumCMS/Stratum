<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageApiController extends Controller
{
    public function index()
    {
        $pages = Page::where('status', 'published')
            ->latest()
            ->paginate(10);

        return PageResource::collection($pages);
    }

    public function show(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        return new PageResource($page);
    }
}
