<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['branch', 'roles'])->paginate(10);
        $branches = Branch::where('is_active', true)->get();
        $roles = Role::all();
        
        return view('pages.users.index', compact('users', 'branches', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id']
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', 'User Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $validated['branch_id'];
        
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();

        // Update role
        $user->syncRoles([$validated['role']]);

        return back()->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === 1 || auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun Superadmin atau akun Anda sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User Karyawan berhasil dihapus.');
    }
}
