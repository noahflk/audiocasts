<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Audiobook;
use Illuminate\Support\Facades\URL;
use Spatie\ArrayToXml\ArrayToXml;

class FeedController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Mandatory fields
        $language = Setting::where('key', 'FEED_LANGUAGE')->first()->value;
        $title = Setting::where('key', 'FEED_TITLE')->first()->value;
        $author = Setting::where('key', 'FEED_AUTHOR')->first()->value;

        // Optional fields, check if record exists is necessary
        $descriptionRecord = Setting::where('key', 'FEED_DESCRIPTION')->first();
        $description = $descriptionRecord ? $descriptionRecord->value : null;
        $coverRecord = Setting::where('key', 'FEED_COVER')->first();
        $cover = $coverRecord ? $coverRecord->value : null;

        $feedItems = [];

        foreach (Audiobook::all() as $audiobook) {
            array_push($feedItems, ...$this->generateAudiobookItems($audiobook));
        }


        $data = [
            'channel' => [
                'title' => $title,
                'link' => URL::to('/feed'),
                'language' => $language,
                'atom:link' => [
                    [
                        '_attributes' => [
                            'href' => URL::to('/feed'),
                            'rel' => 'self',
                            'type' => 'application/rss+xml'
                        ]
                    ],
                    [
                        '_attributes' => [
                            'href' => 'https://pubsubhubbub.appspot.com/',
                            'rel' => 'hub',
                            'xmlns' => 'www.w3.org/2005/Atom'
                        ]
                    ],
                ],
                'description' => [
                    '_cdata' => $description
                ],
                'itunes:author' => $author,
                'itunes:summary' => $description,
                'itunes:explicit' => 'no',
                'itunes:category' => [
                    '_attributes' => [
                        'text' => 'Technology'
                    ]
                ],
                'itunes:image' => [
                    '_attributes' => [
                        'href' => asset($cover)
                    ]
                ],
                'itunes:owner' => [
                    'itunes:name' => $author,
                ],
                'item' => $feedItems
            ]
        ];

        $feed = ArrayToXml::convert($data, [
            'rootElementName' => 'rss',
            '_attributes' => [
                'version' => '2.0',
                'xmlns:atom' => 'http://www.w3.org/2005/Atom',
                'xmlns:content' => 'https://purl.org/rss/1.0/modules/content/',
                'xmlns:dc' => 'https://purl.org/dc/elements/1.1/',
                'xmlns:itunes' => 'https://www.itunes.com/dtds/podcast-1.0.dtd'
            ]
        ], true, 'UTF-8');

        return response($feed)->header('Content-Type', 'application/xml');

    }

    private function generateAudiobookItems($audiobook)
    {
        return $audiobook->files->map(function ($file) use ($audiobook) {
            return [
                'title' => $audiobook->title . ' - ' . $file->name,
                'link' => URL::to('/audiobooks/' . $audiobook->slug),
                'guid' => $file->uuid,
                'itunes:subtitle' => 'Your audiobooks',
                'itunes:summary' => 'Your audiobooks',
                'itunes:duration' => $this->getTime($file->playtime),
                'itunes:author' => $file->artist,
                'itunes:image' => asset($file->coverPath()),
                'dc:creator' => $file->artist,
                'pubDate' => $file['updated_at'],
                'enclosure' => [
                    '_attributes' => [
                        'url' => url('/audiobooks/' . $audiobook->slug . '/media/' . $file->id),
                        'type' => $file->type,
                        'length' => $file->filesize
                    ]
                ],
                'description' => 'Regular description',
                'content:encoded' => [
                    '_cdata' => 'Encoded description'
                ],
            ];
        })->toArray();
    }

    private function getTime($seconds): string
    {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    }
}
