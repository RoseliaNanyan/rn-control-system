<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:4',
            'role' => 'required',
        ]);

        // SIMPAN USER
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/users')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);

        return view('users.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // VALIDASI
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'role' => 'required',
        ]);

        $user = User::findOrFail($id);

        // UPDATE DATA
        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;

        // UPDATE PASSWORD JIKA DIISI
        if ($request->password) {

            $user->password = Hash::make($request->password);

        }

        $user->save();

        return redirect('/users')
            ->with('success', 'User berhasil diupdate');
    }

    public function delete($id)
    {
        // CEGAH HAPUS USER SENDIRI
        if (auth()->id() == $id) {

            return back()->with(
                'error',
                'User aktif tidak bisa dihapus'
            );

        }

        $user = User::findOrFail($id);

        $user->delete();

        return redirect('/users')
            ->with('success', 'User berhasil dihapus');
    }
}