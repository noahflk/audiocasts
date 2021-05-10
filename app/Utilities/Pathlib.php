<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Filesystem;

class Pathlib
{
    public function allDirectories($directory)
    {
        $subDir = [];
        $directories = array_filter(glob($directory), 'is_dir');
        $subDir = array_merge($subDir, $directories);
        foreach ($directories as $directory) $subDir = array_merge($subDir, $this->allDirectories($directory . '/*'));
        return $subDir;
    }

    public function files($directory, $includePath = true)
    {
        $values = array_values(array_diff(scandir($directory), array('.', '..')));

        if (!$includePath) {
            return $values;
        }
        return array_map(function ($file) use ($directory) {
            return $directory . '/' . $file;
        }, $values);
    }
}
