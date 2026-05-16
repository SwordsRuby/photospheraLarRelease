<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Illuminate\Support\Facades\Log;

class WatermarkService
{
    protected ImageManager $imageManager;
    protected string $cachePath;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
        $this->cachePath = storage_path('app/public/watermarked');
        
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    public function canDownloadOriginal(): bool
    {
        return auth()->check();
    }

    public function getWatermarkedImage(string $originalPath): string
    {
        if ($this->canDownloadOriginal()) {
            return $originalPath;
        }

        $cacheKey = 'watermarked_' . md5($originalPath);
        $cachedPath = $this->cachePath . '/' . $cacheKey . '.jpg';
        
        if (file_exists($cachedPath)) {
            return '/storage/watermarked/' . $cacheKey . '.jpg';
        }

        $watermarked = $this->applyWatermark($originalPath, $cachedPath);
        
        return $watermarked;
    }

    protected function applyWatermark(string $originalPath, string $outputPath): string
    {
        try {
            $fullPath = $this->getFullPath($originalPath);
            
            if (!file_exists($fullPath)) {
                return $originalPath;
            }

            $image = $this->imageManager->read($fullPath);
            
            $width = $image->width();
            $height = $image->height();
            
            $fontSize = min($width, $height) / 12;
            $fontSize = max(28, min($fontSize, 100));
            
            $fontPath = public_path('fonts/arial.ttf');
            
            if (!file_exists($fontPath)) {
                $fontPath = null;
            }
            
            $angle = rad2deg(atan2($height, $width));
            
            $centerX = $width / 2;
            $centerY = $height / 2;
            
            $watermarkText = '© Фотосфера';
            
            $image->text(
                $watermarkText,
                $centerX,
                $centerY,
                function ($font) use ($fontSize, $fontPath, $angle) {
                    if ($fontPath) {
                        $font->filename($fontPath);
                    }
                    $font->size($fontSize);
                    $font->color('rgba(255, 255, 255, 0.45)');
                    $font->align('center');
                    $font->valign('middle');
                    $font->angle($angle);
                }
            );
            
            $encoder = new JpegEncoder(quality: 85);
            $encoded = $image->encode($encoder);
            file_put_contents($outputPath, $encoded);
            
            return '/storage/watermarked/' . basename($outputPath);
            
        } catch (\Exception $e) {
            Log::error('Watermark failed: ' . $e->getMessage());
            return $originalPath;
        }
    }

    protected function getFullPath(string $path): string
    {
        $relativePath = str_replace('/storage/', '', $path);
        
        $fullPath = storage_path('app/public/' . $relativePath);
        
        if (file_exists($fullPath)) {
            return $fullPath;
        }
        
        $publicPath = public_path($path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }
        
        return $path;
    }

    public function downloadImage(string $imagePath, string $imageName)
    {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $filename = $imageName . '.' . $extension;
        
        if ($this->canDownloadOriginal()) {
            $fullPath = $this->getFullPath($imagePath);
            return response()->download($fullPath, $filename);
        }
        
        $watermarkedPath = $this->getWatermarkedImage($imagePath);
        $fullPath = $this->getFullPath($watermarkedPath);
        
        return response()->download($fullPath, 'watermarked_' . $filename);
    }
}