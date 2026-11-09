<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main user forms dashboard (Jotform "My Forms" style).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $forms = $user->forms()
            ->withCount('submissions')
            ->latest()
            ->get();

        $totalForms = $forms->count();
        $totalSubmissions = $forms->sum('submissions_count');
        $activeForms = $forms->where('is_active', true)->count();

        return view('dashboard.index', compact('user', 'forms', 'totalForms', 'totalSubmissions', 'activeForms'));
    }

    /**
     * Create a brand-new form and redirect directly to the Form Builder Workspace.
     */
    public function storeForm(Request $request): RedirectResponse
    {
        $user = $request->user();
        $count = $user->forms()->count() + 1;

        $title = $request->input('title') ?: "Formulir Baru #{$count}";
        $baseSlug = Str::slug($title);
        $slug = $baseSlug . '-' . Str::lower(Str::random(5));

        $form = $user->forms()->create([
            'title' => $title,
            'slug' => $slug,
            'description' => 'Klik untuk mengedit deskripsi formulir ini...',
            'is_active' => true,
        ]);

        return redirect()->route('forms.edit', ['id' => $form->id]);
    }

    /**
     * Delete a form owned by the authenticated user.
     */
    public function destroyForm(Request $request, string $id): RedirectResponse
    {
        $form = $request->user()->forms()->findOrFail($id);
        $title = $form->title;
        $form->delete();

        return redirect()->route('dashboard')->with('success', "Formulir \"{$title}\" berhasil dihapus.");
    }
}

