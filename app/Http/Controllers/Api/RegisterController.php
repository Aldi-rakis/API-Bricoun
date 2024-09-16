<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'umur' => 'required|integer|min:0',
            'kelas' => 'required|string|max:100',
        ]);

        // Jika validasi gagal, kembalikan respon error
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Jika validasi berhasil, buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'umur' => $request->umur,
            'kelas' => $request->kelas,
        ]);

        // Buat token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kembalikan respon dengan data user dan token
        return response()->json([
            'message' => 'Sukses membuat akun',
            'data' => $user,
            'token' => $token,
        ], 200);
    }
}
