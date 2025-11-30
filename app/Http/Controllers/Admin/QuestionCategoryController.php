<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Liste des catégories
    public function index()
    {
        $categories = QuestionCategory::latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    // Formulaire de création
    public function create()
    {
        return view('admin.categories.create');
    }

    // Enregistrer une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:question_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        QuestionCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès !');
    }

    // Formulaire d'édition
    public function edit(QuestionCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Mettre à jour une catégorie
    public function update(Request $request, QuestionCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:question_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour avec succès !');
    }

    // Supprimer une catégorie
    public function destroy(QuestionCategory $category)
    {
        // Vérifier s'il y a des questions liées (optionnel : pour éviter les suppressions accidentelles)
        if ($category->questions()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : des questions sont liées à cette catégorie.');
        }

        $category->delete();
        return back()->with('success', 'Catégorie supprimée avec succès !');
    }
}