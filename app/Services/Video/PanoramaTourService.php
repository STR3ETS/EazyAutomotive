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
            '-movflags', '+faststart',
            $outputPath,
        ]);

        if (! $result->successful()) {
            $err = trim($result->errorOutput() ?: $result->output());

            throw new \RuntimeException('ffmpeg kon de 360-tour niet maken: ' . mb_substr($err, -300));
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
