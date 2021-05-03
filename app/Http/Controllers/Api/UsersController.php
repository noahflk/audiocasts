<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use stdClass;

class UsersController extends Controller
{
    public function get() {
        $webUser = User::where("type", "web")->first();
        $feedUser = User::where("type", "feed")->first();

        return [
            "web" => $webUser ?? new stdClass(),
            "feed" => $feedUser ?? new stdClass()
        ];
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => ['string', 'min:1', 'max:255', 'alpha_dash'],
            'username' => ['string', 'min:1', 'max:255', 'alpha_dash'],
            "type" => ["required", Rule::in(["web", "feed"])]
        ]);

        if($validator->fails()) {
            return response([
                "errors" => $validator->messages()
            ], 400);
        }

        try {
            return User::create([
                "username" => $request["username"],
                "password" => Hash::make($request["password"]),
                "type" => $request["type"],
            ]);
        } catch(\Exception $e) {
            return response([
                "errors" => "Unable to save user"
            ], 500);
        }
    }

    public function update(User $user, Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => ['string', 'min:1', 'max:255', 'alpha_dash'],
            'username' => ['string', 'min:1', 'max:255', 'alpha_dash'],
            'enabled' => ['boolean']
        ]);

        if($validator->fails()) {
            return response([
                "errors" => $validator->messages()
            ], 400);
        }

        if($request["username"] !== null) {
            $user->username = $request["username"];
        }

        if($request["password"] !== null) {
            $user->password = Hash::make($request["password"]);
        }

        if($request["enabled"] !== null) {
            $user->enabled = $request["enabled"];
        }

        $user->save();
        return $user;
    }
}
