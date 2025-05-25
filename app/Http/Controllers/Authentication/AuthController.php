<?php

namespace App\Http\Controllers\Authentication;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registration Part start ***************************
    public function register(request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ResponseHelper::Out(true, 'Registration Successful', 200);

        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Registration Failed, Please Try Again.', 500);

        }
    }
    // Registration Part end ***************************

    // Login Part start ***************************
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        try {
            $credentials = $request->only('email', 'password');
            $remember = ($request->filled('remember')) ? true : false;
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return ResponseHelper::Out(true, 'Login Successful', 200, $token);
            } else {
                return ResponseHelper::Out(false, 'Invalid Credentials', 401);
            }
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Login Failed, Please Try Again.', 500);
        }
    }
    // Login Part end ***************************

    // Logout Part start ***************************
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return ResponseHelper::Out(true, 'Logout Successful', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Logout Failed, Please Try Again.', 500);
        }
    }
    // Logout Part end ***************************
}
