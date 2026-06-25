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
        $blur = function_exists('imagefilter');

        $canvas = imagecreatetruecolor($w, $h);
        imagealphablending($canvas, true);

        // Studio cove: a lighter wall up top, a slightly deeper floor band below the horizon.
        $floorY = (int) round($h * 0.70);
        for ($y = 0; $y < $h; $y++) {
            if ($y < $floorY) {
                $shade = (int) round(252 - ($y / max($floorY, 1)) * 7);
            } else {
                $t = ($y - $floorY) / max($h - $floorY, 1);
                $shade = (int) round(236 - $t * 14);
            }
            imageline($canvas, 0, $y, $w, $y, imagecolorallocate($canvas, $shade, $shade, $shade));
        }

        // --- Logo as a branded wall behind the car (the car will overlap its lower part). ---
        $logo = @imagecreatefromstring($logoBytes);
        if ($logo !== false) {
            imagealphablending($logo, true);
            $lw = imagesx($logo);
            $lh = imagesy($logo);
            if ($lw > 0 && $lh > 0) {
                $lW = (int) round($w * 0.40);
                $lH = (int) round($lh * ($lW / $lw));
                $maxLH = (int) round($h * 0.46);
                if ($lH > $maxLH) {
                    $lH = $maxLH;
                    $lW = (int) round($lw * ($lH / $lh));
                }
                $lX = (int) round(($w - $lW) / 2);
                $lY = (int) round($h * 0.12);
                imagecopyresampled($canvas, $logo, $lX, $lY, 0, 0, $lW, $lH, $lw, $lh);
            }
            imagedestroy($logo);
        }

        // --- Car cut-out: trim the transparent padding so the real wheels sit on the floor. ---
        $car = @imagecreatefromstring($carPng);
        if ($car === false) {
            imagedestroy($canvas);
            throw new \RuntimeException('Kon de auto-uitsnede niet verwerken.');
        }
        $trimmed = $this->trimTransparent($car);
        if ($trimmed !== $car) {
            imagedestroy($car);
            $car = $trimmed;
        }
        imagealphablending($car, true);
        $cw = imagesx($car);
        $ch = imagesy($car);
        $cW = (int) round($w * 0.74);
        $cH = (int) round($ch * ($cW / $cw));
        $maxCH = (int) round($h * 0.50);
        if ($cH > $maxCH) {
            $cH = $maxCH;
            $cW = (int) round($cw * ($cH / $ch));
        }
        $cX = (int) round(($w - $cW) / 2);
        $baseY = (int) round($h * 0.86);
        $cY = $baseY - $cH;

        // Soft contact shadow right under the wheels so the car is grounded.
        $shadow = imagecreatetruecolor($w, $h);
        imagealphablending($shadow, false);
        imagesavealpha($shadow, true);
        imagefill($shadow, 0, 0, imagecolorallocatealpha($shadow, 0, 0, 0, 127));
        imagefilledellipse($shadow, (int) ($cX + $cW / 2), $baseY, (int) ($cW * 0.86), (int) ($h * 0.06), imagecolorallocatealpha($shadow, 0, 0, 0, 80));
        if ($blur) {
            for ($b = 0; $b < 9; $b++) {
                imagefilter($shadow, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }
        imagealphablending($canvas, true);
        imagecopy($canvas, $shadow, 0, 0, 0, 0, $w, $h);
        imagedestroy($shadow);

        imagecopyresampled($canvas, $car, $cX, $cY, 0, 0, $cW, $cH, $cw, $ch);
        imagedestroy($car);

        ob_start();
        imagejpeg($canvas, null, 92);
        $bytes = (string) ob_get_clean();
        imagedestroy($canvas);

        return $bytes;
    }

    /**
     * Crop the fully-transparent border off a cut-out so the subject (the car) is
     * tight to the edges. Returns a new image, or the original if nothing to trim.
     */
    private function trimTransparent(\GdImage $img): \GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $step = max(1, (int) floor(min($w, $h) / 500));

        $minX = $w;
        $minY = $h;
        $maxX = 0;
        $maxY = 0;
        $found = false;

        for ($y = 0; $y < $h; $y += $step) {
            for ($x = 0; $x < $w; $x += $step) {
                $alpha = (imagecolorat($img, $x, $y) >> 24) & 0x7F; // 0 opaque .. 127 transparent
                if ($alpha < 100) {
                    $minX = min($minX, $x);
                    $maxX = max($maxX, $x);
                    $minY = min($minY, $y);
                    $maxY = max($maxY, $y);
                    $found = true;
                }
            }
        }

        if (! $found || $maxX <= $minX || $maxY <= $minY) {
            return $img;
        }

        $pad = $step + 2;
        $minX = max(0, $minX - $pad);
        $minY = max(0, $minY - $pad);
        $maxX = min($w - 1, $maxX + $pad);
        $maxY = min($h - 1, $maxY + $pad);
        $cw = $maxX - $minX + 1;
        $ch = $maxY - $minY + 1;

        $out = imagecreatetruecolor($cw, $ch);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopy($out, $img, 0, 0, $minX, $minY, $cw, $ch);

        return $out;
    }
}
