<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Tampilan halaman master
    public function index()
    {
        return view('users.index');
    }

    // Mengambil data user untuk tabel
    public function getData()
    {
        $users = User::select('id', 'name', 'id_number', 'created_at')->get();
        return response()->json($users);
    }

    // PROSES TAMBAH USER (CREATE)
    public function store(Request $request)
    {
        // Validasi input dengan pesan kustom berbahasa Indonesia
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'id_number' => 'required|string|max:50|unique:users,id_number',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'id_number.required' => 'ID Number / NIK wajib diisi.',
            'id_number.unique' => 'ID Number ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ]);

        // Simpan data ke database
        $user = User::create([
            'name' => $validated['name'],
            'id_number' => $validated['id_number'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User baru berhasil ditambahkan!',
            'data' => $user
        ], 201);
    }

    // PROSES UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'id_number.required' => 'ID Number wajib diisi.',
            'id_number.unique' => 'ID Number sudah digunakan oleh user lain.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->id_number = $validated['id_number'];

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui!'
        ]);
    }

    // PROSES HAPUS USER
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak diizinkan menghapus akun Anda sendiri yang sedang aktif!'
            ], 400);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus dari sistem!'
        ]);
    }
}