<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class FormBuilderUIController extends Controller
{
    /**
     * Redirect to the user's forms dashboard.
     */
    public function index(Request $request)
    {
        return redirect()->route('dashboard');
    }

    /**
     * Render the Jotform-style Form Builder Workspace.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();

        if ($user) {
            $form = $user->forms()->with(['fields' => fn($q) => $q->orderBy('order_index')])->find($id);

            if (!$form) {
                // If form exists in system and was created by initial seeder admin, claim it for developer
                $exists = Form::find($id);
                if ($exists) {
                    $adminUser = User::where('email', 'admin@itbad.ac.id')->first();
                    if ($adminUser && $exists->user_id === $adminUser->id) {
                        $exists->update(['user_id' => $user->id]);
                        $form = $user->forms()->with(['fields' => fn($q) => $q->orderBy('order_index')])->find($id);
                    }
                }

                if (!$form) {
                    abort(404, 'Formulir tidak ditemukan atau Anda tidak memiliki hak akses.');
                }
            }
        } else {
            $form = Form::with(['fields' => fn($q) => $q->orderBy('order_index')])->findOrFail($id);
        }

        $allForms = $user ? $user->forms()->select('id', 'title', 'slug')->get() : Form::select('id', 'title', 'slug')->get();

        return view('builder.index', compact('form', 'allForms'));
    }

    /**
     * Render the Submissions Inbox Table for a specific form.
     */
    public function submissions(Request $request, string $id)
    {
        $user = $request->user();

        if ($user) {
            $form = $user->forms()->with(['fields' => fn($q) => $q->orderBy('order_index')])->find($id);

            if (!$form) {
                $exists = Form::find($id);
                if ($exists) {
                    $adminUser = User::where('email', 'admin@itbad.ac.id')->first();
                    if ($adminUser && $exists->user_id === $adminUser->id) {
                        $exists->update(['user_id' => $user->id]);
                        $form = $user->forms()->with(['fields' => fn($q) => $q->orderBy('order_index')])->find($id);
                    }
                }

                if (!$form) {
                    abort(404, 'Formulir tidak ditemukan atau Anda tidak memiliki hak akses.');
                }
            }
        } else {
            $form = Form::with(['fields' => fn($q) => $q->orderBy('order_index')])->findOrFail($id);
        }

        $submissions = $form->submissions()->latest()->paginate(25);

        return view('builder.submissions', compact('form', 'submissions'));
    }
}
