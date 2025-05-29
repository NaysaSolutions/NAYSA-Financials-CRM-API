<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\UsersDB; // Ensure you're using UsersDB model
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Validate request input
    $request->validate([
        'userId' => 'required|string|max:10|unique:users,userId',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed', // Make sure password confirmation is supported in the frontend
    ]);

    // Create user (automatically uses DB settings from .env)
    $user = UsersDB::create([
        'userId' => $request->userId,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully!',
        'user' => [
            'id' => $user->id,
            'userId' => $user->userId,
            'username' => $user->username,
            'email' => $user->email,
        ],
    ], 201);
}


    public function login(Request $request) 
{
    // Validate the request data
    $request->validate([
        'userId' => 'required|string',
        'password' => 'required',
    ]);

    // Find the user by userId, specifying the 'API' connection
    $user = UsersDB::on(env('DB_CONNECTION'))->where('userId', $request->userId)->first();


    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User does not exist.',
        ], 404);
    }

    // Check if the password matches
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.',
        ], 401);
    }

    // Return success response
    return response()->json([
        'status' => 'success',
        'message' => 'Login successful.',
        'user' => [
            'id' => $user->id,
            'userId' => $user->userId,
            'username' => $user->username,
            'email' => $user->email,
        ],
    ]);
}

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Use UsersDB model instead of User
         $user = UsersDB::on('API')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User with this email does not exist.',
            ], 404);
        }

        $resetCode = Str::random(40); 

        $user->reset_code = $resetCode;
        $user->save();

        $resetLink = url("/reset-password?code={$resetCode}");

        Mail::to($user->email)->send(new ResetPasswordMail($user, $resetLink));

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset link has been sent to your email.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Use UsersDB model instead of User
        $user = UsersDB::where('email', $request->email)->first();
        if ($user) {
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully!',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found.',
        ]);
    }
}
