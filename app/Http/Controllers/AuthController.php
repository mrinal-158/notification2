<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\User;
use App\Notifications\UserNotify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $verifyOTP = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $verifyOTP,
            'otp_expires_at' => $otp_expires_at,
        ]);

        // Mail::to($user->email)->queue(new OTPMail($verifyOTP, 'Recieved OTP for verify!'));
        $user->notify(new UserNotify($verifyOTP, 'You register Google!'));

        return response()->json([
            'message' => 'User Registration successful. Please check emaail and verify using OTP.',
            'user' => $user,
        ]);
    }

    public function resendOTP(Request $request){
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'message' => 'Email not found!',
            ]);
        }

        $verifyOTP = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);
        $user->otp = $verifyOTP;
        $user->otp_expires_at = $otp_expires_at;
        $user->save();

        // Mail::to($user->email)->queue(new OTPMail($verifyOTP, 'Resend OTP for verify!'));
        $user->notify(new UserNotify($verifyOTP, 'You resend OTP in Google!'));

        return response()->json([
            'message' => 'Resend otp success.',
            'user' => $user,
        ]);
    }

    public function verifyOTP(Request $request){
        
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);
        
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'Email not found!',
            ]);
        }

        if($user->otp != $request->otp){
            return response()->json([
                'message' => 'Incorrect OTP!',
            ]);
        }

        if(Carbon::now()->gt($user->otp_expires_at)){
            return response()->json([
                'message' => 'OTP is expired!',
            ]);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now(),
        ]);

        $user->notify(new UserNotify('OTP Verification Successfull!', 'OTP Verification Successfull!'));

        return response()->json([
            'message' => 'OTP Verification Successfull!',
            'user' => $user,
        ]);
    }

    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'message' => 'Email not found!',
            ]);
        }

        $forgotPasswordOTP = rand(100000, 999999);
        $otp_expires_at = Carbon::now()->addMinutes(10);

        Mail::to($user->email)->queue(new OTPMail($forgotPasswordOTP, 'Forgot Password OTP!'));

        $user->otp = $forgotPasswordOTP;
        $user->otp_expires_at = $otp_expires_at;
        $user->save();

        return response()->json([
            'message' => 'Resend otp success.',
            'user' => $user,
        ]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|string|email|max:255',
            'forgot_password_otp' => 'required',
            'password' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'Email not found!',
            ]);
        }

        if($user->otp != $request->otp){
            return response()->json([
                'message' => 'Incorrect OTP!',
            ]);
        }

        if(!$user->otp){
            return response()->json([
                'message' => 'Forgot Password OTP filled is empty!',
            ]);
        }

        if(Carbon::now()->gt($user->otp_expires_at)){
            return response()->json([
                'message' => 'OTP is expired!',
            ]);
        }

        $user->otp = null;
        $user->password = $request->password;
        $user->save();

        return response()->json([
            'message' => 'Password Reset Successfull!',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Invalid credentials',
            ]);
        }

        if(!$user->email_verified_at){
            return response()->json([
                'message' => 'You are not verified!',
            ]);
        }

        $token = JWTAuth::attempt($request->only('email', 'password'));

        $user->notify(new UserNotify('You are Logged in Google!', 'You are Logged in Google!'));

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // $user = Auth::user();
        // $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'User not logged in',
            ]);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if($request->has('name')){
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $user->update([
                'name' => $request->name,
            ]);
        }

        if($request->has('passworrd')){
            $request->validate([
                'password' => "required|string|min:6",
            ]);
            if(!Hash::check($request->old_password, $user->password)){
                return response()->json([
                    'message' => 'Your Current password does not match. Please Enter Correct password',
                ]);
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully']);
    }
}