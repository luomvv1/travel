<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // tìm user theo email
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'tendangnhap' => $googleUser->getName(),
                'email'       => $googleUser->getEmail(),
                'matkhau'     => Hash::make(uniqid()),
                'vaitro'      => 'khach_hang',
                'trangthai'   => 'hoat_dong'
            ]);
        }

        Auth::login($user);
        session()->put('tendangnhap', $user->tendangnhap);
        session()->put('ndid', $user->ndid);

        return redirect()->route('home');
    }
}