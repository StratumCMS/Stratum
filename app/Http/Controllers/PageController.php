<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('created_at','desc')->get();
        $templates = $this->getTemplates();
        $stats = [
            'total' => $pages->count(),
            'published' => $pages->where('status','published')->count(),
            'draft' => $pages->where('status','draft')->count(),
            'homepage' => optional($pages->where('is_home',true)->first())->title,
        ];

        return view('admin.pages', compact('pages','templates','stats'));
    }

    public function create()
    {
        $templates = $this->getTemplates();
        return view('admin.pages-create', compact('templates'));
    }

    public function edit(Page $page)
    {
        $templates = $this->getTemplates();
        return view('admin.pages-edit', compact('page','templates'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title'=>'required',
            'slug'=>'required|unique:pages,slug',
            'content'=>'required',
            'meta_description'=>'required|max:160',
            'status'=>'required|in:draft,published,archived',
            'template'=>'required|string',
            'is_home'=>'nullable|boolean'
        ]);

        if(!empty($data['is_home'])){
            Page::where('is_home', true)->update(['is_home' => false]);
        } else {
            $data['is_home'] = false;
        }

        $page = Page::create($data);

        log_activity('page', 'Création', "Création de la page « {$data['title']} »");

        return redirect()->back()->with('success', 'Page créée avec succès.');
    }

    public function update(Page $page, Request $req)
    {
        $data = $req->validate([
            'title'=>'required',
            'slug'=>"required|unique:pages,slug,{$page->id}",
            'content'=>'required',
            'meta_description'=>'required|max:160',
            'status'=>'required|in:draft,published,archived',
            'template'=>'required|string',
            'is_home'=>'nullable|boolean'
        ]);

        if(!empty($data['is_home'])){
            Page::where('is_home', true)
                ->whereKeyNot($page->id)
                ->update(['is_home' => false]);
        } else if ($page->is_home) {
            $data['is_home'] = false;
        }

        $page->update($data);

        log_activity('page', 'Mise à jour', "Mise à jour de la page « {$data['title']} »");

        return redirect()->back()->with('success', 'Page mise à jour avec succès.');
    }

    public function destroy(Page $page)
    {
        if($page->is_home){
            return redirect()->back()->with('error', 'Impossible de supprimer la page d’accueil. Veuillez définir une autre page comme page d’accueil avant.');
        }

        $page->delete();

        log_activity('page', 'Suppression', "Suppression de la page « {$page->title} »");

        return redirect()->back()->with('success', 'Page supprimée avec succès.');
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        if ($page->status !== 'published') {
            abort(404);
        }

        $page->increment('views');

        return theme_view('pages', compact('page'));
    }

    private function getTemplates()
    {
        return [
            ['value'=>'default','label'=>'Par défaut'],
            ['value'=>'home','label'=>'Page d\'accueil'],
            ['value'=>'services','label'=>'Services'],
            ['value'=>'contact','label'=>'Contact'],
            ['value'=>'blog','label'=>'Blog'],
        ];
    }
}
