<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

/**
 * Stitches several short clips (the per-photo videos) into one reel using ffmpeg.
 * Clips are scaled/padded to a common size and frame rate, then joined with a
 * short crossfade between each. If clip durations can't be read (no ffprobe), it
 * falls back to a plain hard-cut concatenation.
 */
class VideoStitcher
{
    private const WIDTH = 1280;
    private const HEIGHT = 720;
    private const FPS = 24;
    private const XFADE = 0.5; // crossfade duration in seconds

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
            $files = $this->download($clipUrls, $workDir);

            $outDir = dirname($outputPath);
            if (! is_dir($outDir)) {
                mkdir($outDir, 0775, true);
            }

            // Prefer crossfade transitions; fall back to a hard-cut concat.
            $durations = $this->durations($files);
            $usedCrossfade = $durations !== null;
            $args = $usedCrossfade
                ? $this->crossfadeArgs($files, $durations, $outputPath)
                : $this->concatArgs($files, $outputPath);

            $result = Process::timeout(600)->run($args);

            if (($this->failed($result, $outputPath)) && $usedCrossfade) {
                // Crossfade can be finicky; retry with a plain concat.
                $result = Process::timeout(600)->run($this->concatArgs($files, $outputPath));
            }

            if ($this->failed($result, $outputPath)) {
                $err = trim($result->errorOutput()) ?: trim($result->output());
                throw new \RuntimeException('ffmpeg kon de video niet samenvoegen: ' . mb_substr($err, -300));
            }
        } finally {
            $this->cleanup($workDir);
        }
    }

    private function failed($result, string $outputPath): bool
    {
        return ! $result->successful() || ! is_file($outputPath) || filesize($outputPath) === 0;
    }

    /**
     * @param  list<string>  $urls
     * @return list<string>
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

    /**
     * Hard-cut concatenation (robust fallback).
     *
     * @param  list<string>  $files
     * @return list<string>
     */
    private function concatArgs(array $files, string $outputPath): array
    {
        $n = count($files);
        $parts = [];
        $labels = '';
        for ($i = 0; $i < $n; $i++) {
            $parts[] = "[{$i}:v]" . $this->normalize() . "[v{$i}]";
            $labels .= "[v{$i}]";
        }
        $parts[] = $labels . "concat=n={$n}:v=1:a=0[outv]";

        return $this->encodeArgs($files, implode(';', $parts), $outputPath);
    }

    /**
     * Crossfade between each clip using xfade with cumulative offsets.
     *
     * @param  list<string>  $files
     * @param  list<float>  $durations
     * @return list<string>
     */
    private function crossfadeArgs(array $files, array $durations, string $outputPath): array
    {
        $n = count($files);
        $d = self::XFADE;
        $parts = [];
        for ($i = 0; $i < $n; $i++) {
            $parts[] = "[{$i}:v]" . $this->normalize() . "[v{$i}]";
        }

        $last = '[v0]';
        $cum = $durations[0];
        for ($i = 1; $i < $n; $i++) {
            $offset = max(0.1, $cum - $i * $d);
            $out = ($i === $n - 1) ? '[outv]' : "[x{$i}]";
            $parts[] = "{$last}[v{$i}]xfade=transition=fade:duration={$d}:offset="
                . number_format($offset, 3, '.', '') . $out;
            $last = $out;
            $cum += $durations[$i];
        }

        return $this->encodeArgs($files, implode(';', $parts), $outputPath);
    }

    private function normalize(): string
    {
        $w = self::WIDTH;
        $h = self::HEIGHT;
        $fps = self::FPS;

        return "scale={$w}:{$h}:force_original_aspect_ratio=decrease,"
            . "pad={$w}:{$h}:(ow-iw)/2:(oh-ih)/2,setsar=1,fps={$fps},format=yuv420p";
    }

    /**
     * @param  list<string>  $files
     * @return list<string>
     */
    private function encodeArgs(array $files, string $filter, string $outputPath): array
    {
        $args = [$this->binary(), '-y'];
        foreach ($files as $file) {
            $args[] = '-i';
            $args[] = $file;
        }
        $args[] = '-filter_complex';
        $args[] = $filter;
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

        return $args;
    }

    /**
     * @param  list<string>  $files
     * @return list<float>|null  Per-clip duration in seconds, or null if unavailable.
     */
    private function durations(array $files): ?array
    {
        $probe = $this->probeBinary();
        if ($probe === null) {
            return null;
        }

        $durations = [];
        foreach ($files as $file) {
            try {
                $r = Process::timeout(30)->run([
                    $probe, '-v', 'error',
                    '-show_entries', 'format=duration',
                    '-of', 'default=noprint_wrappers=1:nokey=1',
                    $file,
                ]);
            } catch (\Throwable $e) {
                return null;
            }

            if (! $r->successful()) {
                return null;
            }
            $d = (float) trim($r->output());
            if ($d <= 0) {
                return null;
            }
            $durations[] = $d;
        }

        return $durations;
    }

    private function probeBinary(): ?string
    {
        $ff = $this->binary();
        if ($ff === 'ffmpeg' || $ff === '') {
            return 'ffprobe';
        }

        $dir = dirname($ff);
        foreach (['ffprobe.exe', 'ffprobe'] as $name) {
            $candidate = $dir . DIRECTORY_SEPARATOR . $name;
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
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
