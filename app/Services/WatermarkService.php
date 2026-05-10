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
            
            $fontSize = min($width, $height) / 10;
            $fontSize = max(24, min($fontSize, 80));
            $smallFontSize = $fontSize * 0.5;
            

            $fontPath = public_path('fonts/arial.ttf');
            
            if (!file_exists($fontPath)) {
                $fontPath = null;
            }
            
            $image->text(
                '© Фотосфера',
                30,  // X 
                40,  // Y 
                function ($font) use ($fontSize, $fontPath) {
                    if ($fontPath) {
                        $font->filename($fontPath);
                    }
                    $font->size($fontSize);
                    $font->color('rgba(255, 255, 255, 0.75)');
                    $font->align('left');
                    $font->valign('top');
                }
            );
            
            $image->text(
                '© Фотосфера',
                $width - 30,  // X 
                $height - 30, // Y 
                function ($font) use ($fontSize, $fontPath) {
                    if ($fontPath) {
                        $font->filename($fontPath);
                    }
                    $font->size($fontSize);
                    $font->color('rgba(255, 255, 255, 0.6)');
                    $font->align('right');
                    $font->valign('bottom');
                }
            );
            
            // Save
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