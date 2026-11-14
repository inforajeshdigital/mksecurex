<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Stores uploads under /public/uploads (works on shared hosting without storage:link),
 * registers them in the media library and creates an optimised WebP copy when GD supports it.
 */
class ImageService
{
    public const ALLOWED = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'ico'];

    public const MAX_KB = 4096;

    public static function rules(bool $required = false): array
    {
        return [$required ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,ico', 'max:'.self::MAX_KB];
    }

    public function upload(UploadedFile $file, array $meta = []): Media
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        if (! in_array($ext, self::ALLOWED, true)) {
            throw new \InvalidArgumentException('Unsupported file type.');
        }

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $base = Str::limit($base, 60, '').'-'.Str::lower(Str::random(6));
        $dir = 'uploads/'.date('Y/m');
        $abs = public_path($dir);
        if (! is_dir($abs)) {
            mkdir($abs, 0755, true);
        }

        $name = "$base.$ext";
        $file->move($abs, $name);
        $original = "$dir/$name";
        $path = $original;
        [$w, $h] = @getimagesize(public_path($original)) ?: [null, null];

        // Generate a WebP version for JPG/PNG when possible (smaller, faster).
        if (in_array($ext, ['jpg', 'jpeg', 'png'], true) && function_exists('imagewebp')) {
            $webp = $this->toWebp(public_path($original), "$abs/$base.webp", $ext);
            if ($webp) {
                $path = "$dir/$base.webp";
            }
        }

        return Media::create([
            'file_name' => $name,
            'path' => $path,
            'original_path' => $original,
            'mime_type' => $file->getClientMimeType(),
            'size' => @filesize(public_path($path)) ?: 0,
            'width' => $w,
            'height' => $h,
            'title' => $meta['title'] ?? Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
            'alt' => $meta['alt'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);
    }

    protected function toWebp(string $src, string $dest, string $ext): bool
    {
        try {
            $img = $ext === 'png' ? @imagecreatefrompng($src) : @imagecreatefromjpeg($src);
            if (! $img) {
                return false;
            }
            if ($ext === 'png') {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            // Downscale very large images to 2000px wide.
            if (imagesx($img) > 2000) {
                $scaled = imagescale($img, 2000);
                imagedestroy($img);
                $img = $scaled;
            }
            $ok = imagewebp($img, $dest, 82);
            imagedestroy($img);

            return $ok && filesize($dest) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Delete a media record and its files. */
    public function delete(Media $media): void
    {
        foreach (array_unique(array_filter([$media->path, $media->original_path])) as $p) {
            $abs = public_path($p);
            if (str_starts_with($p, 'uploads/') && is_file($abs)) {
                @unlink($abs);
            }
        }
        $media->delete();
    }
}
