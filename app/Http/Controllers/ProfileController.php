<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de profil utilisateur.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Met à jour les informations du profil (nom, email, avatar).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();
        $data = [];

        if ($request->filled('name')) {
            $data['name'] = $request->name;
        }

        if ($request->filled('email')) {
            $data['email'] = $request->email;
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('users/avatars', 'public');
        }

        if (!empty($data)) {
            $user->update($data);
        }
        session()->flash('success', __('Your profile has been successfully updated! 🎉'));

        return redirect()->route('profile.edit');

    }

    /**
     * Met à jour le mot de passe.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'new_password_confirmation' => ['required'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);
        session()->flash('success', __('Your password has been successfully updated! 🎉'));

        return redirect()->route('profile.edit');
    }

    /**
     * Supprime le compte utilisateur après vérification du mot de passe.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'delete_password' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Vérifie si le mot de passe fourni correspond au mot de passe actuel
        if (!Hash::check($request->delete_password, $user->password)) {
            return back()->withErrors(['delete_password' => __('The provided password does not match your current password.')])
                        ->withInput();
        }

        // Supprimer l'avatar s'il existe
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->is_active = false; // Désactivation au lieu de suppression physique
        $user->save();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
