<?php

function album_cover_path(string $fileName): string
{
    return public_path(config('audiocasts.cover_directory') . $fileName);
}
