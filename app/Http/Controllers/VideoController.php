<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Cache::remember('videos_with_ratings', now()->addMinutes(15), function () {
            return Video::select('id', 'title', 'description', 'video_url')
                ->withSum('rates', 'rate')
                ->get();
        });

        return Inertia::render('videos/Index', [
            'videos' => Inertia::defer(function () use ($videos) {
                return $videos;
            }),
        ]);
    }
    public function show(Video $video)
    {
        $video->loadSum('rates', 'rate');
        return Inertia::render('videos/Show', [
            'video' => $video
        ]);
    }

    public function rateVideo(Request $request, Video $video)
    {
        $validData = $request->validate([
           'rate' => 'required|numeric|min:1|max:5',
        ]);

        $video->rates()->create(array_merge(['video_id' => $video->id ], $validData));

        return back()->with('message', 'Thanks for rating the video!');
    }
}
