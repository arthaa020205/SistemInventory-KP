<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Helpers\ActivityLogger;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $data = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('users.index', compact('data', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'Aktif',
        ]);

        $user->assignRole($request->role);

        ActivityLogger::log(
            'CREATE',
            'User',
            'Menambahkan user: ' . $user->name .
            ' dengan role: ' . $request->role
        );

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
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
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'status' => 'required',
        ]);

        $namaLama = $user->name;
        $roleLama = $user->getRoleNames()->first();
        $statusLama = $user->status;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        ActivityLogger::log(
            'UPDATE',
            'User',
            'Mengubah user: ' . $namaLama .
            ' menjadi ' . $user->name .
            ' | Role: ' . ($roleLama ?? '-') .
            ' → ' . $request->role .
            ' | Status: ' . $statusLama .
            ' → ' . $request->status
        );

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
            return back()->with(
                'error',
                'Tidak dapat menghapus akun sendiri.'
            );
        }

        $namaUser = $user->name;
        $emailUser = $user->email;
        $roleUser = $user->getRoleNames()->first();

        $user->delete();

        ActivityLogger::log(
            'DELETE',
            'User',
            'Menghapus user: ' . $namaUser .
            ' | Email: ' . $emailUser .
            ' | Role: ' . ($roleUser ?? '-')
        );

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}