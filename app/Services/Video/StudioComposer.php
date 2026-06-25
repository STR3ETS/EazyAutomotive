<?php

namespace App\Services\Video;

/**
 * Builds a branded studio still with GD: a clean white backdrop, the garage logo
 * on the backdrop, and the cut-out car in front. The result is a single frame
 * that gets animated, so the logo and background stay crisp and consistent.
 */
class StudioComposer
{
    public function isAvailable(): bool
    {
        return function_exists('imagecreatetruecolor') && function_exists('imagecreatefromstring');
    }

    /**
     * @param  string  $carPng  Transparent PNG of the car (background already removed).
     * @param  string  $logoBytes  The garage logo (PNG with transparency is ideal).
     * @return string  JPEG bytes of the composed studio frame.
     */
    public function compose(string $carPng, string $logoBytes, string $aspect = '16:9'): string
    {
        [$w, $h] = $aspect === '4:3' ? [1280, 960] : ($aspect === '9:16' ? [720, 1280] : [1280, 720]);

        $canvas = imagecreatetruecolor($w, $h);
        imagealphablending($canvas, true);
        // A very soft top-to-bottom grey so the white doek reads as a studio cove.
        for ($y = 0; $y < $h; $y++) {
            $shade = (int) round(255 - ($y / $h) * 14);
            $line = imagecolorallocate($canvas, $shade, $shade, $shade);
            imageline($canvas, 0, $y, $w, $y, $line);
        }

        // Logo on the backdrop: upper-centre, about a third of the width.
        $logo = @imagecreatefromstring($logoBytes);
        if ($logo !== false) {
            imagealphablending($logo, true);
            $lw = imagesx($logo);
            $lh = imagesy($logo);
            if ($lw > 0 && $lh > 0) {
                $targetW = (int) round($w * 0.32);
                $targetH = (int) round($lh * ($targetW / $lw));
                $lx = (int) round(($w - $targetW) / 2);
                $ly = (int) round($h * 0.09);
                imagecopyresampled($canvas, $logo, $lx, $ly, 0, 0, $targetW, $targetH, $lw, $lh);
            }
            imagedestroy($logo);
        }

        // Car cut-out in front: bottom-centre, fit to width then clamp height.
        $car = @imagecreatefromstring($carPng);
        if ($car === false) {
            imagedestroy($canvas);
            throw new \RuntimeException('Kon de auto-uitsnede niet verwerken.');
        }
        imagealphablending($car, true);
        $cw = imagesx($car);
        $ch = imagesy($car);
        $targetCW = (int) round($w * 0.84);
        $targetCH = (int) round($ch * ($targetCW / $cw));
        $maxCH = (int) round($h * 0.74);
        if ($targetCH > $maxCH) {
            $targetCH = $maxCH;
            $targetCW = (int) round($cw * ($targetCH / $ch));
        }
        $cx = (int) round(($w - $targetCW) / 2);
        $cy = (int) round($h - $targetCH - $h * 0.07);
        imagecopyresampled($canvas, $car, $cx, $cy, 0, 0, $targetCW, $targetCH, $cw, $ch);
        imagedestroy($car);

        ob_start();
        imagejpeg($canvas, null, 90);
        $bytes = (string) ob_get_clean();
        imagedestroy($canvas);

        return $bytes;
    }
}
