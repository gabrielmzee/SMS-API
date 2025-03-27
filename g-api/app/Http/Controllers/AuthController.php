<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'is_active' => false,
            ]);

            // Send email verification notification
            event(new Registered($user));
            
            // $token = JWTAuth::fromUser($user);

            return response()->json([
                'statusCode' => '2',
                'message' => 'Successfully created. Please verify your email.',
                // 'token' => $token,
                // 'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while creating the record',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            // Get credentials from the request
            $credentials = $request->only('email', 'password');

            // Attempt to authenticate and get the token using the 'api' guard
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(
                    [
                        'statusCode' => '1',
                        'message' => 'Unauthorized'
                    ], 401);
            }

            // Check if the user has verified their email
            $user = auth()->user();

            if (!$user->hasVerifiedEmail() || !$user->is_active) {
                // If the user hasn't verified their email, log them out and return an error message
                if(!$user->hasVerifiedEmail()){
                    $message = 'Email not verified. Please verify your email before logging in.';
                }else{
                    $message = 'Your account is currently disabled, Kindly contact your system administrator.';
                }
                auth()->logout();
                return response()->json(
                    [
                        'statusCode' => '1',
                        'message' => $message
                    ], 403);
            }

            // Return the response with the token and user details
            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully logged in',
                'token' => $token,
                'user' => $user->only(['id', 'name', 'email', 'is_active'])
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return the error message
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while logging in',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Get authenticated user
    public function me()
    {
        return response()->json(Auth::user());
    }

    // Logout user
    public function logout()
    {
        try {
            Auth::logout();
            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return the error message
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Refresh JWT token
    public function refresh()
    {
        // return $this->respondWithToken(Auth::refresh());
        try {
            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully refreshed token',
                'token' => Auth::refresh(),
                'user' => auth()->user()->only(['id', 'name', 'email'])
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return the error message
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Format token response
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }
}
