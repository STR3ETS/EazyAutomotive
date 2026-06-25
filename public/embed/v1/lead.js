(function () {
    'use strict';

    var SCRIPT = document.currentScript;
    var API_KEY = SCRIPT && SCRIPT.getAttribute('data-api-key');
    var BASE_URL = ((SCRIPT && SCRIPT.getAttribute('data-base-url')) || '').replace(/\/$/, '');
    var CONTAINER_ID = (SCRIPT && SCRIPT.getAttribute('data-container')) || 'eazy-lead';
    var TYPE = (SCRIPT && SCRIPT.getAttribute('data-type')) || 'contact';
    var PRESELECT_CAR = (SCRIPT && SCRIPT.getAttribute('data-car-id')) || '';
    var CUSTOM_TITLE = (SCRIPT && SCRIPT.getAttribute('data-title')) || '';

    var GOOGLE_FONTS = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins'];
    var SYSTEM_FONT = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";

    var TYPES = {
        contact: { title: 'Neem contact op', intro: 'Stel je vraag, we reageren snel.', knop: 'Versturen' },
        inruil: { title: 'Auto inruilen', intro: 'Laat je gegevens achter, dan komen we met een inruilvoorstel.', knop: 'Inruilvoorstel aanvragen' },
        financiering: { title: 'Financiering aanvragen', intro: 'We rekenen vrijblijvend je mogelijkheden uit.', knop: 'Aanvraag versturen' }
    };
    if (!TYPES[TYPE]) { TYPE = 'contact'; }

    if (!API_KEY || !BASE_URL) {
        console.error('[EazyAutomotive] lead: data-api-key en data-base-url zijn vereist.');
        return;
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    function loadFont(name) {
        if (!name || name === 'system' || GOOGLE_FONTS.indexOf(name) === -1) { return SYSTEM_FONT; }
        var id = 'eazy-lead-font-' + name.replace(/\s/g, '-').toLowerCase();
        if (!document.getElementById(id)) {
            var link = document.createElement('link');
            link.id = id;
            link.rel = 'stylesheet';
            link.href = 'https://fonts.googleapis.com/css2?family=' + name.replace(/\s/g, '+') + ':wght@400;500;600;700&display=swap';
            document.head.appendChild(link);
        }
        return "'" + name + "'," + SYSTEM_FONT;
    }

    function styles(color, cardRadius, inputRadius, font) {
        return '' +
            ':host{all:initial}' +
            '*{box-sizing:border-box;font-family:' + font + '}' +
            '.ea-card{max-width:520px;background:#fff;border:1px solid rgba(15,23,42,.08);border-radius:' + cardRadius + 'px;padding:24px;box-shadow:0 10px 30px rgba(15,23,42,.06);color:#1f2937}' +
            '.ea-h{font-size:19px;font-weight:800;margin:0 0 4px;color:#111827}' +
            '.ea-sub{font-size:13px;color:#6b7280;margin:0 0 18px}' +
            '.ea-row{margin-bottom:13px}' +
            '.ea-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}' +
            '@media(max-width:420px){.ea-grid{grid-template-columns:1fr}}' +
            '.ea-label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}' +
            '.ea-input,.ea-select,.ea-textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:' + inputRadius + 'px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}' +
            '.ea-input:focus,.ea-select:focus,.ea-textarea:focus{border-color:' + color + ';box-shadow:0 0 0 3px ' + color + '33}' +
            '.ea-textarea{min-height:76px;resize:vertical}' +
            '.ea-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}' +
            '.ea-btn{width:100%;margin-top:6px;padding:12px 18px;border:0;border-radius:9999px;background:' + color + ';color:#fff;font-size:14px;font-weight:700;cursor:pointer;transition:filter .15s}' +
            '.ea-btn:hover{filter:brightness(.95)}' +
            '.ea-btn:disabled{opacity:.65;cursor:default}' +
            '.ea-err{display:none;background:#fef2f2;color:#b91c1c;font-size:13px;padding:9px 12px;border-radius:10px;margin-bottom:13px}' +
            '.ea-ok{text-align:center;padding:18px 6px}' +
            '.ea-ok-ic{width:54px;height:54px;border-radius:50%;background:' + color + '1a;color:' + color + ';display:flex;align-items:center;justify-content:center;margin:0 auto 12px}' +
            '.ea-ok-h{font-size:17px;font-weight:800;color:#111827;margin:0 0 4px}' +
            '.ea-ok-p{font-size:14px;color:#6b7280;margin:0}' +
            '.ea-foot{margin-top:14px;text-align:center;font-size:11px;color:#9ca3af}' +
            '.ea-foot a{color:#9ca3af;text-decoration:none}';
    }

    function carField(cars) {
        if (PRESELECT_CAR || !cars || !cars.length) { return ''; }
        var opts = '<option value="">Geen voorkeur / algemeen</option>';
        for (var i = 0; i < cars.length; i++) {
            opts += '<option value="' + esc(cars[i].id) + '">' + esc(cars[i].titel) + '</option>';
        }
        return '<div class="ea-row"><label class="ea-label">Auto</label><select class="ea-select" name="car_id">' + opts + '</select></div>';
    }

    function formHtml(cfg) {
        var t = TYPES[TYPE];
        var title = CUSTOM_TITLE || t.title;

        var fields = '';
        if (TYPE === 'inruil') {
            fields += '<div class="ea-grid">' +
                '<div class="ea-row"><label class="ea-label">Kenteken</label><input class="ea-input" type="text" name="kenteken" maxlength="10" placeholder="XX-123-X"></div>' +
                '<div class="ea-row"><label class="ea-label">Kilometerstand</label><input class="ea-input" type="number" name="kilometerstand" min="0" placeholder="bijv. 85000"></div>' +
                '</div>';
        } else {
            fields += carField(cfg.cars || []);
        }
        fields += '<div class="ea-row"><label class="ea-label">Bericht</label><textarea class="ea-textarea" name="bericht" maxlength="1000" placeholder="Waar kunnen we je mee helpen?"></textarea></div>';

        return '<div class="ea-card">' +
            '<h3 class="ea-h">' + esc(title) + '</h3>' +
            '<p class="ea-sub">' + esc(t.intro) + '</p>' +
            '<div class="ea-err" id="ea-err"></div>' +
            '<form id="ea-form" novalidate>' +
            '<div class="ea-hp"><label>Laat dit veld leeg<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>' +
            '<div class="ea-grid">' +
            '<div class="ea-row"><label class="ea-label">Naam *</label><input class="ea-input" type="text" name="naam" required maxlength="100"></div>' +
            '<div class="ea-row"><label class="ea-label">Telefoon</label><input class="ea-input" type="tel" name="telefoon" maxlength="30"></div>' +
            '</div>' +
            '<div class="ea-row"><label class="ea-label">E-mail</label><input class="ea-input" type="email" name="email" maxlength="150"></div>' +
            fields +
            '<button class="ea-btn" type="submit" id="ea-submit">' + esc(t.knop) + '</button>' +
            '</form>' +
            '<div class="ea-foot">Mogelijk gemaakt door <a href="https://eazyautomotive.nl" target="_blank" rel="noopener">EazyAutomotive</a></div>' +
            '</div>';
    }

    function successHtml(message) {
        return '<div class="ea-card"><div class="ea-ok">' +
            '<div class="ea-ok-ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>' +
            '<h3 class="ea-ok-h">Verzonden</h3>' +
            '<p class="ea-ok-p">' + esc(message) + '</p>' +
            '</div></div>';
    }

    function mount() {
        var host = document.getElementById(CONTAINER_ID);
        if (!host) {
            console.error('[EazyAutomotive] lead: container #' + CONTAINER_ID + ' niet gevonden.');
            return;
        }
        var shadow = host.attachShadow ? host.attachShadow({ mode: 'open' }) : host;

        fetch(BASE_URL + '/api/embed/v1/lead/config?api_key=' + encodeURIComponent(API_KEY))
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
            .then(function (cfg) { render(shadow, cfg); })
            .catch(function () {
                shadow.innerHTML = '<div style="font:14px sans-serif;color:#b91c1c;padding:16px">Het formulier kon niet worden geladen.</div>';
            });
    }

    function render(shadow, cfg) {
        var color = /^#[0-9A-Fa-f]{6}$/.test(cfg.primary_color || '') ? cfg.primary_color : '#0F9B9F';
        var cardRadius = (cfg.radius != null) ? Math.max(8, cfg.radius) : 18;
        var inputRadius = (cfg.radius != null) ? Math.min(cfg.radius, 14) : 10;
        var font = loadFont(cfg.font_family);

        shadow.innerHTML = '<style>' + styles(color, cardRadius, inputRadius, font) + '</style>' + formHtml(cfg);

        var form = shadow.getElementById('ea-form');
        var errEl = shadow.getElementById('ea-err');
        var btn = shadow.getElementById('ea-submit');

        function fail(msg) {
            errEl.textContent = msg;
            errEl.style.display = 'block';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errEl.style.display = 'none';

            var raw = {};
            new FormData(form).forEach(function (v, k) { raw[k] = v; });

            if (!raw.naam) { return fail('Vul je naam in.'); }
            if (!raw.email && !raw.telefoon) { return fail('Vul je e-mail of telefoonnummer in.'); }

            // Build the message: fold inruil specifics into the message text.
            var bericht = raw.bericht || '';
            if (TYPE === 'inruil') {
                var extra = [];
                if (raw.kenteken) { extra.push('Kenteken: ' + raw.kenteken); }
                if (raw.kilometerstand) { extra.push('Kilometerstand: ' + raw.kilometerstand); }
                if (extra.length) { bericht = extra.join(' | ') + (bericht ? '\n\n' + bericht : ''); }
            }

            var payload = {
                type: TYPE,
                naam: raw.naam,
                email: raw.email || null,
                telefoon: raw.telefoon || null,
                bericht: bericht || null,
                car_id: PRESELECT_CAR || raw.car_id || null,
                website: raw.website || ''
            };

            btn.disabled = true;
            btn.textContent = 'Versturen...';

            fetch(BASE_URL + '/api/embed/v1/lead?api_key=' + encodeURIComponent(API_KEY), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                .then(function (res) {
                    if (res.ok && res.body.ok) {
                        shadow.innerHTML = '<style>' + styles(color, cardRadius, inputRadius, font) + '</style>' + successHtml(res.body.message || 'We nemen snel contact met je op.');
                    } else {
                        var msg = res.body && res.body.errors
                            ? Object.values(res.body.errors)[0][0]
                            : (res.body && res.body.message) || 'Er ging iets mis. Probeer het opnieuw.';
                        fail(msg);
                        btn.disabled = false;
                        btn.textContent = TYPES[TYPE].knop;
                    }
                })
                .catch(function () {
                    fail('Verbinding mislukt. Probeer het later opnieuw.');
                    btn.disabled = false;
                    btn.textContent = TYPES[TYPE].knop;
                });
        });
    }

    ready(mount);
})();
