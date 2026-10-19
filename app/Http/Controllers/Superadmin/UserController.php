<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('superadmin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        return redirect()->route('superadmin.users')->with('success', 'User ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'user', 422);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();

        return redirect()->route('superadmin.users')->with('success', 'User diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'user', 422);

        $user->delete();

        return redirect()->route('superadmin.users')->with('success', 'User dihapus.');
    }
}
