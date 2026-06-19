<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

/**
 * Stitches several short clips (the per-photo DoP videos) into one reel using
 * ffmpeg. Each clip is downloaded, scaled/padded to a common size and frame
 * rate, then concatenated. Runs synchronously; the clips are short so this
 * completes within a request.
 */
class VideoStitcher
{
    private const WIDTH = 1280;
    private const HEIGHT = 720;

    public function isAvailable(): bool
    {
        try {
            return Process::timeout(20)->run([$this->binary(), '-version'])->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param  list<string>  $clipUrls  Public URLs of the source clips, in order.
     * @param  string  $outputPath  Absolute filesystem path for the resulting mp4.
     *
     * @throws \RuntimeException on any failure.
     */
    public function stitch(array $clipUrls, string $outputPath): void
    {
        $clipUrls = array_values(array_filter($clipUrls));
        if (count($clipUrls) < 2) {
            throw new \RuntimeException('Minstens twee clips nodig om samen te voegen.');
        }

        $workDir = storage_path('app/tmp/reel_' . bin2hex(random_bytes(6)));
        if (! is_dir($workDir) && ! mkdir($workDir, 0775, true) && ! is_dir($workDir)) {
            throw new \RuntimeException('Kon tijdelijke map niet aanmaken.');
        }

        try {
            $localFiles = $this->download($clipUrls, $workDir);

            $outDir = dirname($outputPath);
            if (! is_dir($outDir)) {
                mkdir($outDir, 0775, true);
            }

            $args = [$this->binary(), '-y'];
            foreach ($localFiles as $file) {
                $args[] = '-i';
                $args[] = $file;
            }
            $args[] = '-filter_complex';
            $args[] = $this->filter(count($localFiles));
            $args[] = '-map';
            $args[] = '[outv]';
            $args[] = '-c:v';
            $args[] = 'libx264';
            $args[] = '-preset';
            $args[] = 'veryfast';
            $args[] = '-pix_fmt';
            $args[] = 'yuv420p';
            $args[] = '-movflags';
            $args[] = '+faststart';
            $args[] = $outputPath;

            $result = Process::timeout(600)->run($args);

            if (! $result->successful() || ! is_file($outputPath) || filesize($outputPath) === 0) {
                $err = trim($result->errorOutput()) ?: trim($result->output());
                throw new \RuntimeException('ffmpeg kon de video niet samenvoegen: ' . mb_substr($err, -300));
            }
        } finally {
            $this->cleanup($workDir);
        }
    }

    /**
     * @param  list<string>  $urls
     * @return list<string>  Local file paths.
     */
    private function download(array $urls, string $workDir): array
    {
        $files = [];
        foreach ($urls as $i => $url) {
            $body = Http::timeout(60)->get($url)->body();
            if ($body === '' || strlen($body) < 1024) {
                throw new \RuntimeException('Een clip kon niet worden gedownload.');
            }
            $path = $workDir . DIRECTORY_SEPARATOR . 'clip' . $i . '.mp4';
            file_put_contents($path, $body);
            $files[] = $path;
        }

        return $files;
    }

    private function filter(int $n): string
    {
        $w = self::WIDTH;
        $h = self::HEIGHT;
        $parts = [];
        $labels = '';
        for ($i = 0; $i < $n; $i++) {
            $parts[] = "[{$i}:v]scale={$w}:{$h}:force_original_aspect_ratio=decrease,"
                . "pad={$w}:{$h}:(ow-iw)/2:(oh-ih)/2,setsar=1,fps=24,format=yuv420p[v{$i}]";
            $labels .= "[v{$i}]";
        }
        $parts[] = $labels . "concat=n={$n}:v=1:a=0[outv]";

        return implode(';', $parts);
    }

    private function cleanup(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($dir);
    }

    private function binary(): string
    {
        return (string) config('services.ffmpeg.path', 'ffmpeg');
    }
}
