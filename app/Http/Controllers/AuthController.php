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
         $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        /**
         * =========2===========
         * Buat user baru dan generate token API, atur masa berlaku token 1 jam
         */
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);

        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addHour()
        )->plainTextToken;



        /**
         * =========3===========
         * Kembalikan response sukses dengan data $user dan $token
         */
         return response()->json([
            'message'=>'Register success',
            'user'=>$user,
            'token'=>$token
        ], 201);
    
    }


    public function login(Request $request)
    {
        /**
         * =========4===========
         * Validasi data login yang masuk
         */
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (!Auth::attempt($request->only('email','password'))) {
            return response()->json(['message'=>'Invalid credentials'],401);
        }
        /**
         * =========5===========
         * Generate token API untuk user yang terautentikasi
         * Atur token agar expired dalam 1 jam
         */
        $user = Auth::user();

        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addHour()
        )->plainTextToken;
        /**
         * =========6===========
         * Kembalikan response sukses dengan data $user dan $token
         */
         return response()->json([
            'message'=>'Login success',
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout(Request $request)
    {
        /**
         * =========7===========
         * Invalidate token yang digunakan untuk autentikasi request saat ini
         */
        $request->user()->currentAccessToken()->delete();

        /**
         * =========8===========
         * Kembalikan response sukses
         */
        return response()->json(['message'=>'Logged out']);
    }
}
