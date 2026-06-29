@php use App\Models\Invoice; @endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factuur {{ $invoice->number ?: 'concept' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; color: #1f2937; margin: 0; background: #f3f4f6; }
        .sheet { max-width: 800px; margin: 24px auto; background: #fff; padding: 48px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .top { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 40px; }
        .brand { font-size: 18px; font-weight: 800; color: #0F9B9F; }
        .muted { color: #6b7280; font-size: 12px; line-height: 1.6; white-space: pre-line; }
        h1 { font-size: 26px; margin: 0 0 4px; letter-spacing: .04em; }
        .meta { font-size: 12px; color: #6b7280; }
        .meta strong { color: #111827; }
        .billto-label { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 28px 0 8px; font-size: 13px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; border-bottom: 2px solid #e5e7eb; padding: 8px 6px; }
        td { padding: 9px 6px; border-bottom: 1px solid #f1f3f5; }
        .r { text-align: right; }
        .totals { width: 280px; margin-left: auto; font-size: 13px; }
        .totals div { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .grand { border-top: 2px solid #e5e7eb; margin-top: 6px; padding-top: 8px; font-weight: 800; font-size: 16px; }
        .note { margin-top: 24px; font-size: 12px; color: #6b7280; font-style: italic; }
        .foot { margin-top: 36px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; white-space: pre-line; }
        .printbar { text-align: center; margin: 16px; }
        .printbar button { padding: 10px 22px; border: 0; border-radius: 9999px; background: #0F9B9F; color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; }
        @media print { body { background: #fff; } .sheet { box-shadow: none; margin: 0; max-width: none; } .printbar { display: none; } }
    </style>
</head>
<body>
    <div class="printbar"><button onclick="window.print()">Printen / opslaan als PDF</button></div>
    <div class="sheet">
        <div class="top">
            <div>
                <div class="brand">{{ $invoice->company->name }}</div>
                <div class="muted">{{ trim(($invoice->company->address ?? '') . "\n" . trim(($invoice->company->postal_code ?? '') . ' ' . ($invoice->company->city ?? ''))) }}</div>
                <div class="muted">@if($invoice->company->btw_number)BTW: {{ $invoice->company->btw_number }}@endif @if($invoice->company->kvk_number) · KvK: {{ $invoice->company->kvk_number }}@endif</div>
            </div>
            <div style="text-align:right">
                <h1>FACTUUR</h1>
                <div class="meta"><strong>{{ $invoice->number ?: 'CONCEPT' }}</strong></div>
                <div class="meta">Datum: {{ $invoice->date->format('d-m-Y') }}</div>
                @if($invoice->due_date)<div class="meta">Vervaldatum: {{ $invoice->due_date->format('d-m-Y') }}</div>@endif
            </div>
        </div>

        <div class="billto-label">Factuur aan</div>
        <div class="muted" style="font-size:13px;color:#111827">{{ $invoice->bill_to_address ?: ($invoice->customer?->address_block ?? '') }}</div>

        <table>
            <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th class="r" style="width:60px">Aantal</th>
                    @if($invoice->vat_scheme === 'btw')<th class="r" style="width:60px">BTW</th>@endif
                    <th class="r" style="width:110px">Bedrag</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->lines as $line)
                    <tr>
                        <td>{{ $line->description }}</td>
                        <td class="r">{{ rtrim(rtrim(number_format($line->quantity, 2, ',', '.'), '0'), ',') }}</td>
                        @if($invoice->vat_scheme === 'btw')<td class="r">{{ $line->vat_rate }}%</td>@endif
                        <td class="r">{{ Invoice::eur($invoice->vat_scheme === 'marge' ? $line->line_total : (int) round($line->line_total * (1 + $line->vat_rate / 100))) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            @if($invoice->vat_scheme === 'btw')
                <div><span>Subtotaal</span><span>{{ Invoice::eur($invoice->subtotal) }}</span></div>
                <div><span>BTW</span><span>{{ Invoice::eur($invoice->vat_amount) }}</span></div>
            @endif
            <div class="grand"><span>Totaal</span><span>{{ Invoice::eur($invoice->total) }}</span></div>
        </div>

        @if($invoice->marge_note)<div class="note">{{ $invoice->marge_note }}</div>@endif
        @if($invoice->notes)<div class="note" style="font-style:normal;color:#374151">{{ $invoice->notes }}</div>@endif

        <div class="foot">@if($invoice->company->iban)Gelieve te betalen op {{ $invoice->company->iban }} o.v.v. {{ $invoice->number ?: 'het factuurnummer' }}.@endif
@if($invoice->company->invoice_footer){{ "\n" . $invoice->company->invoice_footer }}@endif</div>
    </div>
</body>
</html>
