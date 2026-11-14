<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Http\Request;

class MediaController extends AdminController
{
    public function index(Request $request)
    {
        $media = Media::when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('alt', 'like', "%$s%")->orWhere('file_name', 'like', "%$s%")))
            ->latest()->paginate(24)->withQueryString();

        return view('admin.media.index', compact('media'));
    }

    /** JSON list for the image picker modal. */
    public function picker(Request $request)
    {
        $media = Media::when($request->q, fn ($q, $s) => $q->where('title', 'like', "%$s%")->orWhere('alt', 'like', "%$s%"))
            ->latest()->limit(60)->get(['id', 'path', 'title', 'alt']);

        return response()->json($media->map(fn ($m) => ['id' => $m->id, 'path' => $m->path, 'url' => asset($m->path), 'title' => $m->title, 'alt' => $m->alt]));
    }

    public function store(Request $request, ImageService $images)
    {
        $request->validate(['files' => ['required', 'array', 'max:20'], 'files.*' => ImageService::rules(true)]);
        $created = [];
        foreach ($request->file('files') as $file) {
            $created[] = $images->upload($file);
        }
        $this->log('upload', 'Uploaded '.count($created).' file(s)');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'items' => collect($created)->map(fn ($m) => ['id' => $m->id, 'path' => $m->path, 'url' => asset($m->path), 'title' => $m->title])]);
        }

        return back()->with('success', count($created).' file(s) uploaded. Add alt text for each image.');
    }

    public function update(Request $request, Media $media)
    {
        $media->update($request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return $request->expectsJson() ? response()->json(['success' => true]) : back()->with('success', 'Media details saved.');
    }

    public function destroy(Media $media, ImageService $images)
    {
        $images->delete($media);
        $this->log('delete', "Deleted media {$media->file_name}");

        return back()->with('success', 'File deleted.');
    }
}
