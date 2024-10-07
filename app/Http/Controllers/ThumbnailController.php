<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThumbnailController extends Controller
{
    public function __invoke(
        string $dir,
        string $method,
        string $size,
        string $file,
    ): BinaryFileResponse {
        try {
            abort_if(
                !in_array($size, config('thumbnail.allowed_sizes', [])),
                403,
                'Size not allowed');

            $storage = Storage::disk('images');

            $realPath = "$dir/$file";
            $newDirPath = "$dir/$method/$file";
            $resultPath = "$newDirPath/$file";

            if (!$storage->exists($newDirPath)) {
                $storage->makeDirectory($newDirPath);
            }

            if (!$storage->exists($resultPath)) {

                    $image = Image::read($storage->path("$realPath"));

                    [$w, $h] = explode('x', $size);

                    $image->{$method}($w, $h);

                    $image->save($storage->path($resultPath));

            }

            return response()->file($storage->path($resultPath));
        } catch (\Exception $e) {
            throw new Exception('Ошибка при сохранении загружаемого файла', Response::HTTP_BAD_REQUEST);
        }
    }

}
