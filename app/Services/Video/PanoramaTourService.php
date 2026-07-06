<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Process;

/**
 * Maakt van een equirectangular 360-panoramafoto een soepele "rondkijk"-video,
 * volledig lokaal met ffmpeg (geen AI). De bol wordt teruggeprojecteerd naar een
 * normaal, recht camerabeeld (v360 e -> flat) dat vloeiend rondpant. Zo blijft
 * alles recht en onvervormd, precies zoals een vastgoed-360-tour.
 *
 * Truc: een yaw-rotatie is exact een horizontale, cyclische verschuiving van de
 * equirectangular. We plaatsen de foto dubbel naast elkaar en schuiven er een
 * venster ter grootte van de volledige 360 doorheen; daarna projecteert v360 dat
 * naar een recht beeld. Eén ffmpeg-commando, snel, naadloos loopend.
 */
class PanoramaTourService
{
    public function isAvailable(): bool
    {
        $bin = $this->binary();

        // Absoluut pad: bestand moet bestaan. Kale opdracht: aannemen dat het op PATH staat.
        return $bin !== ''
            && (! $this->looksLikePath($bin) || is_file($bin) || is_file($bin . '.exe'));
    }

    /**
     * Rendert de tour-video naar $outputPath.
     *
     * @param array{duration?:int,fov?:int,direction?:string,width?:int,height?:int} $opts
     */
    public function render(string $inputPath, string $outputPath, array $opts = []): void
    {
        $duration = max(4, min(30, (int) ($opts['duration'] ?? 12)));
        $fov = max(60, min(140, (int) ($opts['fov'] ?? 100)));
        $direction = ($opts['direction'] ?? 'right') === 'left' ? 'left' : 'right';
        $outW = (int) ($opts['width'] ?? 1280);
        $outH = (int) ($opts['height'] ?? 720);

        // Voortgang 0..W over de volledige duur (W = halve breedte na het verdubbelen).
        // De komma in mod() moet in de filtergraph geescaped worden als \,.
        $progress = "mod(t*(iw/2)/{$duration}\\,iw/2)";
        $x = $direction === 'left' ? "(iw/2 - {$progress})" : $progress;

        $vf = 'split[a][b];[a][b]hstack=inputs=2[s];'
            . "[s]crop=w=iw/2:h=ih:x={$x}:y=0[r];"
            . "[r]v360=input=e:output=flat:d_fov={$fov}:w={$outW}:h={$outH},format=yuv420p";

        $result = Process::timeout(240)->run([
            $this->binary(), '-y', '-hide_banner', '-loglevel', 'error',
            '-loop', '1', '-i', $inputPath,
            '-t', (string) $duration,
            '-vf', $vf,
            '-r', '30',
            '-an',
            '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '20',
            '-pix_fmt', 'yuv420p', // brede browser-compatibiliteit (Safari/iOS)
            '-movflags', '+faststart',
            $outputPath,
        ]);

        if (! $result->successful()) {
            $err = trim($result->errorOutput() ?: $result->output());

            throw new \RuntimeException('ffmpeg kon de 360-tour niet maken: ' . mb_substr($err, -300));
        }
    }

    /**
     * Maakt van meerdere 360-foto's (in kamer-volgorde) een doorlopende tour:
     * per foto een rondkijk-segment, met een crossfade als overgang naar de
     * volgende ruimte. Matterport-achtig: rondkijken en doorlopen.
     *
     * @param array<int, string> $inputPaths
     * @param array{duration?:int,fov?:int,direction?:string,crossfade?:float,width?:int,height?:int} $opts
     */
    public function renderTour(array $inputPaths, string $outputPath, array $opts = []): void
    {
        $inputs = array_values(array_filter($inputPaths, 'is_file'));
        if ($inputs === []) {
            throw new \RuntimeException('Geen geldige panoramafoto ontvangen.');
        }

        if (count($inputs) === 1) {
            $this->render($inputs[0], $outputPath, $opts);

            return;
        }

        $duration = max(4, min(30, (int) ($opts['duration'] ?? 8)));
        $crossfade = max(0.3, min(2.5, (float) ($opts['crossfade'] ?? 1.0)));
        $width = (int) ($opts['width'] ?? 1280);
        $height = (int) ($opts['height'] ?? 720);

        $segDir = dirname($outputPath) . DIRECTORY_SEPARATOR . 'seg_' . bin2hex(random_bytes(4));
        @mkdir($segDir, 0775, true);

        $segments = [];
        try {
            foreach ($inputs as $i => $input) {
                $seg = $segDir . DIRECTORY_SEPARATOR . 'seg' . $i . '.mp4';
                $this->render($input, $seg, ['duration' => $duration] + $opts);
                $segments[] = $seg;
            }

            $this->crossfade($segments, $outputPath, $duration, $crossfade, $width, $height);
        } finally {
            foreach ($segments as $seg) {
                @unlink($seg);
            }
            @rmdir($segDir);
        }
    }

    /**
     * Voegt gelijk-lange segmenten samen met een crossfade tussen elk paar.
     *
     * @param array<int, string> $segments
     */
    private function crossfade(array $segments, string $outputPath, int $duration, float $crossfade, int $width, int $height): void
    {
        $count = count($segments);

        $cmd = [$this->binary(), '-y', '-hide_banner', '-loglevel', 'error'];
        foreach ($segments as $seg) {
            $cmd[] = '-i';
            $cmd[] = $seg;
        }

        // Ketting van xfade-filters. Alle segmenten zijn even lang, dus de
        // startoffset van de j-de overgang is j * (duur - crossfade).
        $filter = '';
        $prev = '[0:v]';
        for ($j = 1; $j < $count; $j++) {
            $offset = sprintf('%.3f', $j * ($duration - $crossfade));
            $label = $j === $count - 1 ? '[out]' : "[x{$j}]";
            $filter .= "{$prev}[{$j}:v]xfade=transition=fade:duration={$crossfade}:offset={$offset}{$label};";
            $prev = $label;
        }
        $filter = rtrim($filter, ';');

        $cmd = array_merge($cmd, [
            '-filter_complex', $filter,
            '-map', '[out]',
            '-r', '30',
            '-an',
            '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '20',
            '-pix_fmt', 'yuv420p', // brede browser-compatibiliteit (Safari/iOS)
            '-movflags', '+faststart',
            $outputPath,
        ]);

        $result = Process::timeout(300)->run($cmd);

        if (! $result->successful()) {
            $err = trim($result->errorOutput() ?: $result->output());

            throw new \RuntimeException('ffmpeg kon de tour niet samenvoegen: ' . mb_substr($err, -300));
        }
    }

    /** Een nette poster (still) uit de panoramafoto op ooghoogte. */
    public function poster(string $inputPath, string $outputPath, int $fov = 100): void
    {
        Process::timeout(60)->run([
            $this->binary(), '-y', '-hide_banner', '-loglevel', 'error',
            '-i', $inputPath,
            '-vf', "v360=input=e:output=flat:d_fov={$fov}:w=640:h=360",
            '-frames:v', '1',
            $outputPath,
        ]);
    }

    private function binary(): string
    {
        return (string) config('services.ffmpeg.path', 'ffmpeg');
    }

    private function looksLikePath(string $bin): bool
    {
        return str_contains($bin, '/') || str_contains($bin, '\\');
    }
}
