@php
    $c = $mutatie->company;
    $isVrijwaring = $mutatie->type === 'vrijwaring';
    $titel = $isVrijwaring ? 'Vrijwaringsbewijs' : 'Bevestiging uit bedrijfsvoorraad';
    $voertuig = $mutatie->car ? trim(($mutatie->car->merk ?? '') . ' ' . ($mutatie->car->handelsbenaming ?? '')) : null;
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titel }} {{ $mutatie->kenteken }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; color: #1f2937; margin: 0; background: #f3f4f6; font-size: 13px; }
        .sheet { max-width: 760px; margin: 24px auto; background: #fff; padding: 48px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .top { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 36px; }
        .brand { font-size: 18px; font-weight: 800; color: #0F9B9F; }
        .muted { color: #6b7280; font-size: 12px; line-height: 1.6; white-space: pre-line; }
        h1 { font-size: 24px; margin: 0 0 4px; letter-spacing: .04em; }
        .meta { font-size: 12px; color: #6b7280; }
        .meta strong { color: #111827; }
        table.spec { width: 100%; border-collapse: collapse; margin: 8px 0 24px; }
        table.spec td { padding: 9px 6px; border-bottom: 1px solid #f1f3f5; }
        table.spec td.k { color: #6b7280; width: 40%; }
        table.spec td.val { color: #111827; font-weight: 600; }
        .statement { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 10px; padding: 16px 18px; color: #115e59; line-height: 1.7; }
        .foot { margin-top: 40px; font-size: 11px; color: #9ca3af; }
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
                <div class="brand">{{ $c->name }}</div>
                <div class="muted">{{ trim(($c->address ?? '') . "\n" . trim(($c->postal_code ?? '') . ' ' . ($c->city ?? ''))) }}</div>
                <div class="muted">@if($c->btw_number)BTW: {{ $c->btw_number }}@endif @if($c->kvk_number) · KvK: {{ $c->kvk_number }}@endif</div>
            </div>
            <div style="text-align:right">
                <h1>{{ mb_strtoupper($titel) }}</h1>
                <div class="meta"><strong>{{ $mutatie->vrijwaringsbewijs ?: $mutatie->referentie }}</strong></div>
                <div class="meta">Datum: {{ ($mutatie->bewijs_datum ?: $mutatie->created_at)->format('d-m-Y') }}</div>
            </div>
        </div>

        <table class="spec">
            <tr><td class="k">Kenteken</td><td class="val">{{ $mutatie->kenteken }}</td></tr>
            @if($voertuig)<tr><td class="k">Voertuig</td><td class="val">{{ $voertuig }}</td></tr>@endif
            @if($mutatie->vrijwaringsbewijs)<tr><td class="k">Vrijwaringsbewijsnummer</td><td class="val">{{ $mutatie->vrijwaringsbewijs }}</td></tr>@endif
            @if($mutatie->referentie)<tr><td class="k">RDW-kenmerk</td><td class="val">{{ $mutatie->referentie }}</td></tr>@endif
            <tr><td class="k">Datum</td><td class="val">{{ ($mutatie->bewijs_datum ?: $mutatie->created_at)->format('d-m-Y H:i') }}</td></tr>
        </table>

        <div class="statement">
            @if($isVrijwaring)
                Hierbij wordt bevestigd dat bovengenoemd voertuig op de vermelde datum is opgenomen in de bedrijfsvoorraad van {{ $c->name }}. De vorige kentekenhouder is hiermee gevrijwaard van de tenaamstelling en de daaruit voortvloeiende verplichtingen (houderschapsbelasting, verzekeringsplicht en verkeersboetes) vanaf dat moment.
            @else
                Hierbij wordt bevestigd dat bovengenoemd voertuig op de vermelde datum uit de bedrijfsvoorraad van {{ $c->name }} is gegeven en op naam van de nieuwe eigenaar is gesteld.
            @endif
        </div>

        <div class="foot">
            @if($mutatie->mode !== 'soap')
                Let op: dit document is aangemaakt in testmodus (sandbox) en is niet door de RDW verwerkt.
            @else
                Geregistreerd bij de RDW.
            @endif
        </div>
    </div>
</body>
</html>
