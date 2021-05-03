<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Audiobook;
use Spatie\ArrayToXml\ArrayToXml;

class FeedController extends Controller
{
    const FEED_URL = "https://audiocasts-test.herokuapp.com/feed";

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $language = Setting::where("key", "FEED_LANGUAGE")->first()->value;
        $title = Setting::where("key", "FEED_TITLE")->first()->value;
        $description = Setting::where("key", "FEED_DESCRIPTION")->first()->value;
        $author = Setting::where("key", "FEED_AUTHOR")->first()->value;
        $cover = Setting::where("key", "FEED_COVER")->first()->value;

        $feedItems = [];

        foreach (Audiobook::all() as $audiobook) {
            array_push($feedItems, ...$this->generateAudiobookItems($audiobook));
        }


        $data = [
            "channel" => [
                "title" => $title,
                "link" => "https://noahfleischmann.com/audiobook",
                "language" => $language,
                "atom:link" => [
                    [
                        "_attributes" => [
                            "href" => self::FEED_URL,
                            "rel" => "self",
                            "type" => "application/rss+xml"
                        ]
                    ],
                    [
                        "_attributes" => [
                            "href" => "https://pubsubhubbub.appspot.com/",
                            "rel" => "hub",
                            "xmlns" => "www.w3.org/2005/Atom"
                        ]
                    ],
                ],
                "description" => [
                    "_cdata" => $description
                ],
                "itunes:author" => $author,
                "itunes:summary" => $description,
                "itunes:explicit" => "no",
                "itunes:category" => [
                    "_attributes" => [
                        "text" => "Technology"
                    ]
                ],
                "itunes:image" => [
                    "_attributes" => [
                        "href" => asset($cover)
                    ]
                ],
                "itunes:owner" => [
                    "itunes:name" => $author,
                ],
                "item" => $feedItems
            ]
        ];

        $feed = ArrayToXml::convert($data, [
            "rootElementName" => "rss",
            "_attributes" => [
                "version" => "2.0",
                "xmlns:atom" => "http://www.w3.org/2005/Atom",
                "xmlns:content" => "http://purl.org/rss/1.0/modules/content/",
                "xmlns:dc" => "http://purl.org/dc/elements/1.1/",
                "xmlns:itunes" => "http://www.itunes.com/dtds/podcast-1.0.dtd"
            ]
        ], true, "UTF-8", "1.0");

        return response($feed)->header("Content-Type", "application/xml");

    }

    private function generateAudiobookItems($audiobook) {
        return $audiobook->files->map(function ($file) use($audiobook) {
            return [
                "title" => $audiobook->title . " - " . $file->name,
                "link" => "https://regular.html/page",
                "guid" => $file["uuid"],
                "itunes:subtitle" => "Subtitle",
                "itunes:summary" => "This is a summary",
                "itunes:duration" => $this->getTime($file->playtime),
                "itunes:author" => $file->artist,
                "itunes:image" => asset("storage/" . $file->cover),
                "dc:creator" => $file->artist,
                "pubDate" => $file["updated_at"],
                "enclosure" => [
                    "_attributes" => [
                        "url" => asset("storage/" . $file["filename"]),
                        "type" => $file->type,
                        "length" =>  $file->filesize
                    ]
                ],
                "description" => "Regular description",
                "content:encoded" => [
                    "_cdata" => "Encoded description"
                ],
            ];
        })->toArray();
    }

    private function getTime($seconds) {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }
}
