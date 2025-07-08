<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\NavbarElement;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NavbarElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = NavbarElement::with('elements')
            ->scopes('parent')
            ->orderBy('position')
            ->get();

        return view('admin.navbar', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allRoutes = collect(Route::getRoutes());

        // Filtrer les routes des modules
        $moduleRoutes = $allRoutes
            ->filter(fn($route) =>
                str_starts_with($route->getAction('namespace') ?? '', 'Modules\\')
                || str_contains($route->getActionName(), 'Modules\\')
            )
            ->filter(fn($route) => $route->getName())
            ->map(fn($route) => [
                'name' => $route->getName(),
                'uri' => $route->uri(),
            ])
            ->values();

        $pages = Page::pluck('title', 'slug');
        $articles = Article::where('is_published', true)->pluck('title', 'id');
        $dropdowns = NavbarElement::where('type', 'dropdown')->get();

        return view('admin.create-navbar', [
            'modules' => $moduleRoutes,
            'pages' => $pages,
            'articles' => $articles,
            'dropdowns' => $dropdowns,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'value' => 'nullable|string',
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|exists:navbar_elements,id',
        ]);

        NavbarElement::create($request->only('name', 'type', 'value', 'icon', 'parent_id'));

        return redirect()->route('navbar.index')->with('success', 'Élément ajouté.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NavbarElement $navbar)
    {
        $allRoutes = collect(Route::getRoutes());

        $modules = $allRoutes
            ->filter(fn($route) =>
                str_starts_with($route->getAction('namespace') ?? '', 'Modules\\') ||
                str_contains($route->getActionName(), 'Modules\\')
            )
            ->filter(fn($route) => $route->getName())
            ->map(fn($route) => [
                'name' => $route->getName(),
                'uri' => $route->uri(),
            ])
            ->values();

        $pages = \App\Models\Page::pluck('title', 'slug');
        $articles = \App\Models\Article::where('is_published', true)->pluck('title', 'id');
        $dropdowns = \App\Models\NavbarElement::where('type', 'dropdown')->where('id', '!=', $navbar->id)->get();

        return view('admin.edit-navbar', [
            'navbar' => $navbar,
            'modules' => $modules,
            'pages' => $pages,
            'articles' => $articles,
            'dropdowns' => $dropdowns,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NavbarElement $navbar)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'value' => 'nullable|string',
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|exists:navbar_elements,id',
        ]);

        $navbar->update($request->only('name', 'type', 'value', 'icon', 'parent_id'));

        return redirect()->route('navbar.index')->with('success', 'Élément mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NavbarElement $navbar)
    {
        $navbar->delete();
        return back()->with('success', 'Élément supprimé.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order') as $position => $id) {
            NavbarElement::where('id', $id)->update(['position' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
