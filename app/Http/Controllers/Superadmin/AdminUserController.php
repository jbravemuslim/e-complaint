<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::whereIn('role', ['admin', 'superadmin'])->orderBy('name')->get();

        return view('superadmin.admins', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:4'],
            'role' => ['required', Rule::in(['admin', 'superadmin'])],
        ]);

        User::create($validated);

        return redirect()->route('superadmin.admins')->with('success', 'Admin ditambahkan.');
    }

    public function update(Request $request, User $admin)
    {
        abort_unless(in_array($admin->role, ['admin', 'superadmin'], true), 422);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:4'],
            'role' => ['required', Rule::in(['admin', 'superadmin'])],
        ]);

        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        $admin->role = $validated['role'];
        if (! empty($validated['password'])) {
            $admin->password = $validated['password'];
        }
        $admin->save();

        return redirect()->route('superadmin.admins')->with('success', 'Admin diperbarui.');
    }

    public function destroy(User $admin)
    {
        abort_unless(in_array($admin->role, ['admin', 'superadmin'], true), 422);
        abort_if($admin->id === auth()->id(), 422, 'Tidak bisa menghapus akun sendiri.');
        abort_if(
            $admin->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1,
            422, 'Tidak bisa menghapus satu-satunya superadmin.'
        );

        // Respons ikut terhapus via cascade; kembalikan statusnya ke proses
        // agar tidak ada keluhan 'selesai' tanpa tanggapan.
        Complain::whereHas('response', fn ($q) => $q->where('admin_id', $admin->id))
            ->update(['status' => 'proses']);

        $admin->delete();

        return redirect()->route('superadmin.admins')->with('success', 'Admin dihapus.');
    }
}
