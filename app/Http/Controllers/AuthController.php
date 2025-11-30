<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        /**
         * ==========1===========
         * Validasi data registrasi yang masuk
         */
         $validator = validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'validation gagal',
                'errors' => $validator->errors()
            ], 422);
        }


        /**
         * =========2===========
         * Buat user baru dan generate token API, atur masa berlaku token 1 jam
         */
         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        $token = $user->createToken('auth_token')->plainTextToken;



        /**
         * =========3===========
         * Kembalikan response sukses dengan data $user dan $token
         */
         return response()->json([
            'message' => 'registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);

    }


    public function login(Request $request)
    {
        /**
         * =========4===========
         * Validasi data login yang masuk
         */

        /**
         * =========5===========
         * Generate token API untuk user yang terautentikasi
         * Atur token agar expired dalam 1 jam
         */

        /**
         * =========6===========
         * Kembalikan response sukses dengan data $user dan $token
         */

    }

    public function logout(Request $request)
    {
        /**
         * =========7===========
         * Invalidate token yang digunakan untuk autentikasi request saat ini
         */


        /**
         * =========8===========
         * Kembalikan response sukses
         */

    }
}
