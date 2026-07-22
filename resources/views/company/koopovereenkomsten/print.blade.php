@use('App\Models\Koopovereenkomst')
@php
    $c = $koopovereenkomst->company;
    $koper = $koopovereenkomst->koper ?? [];
    $v = $koopovereenkomst->voertuig ?? [];
    $isBtw = $koopovereenkomst->btw_type === 'btw';
    $prijs = (int) $koopovereenkomst->verkoopprijs;
    $excl = $isBtw ? (int) round($prijs / 1.21) : $prijs;
    $btw = $prijs - $excl;
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Koopovereenkomst {{ $koopovereenkomst->nummer }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; color: #1f2937; margin: 0; background: #f3f4f6; font-size: 13px; }
        .sheet { max-width: 820px; margin: 24px auto; background: #fff; padding: 48px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .top { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 32px; }
        .brand { font-size: 18px; font-weight: 800; color: #0F9B9F; }
        .muted { color: #6b7280; font-size: 12px; line-height: 1.6; white-space: pre-line; }
        h1 { font-size: 24px; margin: 0 0 4px; letter-spacing: .04em; }
        .meta { font-size: 12px; color: #6b7280; }
        .meta strong { color: #111827; }
        .cols { display: flex; gap: 24px; margin-bottom: 24px; }
        .col { flex: 1; }
        .label { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin: 0 0 6px; }
        .box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; }
        .box .name { font-weight: 700; color: #111827; }
        table.spec { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.spec td { padding: 7px 6px; border-bottom: 1px solid #f1f3f5; vertical-align: top; }
        table.spec td.k { color: #6b7280; width: 38%; }
        table.spec td.val { color: #111827; font-weight: 600; }
        .totals { width: 320px; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals .grand { border-top: 2px solid #e5e7eb; margin-top: 6px; padding-top: 8px; font-weight: 800; font-size: 16px; }
        .note { margin-top: 14px; font-size: 12px; color: #6b7280; }
        .section { margin-top: 26px; }
        .section h3 { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; margin: 0 0 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        .sign { display: flex; gap: 40px; margin-top: 48px; }
        .sign .s { flex: 1; }
        .sign .line { border-top: 1px solid #9ca3af; margin-top: 46px; padding-top: 6px; font-size: 11px; color: #6b7280; }
        .terms { font-size: 11px; color: #6b7280; line-height: 1.6; }
        .printbar { text-align: center; margin: 16px; }
        .printbar button { padding: 10px 22px; border: 0; border-radius: 9999px; background: #0F9B9F; color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; }
        @media print { body { background: #fff; } .sheet { box-shadow: none; margin: 0; max-width: none; padding: 24px; } .printbar { display: none; } }
    </style>
</head>
<body>
    <div class="printbar"><button onclick="window.print()">Printen / opslaan als PDF</button></div>
    <div class="sheet">
        <div class="top">
            <div>
                <div class="brand">{{ $c->name }}</div>
                <div class="muted">{{ trim(($c->address ?? '') . "\n" . trim(($c->postal_code ?? '') . ' ' . ($c->city ?? ''))) }}</div>
                <div class="muted">@if($c->phone){{ $c->phone }}@endif @if($c->email) · {{ $c->email }}@endif</div>
                <div class="muted">@if($c->btw_number)BTW: {{ $c->btw_number }}@endif @if($c->kvk_number) · KvK: {{ $c->kvk_number }}@endif</div>
            </div>
            <div style="text-align:right">
                <h1>KOOPOVEREENKOMST</h1>
                <div class="meta"><strong>{{ $koopovereenkomst->nummer }}</strong></div>
                <div class="meta">Datum: {{ $koopovereenkomst->created_at->format('d-m-Y') }}</div>
            </div>
        </div>

        <div class="cols">
            <div class="col">
                <p class="label">Verkoper</p>
                <div class="box">
                    <div class="name">{{ $c->name }}</div>
                    <div class="muted">{{ trim(($c->address ?? '') . "\n" . trim(($c->postal_code ?? '') . ' ' . ($c->city ?? ''))) }}</div>
                </div>
            </div>
            <div class="col">
                <p class="label">Koper</p>
                <div class="box">
                    <div class="name">{{ $koper['naam'] ?? '' }}@if(!empty($koper['bedrijfsnaam'])) &middot; {{ $koper['bedrijfsnaam'] }}@endif</div>
                    <div class="muted">{{ trim(($koper['adres'] ?? '') . "\n" . trim(($koper['postcode'] ?? '') . ' ' . ($koper['plaats'] ?? ''))) }}</div>
                    <div class="muted">@if(!empty($koper['telefoon'])){{ $koper['telefoon'] }}@endif @if(!empty($koper['email'])) · {{ $koper['email'] }}@endif</div>
                    @if(!empty($koper['btw']))<div class="muted">BTW: {{ $koper['btw'] }}</div>@endif
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Voertuig</h3>
            <table class="spec">
                <tr><td class="k">Kenteken</td><td class="val">{{ $v['kenteken'] ?? '-' }}</td><td class="k">Merk &amp; model</td><td class="val">{{ trim(($v['merk'] ?? '') . ' ' . ($v['model'] ?? '')) ?: '-' }}</td></tr>
                <tr><td class="k">Bouwjaar</td><td class="val">{{ $v['bouwjaar'] ?? '-' }}</td><td class="k">Kilometerstand</td><td class="val">{{ isset($v['kilometerstand']) ? number_format((int) $v['kilometerstand'], 0, ',', '.') . ' km' : '-' }}</td></tr>
                <tr><td class="k">Brandstof</td><td class="val">{{ $v['brandstof'] ?? '-' }}</td><td class="k">Kleur</td><td class="val">{{ $v['kleur'] ?? '-' }}</td></tr>
                <tr><td class="k">Chassisnummer (VIN)</td><td class="val" colspan="3">{{ $v['chassisnummer'] ?? '-' }}</td></tr>
            </table>
        </div>

        <div class="section">
            <h3>Prijs</h3>
            <div class="totals">
                @if($isBtw)
                    <div><span>Prijs excl. BTW</span><span>{{ Koopovereenkomst::euro($excl) }}</span></div>
                    <div><span>BTW 21%</span><span>{{ Koopovereenkomst::euro($btw) }}</span></div>
                    <div><span>Verkoopprijs incl. BTW</span><span>{{ Koopovereenkomst::euro($prijs) }}</span></div>
                @else
                    <div><span>Verkoopprijs</span><span>{{ Koopovereenkomst::euro($prijs) }}</span></div>
                @endif
                @if($koopovereenkomst->inruil_bedrag)
                    <div><span>Inruil{{ $koopovereenkomst->inruil_omschrijving ? ' (' . $koopovereenkomst->inruil_omschrijving . ')' : '' }}</span><span>- {{ Koopovereenkomst::euro($koopovereenkomst->inruil_bedrag) }}</span></div>
                @endif
                <div class="grand"><span>Te betalen</span><span>{{ Koopovereenkomst::euro($koopovereenkomst->teBetalen()) }}</span></div>
            </div>
            <div class="note">
                @if($isBtw)
                    De genoemde prijs is inclusief 21% BTW.
                @else
                    Deze auto wordt verkocht onder de margeregeling. De BTW is in de prijs begrepen en niet als zodanig verrekenbaar.
                @endif
            </div>
        </div>

        <div class="section">
            <h3>Levering &amp; garantie</h3>
            <table class="spec">
                <tr><td class="k">Leverdatum</td><td class="val">{{ $koopovereenkomst->leverdatum ? $koopovereenkomst->leverdatum->format('d-m-Y') : 'In overleg' }}</td></tr>
                <tr><td class="k">Garantie</td><td class="val">{{ $koopovereenkomst->garantie ?: 'Conform wettelijke bepalingen' }}</td></tr>
            </table>
            @if($koopovereenkomst->bijzonderheden)
                <p class="label" style="margin-top:14px">Bijzonderheden</p>
                <div class="muted" style="color:#374151">{{ $koopovereenkomst->bijzonderheden }}</div>
            @endif
        </div>

        <div class="section">
            <h3>Bepalingen</h3>
            <p class="terms">De koper verklaart het voertuig te kopen in de staat waarin het zich bevindt, met de hierboven vermelde kilometerstand. Het voertuig blijft eigendom van de verkoper tot de volledige koopsom is voldaan. De koper heeft gelegenheid gehad het voertuig te (laten) inspecteren. Op deze overeenkomst is Nederlands recht van toepassing.</p>
        </div>

        <div class="sign">
            <div class="s">
                <div class="line">Handtekening verkoper</div>
                <div class="muted" style="margin-top:8px">{{ $c->name }}</div>
            </div>
            <div class="s">
                <div class="line">Handtekening koper</div>
                <div class="muted" style="margin-top:8px">{{ $koper['naam'] ?? '' }}</div>
            </div>
        </div>
        <div class="muted" style="margin-top:24px">Plaats: {{ $c->city ?: '________________' }} &nbsp;&nbsp; Datum: ________________</div>
    </div>
</body>
</html>
