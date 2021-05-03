<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Filesystem;

class Pathlib {
    public function allDirectories($directory) {
        $subDir = [];
        $directories = array_filter(glob($directory), 'is_dir');
        $subDir = array_merge($subDir, $directories);
        foreach ($directories as $directory) $subDir = array_merge($subDir, $this->allDirectories($directory.'/*'));
        return $subDir;
    }

    public function files($directory) {
        return array_values(array_diff(scandir($directory), array('.', '..')));
    }
}
