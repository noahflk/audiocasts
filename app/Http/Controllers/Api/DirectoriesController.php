<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class DirectoriesController extends Controller
{
    public function list()
    {
        return [
            "directories" => Media::all()
        ];
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "directories" => "required|array",
            "directories.*" => "required|string"
        ]);

        // TODO: adding subpaths of already stored paths should not be allowed

        if($validator->fails()) {
            return response([
                "errors" => $validator->messages()
            ], 400);
        }

        $invalidDirectories = $this->getInvalidDirectoryPaths($request->directories);

        if(!empty($invalidDirectories)) {
            return response([
                "invalid" => $invalidDirectories
            ], 400);
        }

        $directories = array();

        foreach ($request->directories as $directory) {
            $directories[] = [
                "path" => $directory,
                "created_at" =>date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ];
        }

        foreach ($directories as $key => $directory) {
            $id = Media::insertGetId($directory);
            $directories[$key]["id"] = $id;
        }

        return response()->json($directories, 201);
    }

    public function delete(Media $media)
    {
        $media->delete();
        return response('', 204);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "directories" => "required|array",
            "directories.*" => "required|string"
        ]);

        if($validator->fails()) {
            return response([
                "errors" => $validator->messages()
            ], 400);
        }

        $invalidDirectories = $this->getInvalidDirectoryPaths($request->directories);

        return response([
            "invalid" => $invalidDirectories
        ], empty($invalidDirectories) ? 200 : 400);
    }

    private function getInvalidDirectoryPaths($directories)
    {
        $invalidPaths = [];

        // Check if this is a valid absolute path to a directory that exists
        foreach ($directories as $directory) {
            if(!$this->isValidDirectoryPath($directory)) {
                array_push($invalidPaths, $directory);
            }
        }

        return $invalidPaths;
    }

    private function isValidDirectoryPath($path)
    {
        if(!$this->isAbsolutePath($path)) return false;
        return File::isDirectory($path);
    }

    private function isAbsolutePath($path)
    {
        if (!is_string($path)) {
            $mess = sprintf('String expected but was given %s', gettype($path));
            throw new \InvalidArgumentException($mess);
        }
        if (!ctype_print($path)) {
            $mess = 'Path can NOT have non-printable characters or be empty';
            throw new \DomainException($mess);
        }
        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';
        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';
        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';
        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = sprintf('Path is NOT valid, was given %s', $path);
            throw new \DomainException($mess);
        }
        if ('' !== $parts['root']) {
            return true;
        }

        return false;
    }
}
