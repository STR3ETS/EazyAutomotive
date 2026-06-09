<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\Feed\CarFeedService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public, API-key-addressed inventory feeds that external portals pull.
 * No session auth; the company is resolved by its api_key in the URL.
 */
class FeedController extends Controller
{
    public function __construct(private CarFeedService $feed) {}

    /** Full inventory as XML. */
    public function cars(string $apiKey): Response
    {
        $company = $this->resolveCompany($apiKey);
        $xml = $this->feed->toXml($company, $this->feed->vehicles($company));

        return $this->xmlResponse($xml);
    }

    /** Full inventory as CSV (download). */
    public function carsCsv(string $apiKey): Response
    {
        $company = $this->resolveCompany($apiKey);
        $csv = $this->feed->toCsv($this->feed->vehicles($company));

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="voorraad.csv"',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    /** Per-platform feed (XML): only cars published to that platform. */
    public function platform(string $apiKey, string $platform): Response
    {
        $config = config("platforms.{$platform}");

        if (!$config || ($config['delivery'] ?? null) !== 'feed') {
            abort(404);
        }

        $company = $this->resolveCompany($apiKey);
        $xml = $this->feed->toXml($company, $this->feed->vehicles($company, $platform), $platform);

        return $this->xmlResponse($xml);
    }

    private function resolveCompany(string $apiKey): Company
    {
        return Company::where('api_key', $apiKey)
            ->where('is_active', true)
            ->firstOr(fn () => abort(404));
    }

    private function xmlResponse(string $xml): Response
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
