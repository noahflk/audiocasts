<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Response;

class SettingsController extends Controller
{
    public function get() {
        return Setting::all();
    }

    public function getOne($key) {
        try {
            return Setting::where("key", $key)->firstOrFail();

        } catch(\Exception $error) {
            return response()->json([
                "message" => "Record not found"
            ], 404);
        }
    }

    public function update(UpdateSettingRequest $request) {
        Setting::set($request->key, $request->value);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function delete(Setting $setting) {
        $setting->delete();
    }
}
