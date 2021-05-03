<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedPropertiesController extends Controller
{
    public function uploadCover(Request $request)
    {
        $setting = new Setting();
        $setting->key = "FEED_COVER";
        $setting->value = $request->file('cover')->store('covers/');
        $setting->save();
    }

    public function storeSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['string', 'nullable'],
            'author' => ['required', 'string'],
            'language' => ['required', 'string'],
        ]);

        if($validator->fails()) {
            return response([
                "errors" => $validator->messages()
            ], 400);
        }

        $title = Setting::firstOrNew(['key' => "FEED_TITLE"]);
        $title->value = $request->title;
        $title->save();

        if(Setting::where(['key' => "FEED_DESCRIPTION"])->first()) {
            $description = Setting::firstOrNew(['key' => "FEED_DESCRIPTION"]);
            $description->value = $request->description;
            $description->save();
        }

        $author = Setting::firstOrNew(['key' => "FEED_AUTHOR"]);
        $author->value = $request->author;
        $author->save();

        $language = Setting::firstOrNew(['key' => "FEED_LANGUAGE"]);
        $language->value = $request->language;
        $language->save();

        return response()->noContent();
    }
}
