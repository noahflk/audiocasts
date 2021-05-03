<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show() {
        if(Auth::check() || !$this->authenticationIsRequired()) {
            return redirect("/");
        }

        return view("auth.login");
    }

    public function login(Request $request) {
        $credentials = $request->only("username", "password");
        $credentials["type"] = "web";

        if (Auth::attempt($credentials, true)) {
            return redirect()->intended("/");
        }

        return redirect("/login")->withErrors(["error" => "Username or password is incorrect"]);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/login");
    }

    private function authenticationIsRequired() {
        $result = User::where([["type", "web"], ["enabled", "1"]])->get();

        return !$result->isEmpty();
    }
}
