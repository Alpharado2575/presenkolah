<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input yang masuk
        $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:siswa,guru,admin',
            'kelas'    => 'nullable|string|max:20',
        ]);

        // 2. Simpan ke database
        $user = User::create([
            'nama'      => $request->nama,
            'username'  => $request->username,
            'password'  => Hash::make($request->password), // Password wajib di-hash
            'role'      => $request->role,
            'kelas'     => $request->kelas,
            'is_active' => 1, // Default aktif
        ]);

        // 3. Kembalikan response JSON agar enak dibaca di Bruno
        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data'    => $user
        ], 201);
    }
}