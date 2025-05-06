<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Handle file uploads.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $storagePath The storage path (e.g., 'aircraft', 'car')
     * @return array
     */
    public function createFiles($request, $storagePath)
    {
        $fileData = $this->handleFilesUpload($request, $storagePath);

        return $fileData;
    }

    /**
     * Handle file updates.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $storagePath The storage path (e.g., 'aircraft', 'car')
     * @return array
     */
    public function updateFiles($request, $storagePath)
    {
        $fileData = $this->handleFilesUpload($request, $storagePath);

        // Remove files from storage if there are removed files during the save
        if ($request->removed_files) {
            foreach ($request->removed_files as $file) {
                $filePath = $storagePath . '/' . $file;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        }

        return $fileData;
    }

    public function handleFilesUpload($request, $storagePath)
    {
        $fileData = [];

        // Bla-bla-bla
        // Handle file uploads, insert new files and remove old (files deleted by user) files
        // I am ommiting the code for brevity

        return $fileData;
    }
}
