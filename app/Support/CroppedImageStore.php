<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CroppedImageStore
{
    public static function store(?string $dataUrl, string $directory, string $prefix = 'image'): ?string
    {
        if (! $dataUrl) {
            return null;
        }

        if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $dataUrl, $matches)) {
            throw new InvalidArgumentException('Format gambar crop tidak valid.');
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $bytes = base64_decode($base64, true);

        if ($bytes === false) {
            throw new InvalidArgumentException('Gambar crop gagal diproses.');
        }

        $path = trim($directory, '/') . '/' . $prefix . '-' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }
}
