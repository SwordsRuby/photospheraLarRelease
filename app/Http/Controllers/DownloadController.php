<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\WatermarkService;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    protected WatermarkService $watermarkService;

    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }

    /**
     * Download image with watermark for guests.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(int $id)
    {
        $image = Image::findOrFail($id);
        
        if (!$image->is_approved) {
            abort(404, 'Image not available');
        }
        
        if ($image->is_private && auth()->id() !== $image->author_id) {
            abort(403, 'This image is private');
        }
        
        return $this->watermarkService->downloadImage($image->img, $image->name);
    }
}