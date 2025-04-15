<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Actions\UpdateUserStatus;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.users.index', [
            'users' => User::query()
                ->with('role:id,name')
                ->get(['id', 'name', 'email']),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create', [
            'roles' => Role::all(['id', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // 'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['role_id'] = 2;

        $user = User::create($validated);


        session()->flash('success', __('User created successfully'));

        return redirect()->route('admin.users.index');
    }

    /**
     * Enable or disable user account
     */
    public function updateStatus(User $user): RedirectResponse
    {

        // if (! $user->is_active) {
        //     session()->flash('error', __('You cannot enable this account because it was disabled by its owner.'));
        //     return back();
        // }

        if ($user->is_active) {
            $user->is_active = false;
            // $user->disabled_by = Auth::id();
            // $user->disabled_at = now();
        } else {
            $user->is_active = true;
            // $user->disabled_by = null;
            // $user->disabled_at = null;
        }
        $user->save();
        $message = match (intval($user->is_active)) {
            1 => __('Account has been successfully unblocked.'),
            0 => __('Account has been successfully blocked.'),
        };

        session()->flash('success', $message);



        return back();
    }
}
