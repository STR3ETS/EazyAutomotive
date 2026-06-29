<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookkeepingController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        [$year, $quarter, $from, $to] = $this->period($request);

        $data = $this->compute($companyId, $from, $to);

        // Openstaand is a live figure, not period-bound.
        $data['openstaand'] = (int) Invoice::where('company_id', $companyId)
            ->whereIn('status', ['verzonden', 'deels_betaald', 'vervallen'])
            ->sum(\DB::raw('total - amount_paid'));

        return view('company.bookkeeping.index', array_merge($data, [
            'year' => $year,
            'quarter' => $quarter,
            'years' => range((int) now()->year, (int) now()->year - 4),
        ]));
    }

    public function export(Request $request)
    {
        $companyId = $request->user()->company_id;
        [$year, $quarter, $from, $to] = $this->period($request);
        $label = $quarter === 'jaar' ? $year : "{$year}-Q{$quarter}";

        $invoices = Invoice::where('company_id', $companyId)
            ->whereNotIn('status', ['concept', 'geannuleerd'])
            ->whereBetween('date', [$from, $to])
            ->with('customer')
            ->orderBy('date')->get();

        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')->get();

        $eur = fn ($cents) => number_format($cents / 100, 2, '.', '');

        return response()->streamDownload(function () use ($invoices, $expenses, $eur) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Datum', 'Soort', 'Nummer', 'Tegenpartij', 'Categorie/Regeling', 'Excl. BTW', 'BTW', 'Incl. BTW']);

            foreach ($invoices as $inv) {
                $excl = $inv->vat_scheme === 'marge' ? $inv->total : $inv->subtotal;
                $btw = $inv->vat_scheme === 'marge' ? 0 : $inv->vat_amount;
                fputcsv($out, [
                    $inv->date->format('Y-m-d'), 'Verkoop', $inv->number,
                    $inv->bill_to_name ?: ($inv->customer?->label ?? ''),
                    $inv->vat_scheme === 'marge' ? 'Margeregeling' : 'BTW',
                    $eur($excl), $eur($btw), $eur($inv->total),
                ]);
            }
            foreach ($expenses as $e) {
                fputcsv($out, [
                    $e->date->format('Y-m-d'), 'Inkoop', '', $e->supplier ?? '',
                    $e->category_label, $eur($e->amount_excl), $eur($e->vat_amount), $eur($e->amount_incl),
                ]);
            }
            fclose($out);
        }, "boekhouding-{$label}.csv", ['Content-Type' => 'text/csv']);
    }

    /** Resolve the selected year and quarter into a date range. */
    private function period(Request $request): array
    {
        $year = (int) $request->get('jaar', now()->year);
        $quarter = $request->get('kwartaal', (int) ceil(now()->month / 3));

        if ($quarter === 'jaar') {
            return [$year, 'jaar', Carbon::create($year, 1, 1)->startOfDay(), Carbon::create($year, 12, 31)->endOfDay()];
        }

        $quarter = max(1, min(4, (int) $quarter));
        $from = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
        $to = (clone $from)->addMonths(2)->endOfMonth();

        return [$year, $quarter, $from, $to];
    }

    /** Compute the VAT return and the financial figures for the period. */
    private function compute(int $companyId, Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::where('company_id', $companyId)
            ->whereNotIn('status', ['concept', 'geannuleerd'])
            ->whereBetween('date', [$from, $to])
            ->with('lines')->get();

        $omzetHoog = 0;
        $btwHoog = 0;
        $omzetLaag = 0;
        $btwLaag = 0;
        $omzetNul = 0;
        $margeGrondslag = 0;
        $verkoopTotaal = 0;

        foreach ($invoices as $inv) {
            $verkoopTotaal += (int) $inv->total;

            if ($inv->vat_scheme === 'marge') {
                $margeGrondslag += (int) $inv->margin_base;

                continue;
            }

            foreach ($inv->lines as $line) {
                $net = (int) $line->line_total;
                $btw = (int) round($net * $line->vat_rate / 100);
                if ($line->vat_rate == 21) {
                    $omzetHoog += $net;
                    $btwHoog += $btw;
                } elseif ($line->vat_rate == 9) {
                    $omzetLaag += $net;
                    $btwLaag += $btw;
                } else {
                    $omzetNul += $net;
                }
            }
        }

        // Margin scheme: VAT is owed over the gross margin (21/121 of the margin).
        $margeBtw = (int) round($margeGrondslag * 21 / 121);

        $voorbelasting = (int) Expense::where('company_id', $companyId)->whereBetween('date', [$from, $to])->sum('vat_amount');
        $kostenIncl = (int) Expense::where('company_id', $companyId)->whereBetween('date', [$from, $to])->sum('amount_incl');

        $verschuldigd = $btwHoog + $btwLaag + $margeBtw;

        return [
            'omzetHoog' => $omzetHoog, 'btwHoog' => $btwHoog,
            'omzetLaag' => $omzetLaag, 'btwLaag' => $btwLaag,
            'omzetNul' => $omzetNul,
            'margeGrondslag' => $margeGrondslag, 'margeBtw' => $margeBtw,
            'verschuldigd' => $verschuldigd,
            'voorbelasting' => $voorbelasting,
            'saldo' => $verschuldigd - $voorbelasting,
            'verkoopTotaal' => $verkoopTotaal,
            'kostenIncl' => $kostenIncl,
            'resultaat' => $verkoopTotaal - $kostenIncl,
        ];
    }
}
