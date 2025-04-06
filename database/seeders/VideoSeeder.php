<?php

namespace Database\Seeders;

use App\Models\Video;
use DivisionByZeroError;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::truncate('videos');
        $videos = [
            [
                'title' => 'Introduction to Vue.js',
                'description' => 'Learn the basics of Vue.js.',
                'video_url' => 'sample_video.mp4',
            ],
            [
                'title' => 'Laravel Inertia.js Guide',
                'description' => 'Understanding Inertia.js in Laravel.',
                'video_url' => 'sample_video.mp4',
            ],
            [
                'title' => 'Mastering Tailwind CSS',
                'description' => 'A deep dive into Tailwind CSS.',
                'video_url' => 'sample_video.mp4',
            ],
            [
                'title' => 'Understanding JavaScript Promises',
                'description' => 'A comprehensive guide to JavaScript promises.',
                'video_url' => 'sample_video.mp4',
            ],
            [
                'title' => 'REST API Best Practices',
                'description' => 'Learn how to structure RESTful APIs.',
                'video_url' => 'sample_video.mp4',
            ],
            [
                'title' => 'Getting Started with TypeScript',
                'description' => 'An introduction to TypeScript for JavaScript developers.',
                'video_url' => 'sample_video.mp4',
            ],
        ];

        foreach ($videos as $video) {
            Video::create($video);
        }
    }
}
