<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class FeedGenerationService
{
    public function __construct(
        private AudiobookFilesFeedEntryGenerationService $audiobookFilesFeedEntryGenerationService
    )
    {
    }

    public function generate(): array
    {
        // Fetch properties required for feed
        $author = Setting::where('key', 'FEED_AUTHOR')->first()->value;
        // Optional fields, check if record exists is necessary
        $descriptionRecord = Setting::where('key', 'FEED_DESCRIPTION')->first();
        $description = $descriptionRecord ? $descriptionRecord->value : null;
        $coverRecord = Setting::where('key', 'FEED_COVER')->first();
        $cover = $coverRecord ? $coverRecord->value : null;

        $data = [
            'channel' => [
                'title' => Setting::where('key', 'FEED_TITLE')->first()->value,
                'link' => URL::to('/feed'),
                'language' => Setting::where('key', 'FEED_LANGUAGE')->first()->value,
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
                'item' => Audiobook::all()->map(function ($audiobook) {
                    return $this->audiobookFilesFeedEntryGenerationService->generate($audiobook);
                })
            ]
        ];
    }
}
