(function () {
    'use strict';

    var SCRIPT = document.currentScript;
    var API_KEY = SCRIPT && SCRIPT.getAttribute('data-api-key');
    var BASE_URL = ((SCRIPT && SCRIPT.getAttribute('data-base-url')) || '').replace(/\/$/, '');
    var CONTAINER_ID = (SCRIPT && SCRIPT.getAttribute('data-container')) || 'eazy-taxatie';
    var CUSTOM_TITLE = (SCRIPT && SCRIPT.getAttribute('data-title')) || '';

    var GOOGLE_FONTS = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins'];
    var SYSTEM_FONT = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";

    // Kaart-schaduw uit de huisstijl. "none" is bewust een lichte schaduw.
    var SHADOWS = { none: '0 2px 10px rgba(15,23,42,.05)', sm: '0 6px 18px rgba(15,23,42,.07)', md: '0 12px 30px rgba(15,23,42,.10)', lg: '0 20px 45px rgba(15,23,42,.16)' };
    function isLightHex(hex) {
        if (!/^#[0-9A-Fa-f]{6}$/.test(String(hex || ''))) { return true; }
        var r = parseInt(hex.substr(1, 2), 16), g = parseInt(hex.substr(3, 2), 16), b = parseInt(hex.substr(5, 2), 16);
        return (0.299 * r + 0.587 * g + 0.114 * b) > 175;
    }

    if (!API_KEY || !BASE_URL) {
        console.error('[EazyAutomotive] taxatie: data-api-key en data-base-url zijn vereist.');
        return;
    }

    // Widget-brede staat, zodat de tweede stap (lead) het kenteken + km kent.
    var STATE = { kenteken: '', km: null };
    var CFG = {};
    var SHADOW = null;

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    function euro(n) {
        try {
            return new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n);
        } catch (e) {
            return '€ ' + n;
        }
    }

    function loadFont(name) {
        if (!name || name === 'system' || GOOGLE_FONTS.indexOf(name) === -1) { return SYSTEM_FONT; }
        var id = 'eazy-tax-font-' + name.replace(/\s/g, '-').toLowerCase();
        if (!document.getElementById(id)) {
            var link = document.createElement('link');
            link.id = id; link.rel = 'stylesheet';
            link.href = 'https://fonts.googleapis.com/css2?family=' + name.replace(/\s/g, '+') + ':wght@400;500;600;700&display=swap';
            document.head.appendChild(link);
        }
        return "'" + name + "'," + SYSTEM_FONT;
    }

    function styles() {
        var color = /^#[0-9A-Fa-f]{6}$/.test(CFG.primary_color || '') ? CFG.primary_color : '#0F9B9F';
        var cardRadius = (CFG.radius != null) ? Math.max(8, CFG.radius) : 18;
        var inputRadius = (CFG.radius != null) ? Math.min(CFG.radius, 14) : 10;
        var font = CFG._font || SYSTEM_FONT;
        var cardShadow = SHADOWS[CFG.card_shadow] || SHADOWS.none;
        var cardBg = isLightHex(CFG.card_bg_color) ? CFG.card_bg_color : '#fff';
        return '' +
            ':host{all:initial}' +
            '*{box-sizing:border-box;font-family:' + font + '}' +
            '.ea-card{max-width:520px;background:' + cardBg + ';border:1px solid rgba(15,23,42,.08);border-radius:' + cardRadius + 'px;padding:24px;box-shadow:' + cardShadow + ';color:#1f2937;animation:ea-in .25s ease}' +
            '.ea-badge{width:42px;height:42px;border-radius:13px;background:' + color + '1a;color:' + color + ';display:flex;align-items:center;justify-content:center;margin-bottom:12px}' +
            '.ea-h{font-size:19px;font-weight:800;margin:0 0 4px;color:#111827}' +
            '.ea-sub{font-size:13px;color:#6b7280;margin:0 0 18px}' +
            '.ea-row{margin-bottom:13px}' +
            '.ea-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}' +
            '@media(max-width:420px){.ea-grid{grid-template-columns:1fr}}' +
            '.ea-label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}' +
            '.ea-input{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:' + inputRadius + 'px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}' +
            '.ea-input:focus{border-color:' + color + ';box-shadow:0 0 0 3px ' + color + '33}' +
            '.ea-kenteken{text-transform:uppercase;letter-spacing:2px;font-weight:700}' +
            '.ea-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}' +
            '.ea-btn{width:100%;margin-top:6px;padding:12px 18px;border:0;border-radius:9999px;background:' + color + ';color:#fff;font-size:14px;font-weight:700;cursor:pointer;transition:filter .15s}' +
            '.ea-btn:hover{filter:brightness(.95)}' +
            '.ea-btn:disabled{opacity:.65;cursor:default}' +
            '.ea-ghost{background:none;color:' + color + ';border:0;font-size:13px;font-weight:600;cursor:pointer;padding:8px;margin-top:4px;width:100%}' +
            '.ea-err{display:none;background:#fef2f2;color:#b91c1c;font-size:13px;padding:9px 12px;border-radius:10px;margin-bottom:13px}' +
            '.ea-invalid{border-color:#ef4444 !important;box-shadow:0 0 0 3px #ef444422 !important}' +
            '.ea-fielderr{color:#b91c1c;font-size:11.5px;margin-top:4px;font-weight:500}' +
            '.ea-veh{display:flex;align-items:center;gap:12px;background:#f9fafb;border:1px solid #eef1f4;border-radius:14px;padding:12px 14px;margin-bottom:16px}' +
            '.ea-plate{background:#f7d417;color:#0b2b6b;border-radius:5px;font-weight:800;letter-spacing:1px;padding:5px 9px;font-size:13px;border:1px solid #d6b800;white-space:nowrap}' +
            '.ea-vehname{font-size:14px;font-weight:700;color:#111827}' +
            '.ea-vehmeta{font-size:12px;color:#6b7280}' +
            '.ea-est{text-align:center;background:' + color + '0f;border:1px solid ' + color + '33;border-radius:16px;padding:20px;margin-bottom:16px}' +
            '.ea-est-l{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:' + color + ';margin:0 0 6px}' +
            '.ea-est-v{font-size:34px;font-weight:900;color:#111827;line-height:1;margin:0 0 6px}' +
            '.ea-est-r{font-size:13px;color:#6b7280}' +
            '.ea-note{font-size:11.5px;color:#9ca3af;line-height:1.5;margin:0 0 16px}' +
            '.ea-ok{text-align:center;padding:18px 6px}' +
            '.ea-ok-ic{width:54px;height:54px;border-radius:50%;background:' + color + '1a;color:' + color + ';display:flex;align-items:center;justify-content:center;margin:0 auto 12px;animation:ea-pop .4s ease}' +
            '.ea-ok-h{font-size:17px;font-weight:800;color:#111827;margin:0 0 4px}' +
            '.ea-ok-p{font-size:14px;color:#6b7280;margin:0}' +
            '.ea-foot{margin-top:14px;text-align:center;font-size:11px;color:#9ca3af}' +
            '.ea-foot a{color:#9ca3af;text-decoration:none}' +
            '.ea-spin{display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:ea-spin .6s linear infinite;vertical-align:-2px}' +
            '@keyframes ea-spin{to{transform:rotate(360deg)}}' +
            '@keyframes ea-pop{0%{transform:scale(.6);opacity:0}60%{transform:scale(1.1)}100%{transform:scale(1);opacity:1}}' +
            '@keyframes ea-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}';
    }

    var CAR_SVG = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2v-3.28a2 2 0 0 0-.6-1.43L18 10l-1.5-4.5A2 2 0 0 0 14.6 4H9.4a2 2 0 0 0-1.9 1.5L6 10l-2.4 2.29A2 2 0 0 0 3 13.72V17h2"/><circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/></svg>';

    function wrap(inner) {
        return '<div class="ea-card">' + inner +
            '<div class="ea-foot">Mogelijk gemaakt door <a href="https://eazyautomotive.nl" target="_blank" rel="noopener">EazyAutomotive</a></div></div>';
    }

    /* ---------- Stap 1: kenteken invoeren ---------- */
    function startHtml() {
        return wrap(
            '<div class="ea-badge">' + CAR_SVG + '</div>' +
            '<h3 class="ea-h">' + esc(CUSTOM_TITLE || 'Wat is mijn auto waard?') + '</h3>' +
            '<p class="ea-sub">Vul je kenteken in en ontvang direct een indicatie van je inruilwaarde.</p>' +
            '<div class="ea-err" id="ea-err"></div>' +
            '<form id="ea-start" novalidate>' +
            '<div class="ea-grid">' +
            '<div class="ea-row"><label class="ea-label">Kenteken *</label><input class="ea-input ea-kenteken" name="kenteken" maxlength="10" placeholder="XX-123-X" autocomplete="off"></div>' +
            '<div class="ea-row"><label class="ea-label">Kilometerstand</label><input class="ea-input" type="number" name="kilometerstand" min="0" placeholder="bijv. 85000"></div>' +
            '</div>' +
            '<button class="ea-btn" type="submit" id="ea-go">Bereken inruilwaarde</button>' +
            '</form>'
        );
    }

    /* ---------- Stap 2: resultaat + contactgegevens ---------- */
    function resultHtml(res) {
        var v = res.voertuig || {};
        var t = res.taxatie || {};
        var naam = [v.merk, v.model].filter(Boolean).join(' ') || 'Je auto';
        var meta = [v.bouwjaar, v.brandstof].filter(Boolean).join(' · ');

        var vehicle = '<div class="ea-veh">' +
            (v.kenteken ? '<div class="ea-plate">' + esc(v.kenteken) + '</div>' : '') +
            '<div><div class="ea-vehname">' + esc(naam) + '</div>' +
            (meta ? '<div class="ea-vehmeta">' + esc(meta) + '</div>' : '') + '</div></div>';

        var estimate;
        if (t.beschikbaar) {
            estimate = '<div class="ea-est">' +
                '<p class="ea-est-l">Geschatte inruilwaarde</p>' +
                '<p class="ea-est-v">' + euro(t.midden) + '</p>' +
                '<p class="ea-est-r">tussen ' + euro(t.onder) + ' en ' + euro(t.boven) + '</p></div>' +
                '<p class="ea-note">Dit is een indicatie op basis van actuele marktdata, geen definitief bod. De uiteindelijke inruilprijs hangt af van de staat van de auto en een inspectie.</p>';
        } else {
            estimate = '<p class="ea-note">We konden voor deze auto geen automatische waarde berekenen. Laat je gegevens achter, dan maken we een persoonlijk inruilvoorstel.</p>';
        }

        return wrap(
            '<div class="ea-badge">' + CAR_SVG + '</div>' +
            '<h3 class="ea-h">Jouw inruilindicatie</h3>' +
            vehicle + estimate +
            '<div class="ea-err" id="ea-err"></div>' +
            '<form id="ea-lead" novalidate>' +
            '<div class="ea-hp"><label>Laat leeg<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>' +
            '<div class="ea-grid">' +
            '<div class="ea-row"><label class="ea-label">Naam *</label><input class="ea-input" name="naam" required maxlength="100"></div>' +
            '<div class="ea-row"><label class="ea-label">Telefoon</label><input class="ea-input" type="tel" name="telefoon" maxlength="30"></div>' +
            '</div>' +
            '<div class="ea-row"><label class="ea-label">E-mail</label><input class="ea-input" type="email" name="email" maxlength="150"></div>' +
            '<button class="ea-btn" type="submit" id="ea-send">Inruilvoorstel aanvragen</button>' +
            '<button class="ea-ghost" type="button" id="ea-back">Ander kenteken</button>' +
            '</form>'
        );
    }

    function successHtml(message) {
        return wrap('<div class="ea-ok">' +
            '<div class="ea-ok-ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>' +
            '<h3 class="ea-ok-h">Aanvraag verzonden</h3>' +
            '<p class="ea-ok-p">' + esc(message) + '</p></div>');
    }

    function paint(html) {
        SHADOW.innerHTML = '<style>' + styles() + '</style>' + html;
    }

    function fieldError(form, name, msg) {
        var el = form.querySelector('[name="' + name + '"]');
        if (!el) { return; }
        el.classList.add('ea-invalid');
        var row = el.closest('.ea-row') || el.parentNode;
        if (msg && row && !row.querySelector('.ea-fielderr')) {
            var d = document.createElement('div');
            d.className = 'ea-fielderr'; d.textContent = msg;
            row.appendChild(d);
        }
    }

    /* ---------- Stap 1 binden ---------- */
    function bindStart() {
        var form = SHADOW.getElementById('ea-start');
        var errEl = SHADOW.getElementById('ea-err');
        var btn = SHADOW.getElementById('ea-go');
        var kt = form.querySelector('[name="kenteken"]');
        kt.addEventListener('input', function () { kt.value = kt.value.toUpperCase(); kt.classList.remove('ea-invalid'); });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errEl.style.display = 'none';
            var kenteken = (kt.value || '').replace(/[^A-Za-z0-9]/g, '');
            if (kenteken.length < 4) { fieldError(form, 'kenteken', 'Vul een geldig kenteken in.'); kt.focus(); return; }
            var km = form.querySelector('[name="kilometerstand"]').value;

            STATE.kenteken = kenteken;
            STATE.km = km ? parseInt(km, 10) : null;

            btn.disabled = true; btn.innerHTML = '<span class="ea-spin"></span> Ophalen...';

            fetch(BASE_URL + '/api/embed/v1/taxatie?api_key=' + encodeURIComponent(API_KEY), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ kenteken: STATE.kenteken, kilometerstand: STATE.km })
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                .then(function (res) {
                    if (res.ok && res.body.ok) {
                        paint(resultHtml(res.body));
                        bindResult();
                    } else {
                        errEl.textContent = (res.body && res.body.message) || 'Er ging iets mis. Probeer het opnieuw.';
                        errEl.style.display = 'block';
                        btn.disabled = false; btn.textContent = 'Bereken inruilwaarde';
                    }
                })
                .catch(function () {
                    errEl.textContent = 'Verbinding mislukt. Probeer het later opnieuw.';
                    errEl.style.display = 'block';
                    btn.disabled = false; btn.textContent = 'Bereken inruilwaarde';
                });
        });
    }

    /* ---------- Stap 2 binden ---------- */
    function bindResult() {
        var form = SHADOW.getElementById('ea-lead');
        var errEl = SHADOW.getElementById('ea-err');
        var btn = SHADOW.getElementById('ea-send');
        SHADOW.getElementById('ea-back').addEventListener('click', function () { paint(startHtml()); bindStart(); });
        form.addEventListener('input', function (e) { if (e.target.classList) { e.target.classList.remove('ea-invalid'); } });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errEl.style.display = 'none';
            form.querySelectorAll('.ea-fielderr').forEach(function (el) { el.remove(); });

            var raw = {};
            new FormData(form).forEach(function (val, k) { raw[k] = val; });

            var bad = false;
            if (!raw.naam) { fieldError(form, 'naam', 'Vul je naam in.'); bad = true; }
            if (raw.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(raw.email)) { fieldError(form, 'email', 'Controleer je e-mailadres.'); bad = true; }
            if (!raw.email && !raw.telefoon) { fieldError(form, 'email', 'Vul je e-mail of telefoon in.'); fieldError(form, 'telefoon'); bad = true; }
            if (bad) { var f = form.querySelector('.ea-invalid'); if (f) { f.focus(); } return; }

            btn.disabled = true; btn.innerHTML = '<span class="ea-spin"></span>';

            fetch(BASE_URL + '/api/embed/v1/taxatie/lead?api_key=' + encodeURIComponent(API_KEY), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    kenteken: STATE.kenteken, kilometerstand: STATE.km,
                    naam: raw.naam, email: raw.email || null, telefoon: raw.telefoon || null,
                    website: raw.website || ''
                })
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                .then(function (res) {
                    if (res.ok && res.body.ok) {
                        paint(successHtml(res.body.message || 'We nemen snel contact met je op.'));
                    } else {
                        errEl.textContent = (res.body && res.body.message) || 'Er ging iets mis. Probeer het opnieuw.';
                        errEl.style.display = 'block';
                        btn.disabled = false; btn.textContent = 'Inruilvoorstel aanvragen';
                    }
                })
                .catch(function () {
                    errEl.textContent = 'Verbinding mislukt. Probeer het later opnieuw.';
                    errEl.style.display = 'block';
                    btn.disabled = false; btn.textContent = 'Inruilvoorstel aanvragen';
                });
        });
    }

    function mount() {
        var host = document.getElementById(CONTAINER_ID);
        if (!host) { console.error('[EazyAutomotive] taxatie: container #' + CONTAINER_ID + ' niet gevonden.'); return; }
        SHADOW = host.attachShadow ? host.attachShadow({ mode: 'open' }) : host;

        fetch(BASE_URL + '/api/embed/v1/taxatie/config?api_key=' + encodeURIComponent(API_KEY))
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
            .then(function (cfg) {
                CFG = cfg || {};
                CFG._font = loadFont(CFG.font_family);
                paint(startHtml());
                bindStart();
            })
            .catch(function () {
                SHADOW.innerHTML = '<div style="font:14px sans-serif;color:#b91c1c;padding:16px">De taxatietool kon niet worden geladen.</div>';
            });
    }

    ready(mount);
})();
