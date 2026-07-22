(function () {
    'use strict';

    var SCRIPT = document.currentScript;
    var API_KEY = SCRIPT && SCRIPT.getAttribute('data-api-key');
    var BASE_URL = ((SCRIPT && SCRIPT.getAttribute('data-base-url')) || '').replace(/\/$/, '');
    var CONTAINER_ID = (SCRIPT && SCRIPT.getAttribute('data-container')) || 'eazy-proefrit';
    var PRESELECT_CAR = (SCRIPT && SCRIPT.getAttribute('data-car-id')) || '';

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
        console.error('[EazyAutomotive] proefrit: data-api-key en data-base-url zijn vereist.');
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
        var id = 'eazy-pf-font-' + name.replace(/\s/g, '-').toLowerCase();
        if (!document.getElementById(id)) {
            var link = document.createElement('link');
            link.id = id;
            link.rel = 'stylesheet';
            link.href = 'https://fonts.googleapis.com/css2?family=' + name.replace(/\s/g, '+') + ':wght@400;500;600;700&display=swap';
            document.head.appendChild(link);
        }
        return "'" + name + "'," + SYSTEM_FONT;
    }

    function styles(color, cardRadius, inputRadius, font, cardShadow, cardBg) {
        return '' +
            ':host{all:initial}' +
            '*{box-sizing:border-box;font-family:' + font + '}' +
            '.ea-card{max-width:520px;background:' + cardBg + ';border:1px solid rgba(15,23,42,.08);border-radius:' + cardRadius + 'px;padding:24px;box-shadow:' + cardShadow + ';color:#1f2937;animation:ea-cardin .25s ease}' +
            '.ea-h{font-size:19px;font-weight:800;margin:0 0 4px;color:#111827}' +
            '.ea-sub{font-size:13px;color:#6b7280;margin:0 0 18px}' +
            '.ea-row{margin-bottom:13px}' +
            '.ea-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}' +
            '@media(max-width:420px){.ea-grid{grid-template-columns:1fr}}' +
            '.ea-label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}' +
            '.ea-input,.ea-select,.ea-textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:' + inputRadius + 'px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}' +
            '.ea-input:focus,.ea-select:focus,.ea-textarea:focus{border-color:' + color + ';box-shadow:0 0 0 3px ' + color + '33}' +
            '.ea-textarea{min-height:76px;resize:vertical}' +
            '.ea-check{display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;line-height:1.4}' +
            '.ea-check input{margin-top:2px}' +
            '.ea-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}' +
            '.ea-btn{width:100%;margin-top:6px;padding:12px 18px;border:0;border-radius:9999px;background:' + color + ';color:#fff;font-size:14px;font-weight:700;cursor:pointer;transition:filter .15s}' +
            '.ea-btn:hover{filter:brightness(.95)}' +
            '.ea-btn:disabled{opacity:.65;cursor:default}' +
            '.ea-err{display:none;background:#fef2f2;color:#b91c1c;font-size:13px;padding:9px 12px;border-radius:10px;margin-bottom:13px}' +
            '.ea-ok{text-align:center;padding:18px 6px}' +
            '.ea-ok-ic{width:54px;height:54px;border-radius:50%;background:' + color + '1a;color:' + color + ';display:flex;align-items:center;justify-content:center;margin:0 auto 12px;animation:ea-pop .4s ease}' +
            '.ea-ok-h{font-size:17px;font-weight:800;color:#111827;margin:0 0 4px}' +
            '.ea-ok-p{font-size:14px;color:#6b7280;margin:0}' +
            '.ea-foot{margin-top:14px;text-align:center;font-size:11px;color:#9ca3af}' +
            '.ea-foot a{color:#9ca3af;text-decoration:none}' +
            '.ea-badge{width:42px;height:42px;border-radius:13px;background:' + color + '1a;color:' + color + ';display:flex;align-items:center;justify-content:center;margin-bottom:12px}' +
            '.ea-invalid{border-color:#ef4444 !important;box-shadow:0 0 0 3px #ef444422 !important}' +
            '.ea-fielderr{color:#b91c1c;font-size:11.5px;margin-top:4px;font-weight:500}' +
            '.ea-spin{display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:ea-spin .6s linear infinite;vertical-align:-2px}' +
            '@keyframes ea-spin{to{transform:rotate(360deg)}}' +
            '@keyframes ea-pop{0%{transform:scale(.6);opacity:0}60%{transform:scale(1.1)}100%{transform:scale(1);opacity:1}}' +
            '@keyframes ea-cardin{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}';
    }

    function carsField(cars, p) {
        var required = p.auto_verplicht;
        var matched = false;
        for (var j = 0; j < cars.length; j++) {
            if (String(cars[j].id) === String(PRESELECT_CAR)) { matched = true; break; }
        }
        var placeholder = required
            ? '<option value="" disabled' + (matched ? '' : ' selected') + '>Kies een auto</option>'
            : '<option value="">Geen voorkeur / algemeen</option>';
        var opts = placeholder;
        for (var i = 0; i < cars.length; i++) {
            var sel = String(cars[i].id) === String(PRESELECT_CAR) ? ' selected' : '';
            opts += '<option value="' + esc(cars[i].id) + '"' + sel + '>' + esc(cars[i].titel) + '</option>';
        }
        return '<div class="ea-row"><label class="ea-label">Auto' + (required ? ' *' : '') + '</label>' +
            '<select class="ea-select" name="car_id"' + (required ? ' required' : '') + '>' + opts + '</select></div>';
    }

    function formHtml(cfg) {
        var p = cfg.proefrit || {};
        var intro = p.intro || ('Vul je gegevens in bij ' + cfg.company.name + ' en we nemen snel contact met je op.');
        var today = new Date().toISOString().slice(0, 10);

        var fields = '';
        fields += carsField(cfg.cars || [], p);
        if (p.toon_datum) {
            fields += '<div class="ea-row"><label class="ea-label">Gewenste datum</label><input class="ea-input" type="date" name="gewenste_datum" min="' + today + '"></div>';
        }
        if (p.toon_bericht) {
            fields += '<div class="ea-row"><label class="ea-label">Bericht</label><textarea class="ea-textarea" name="bericht" maxlength="1000" placeholder="Bijv. een voorkeursmoment of vraag"></textarea></div>';
        }
        if (p.privacy) {
            fields += '<div class="ea-row"><label class="ea-check"><input type="checkbox" name="privacy" value="1"><span>' + esc(p.privacy_tekst) + '</span></label></div>';
        }

        return '<div class="ea-card">' +
            '<div class="ea-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg></div>' +
            '<h3 class="ea-h">' + esc(p.titel || 'Plan een proefrit') + '</h3>' +
            '<p class="ea-sub">' + esc(intro) + '</p>' +
            '<div class="ea-err" id="ea-err"></div>' +
            '<form id="ea-form" novalidate>' +
            '<div class="ea-hp"><label>Laat dit veld leeg<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>' +
            '<div class="ea-grid">' +
            '<div class="ea-row"><label class="ea-label">Naam *</label><input class="ea-input" type="text" name="naam" required maxlength="100"></div>' +
            '<div class="ea-row"><label class="ea-label">Telefoon *</label><input class="ea-input" type="tel" name="telefoon" required maxlength="30"></div>' +
            '</div>' +
            '<div class="ea-row"><label class="ea-label">E-mail *</label><input class="ea-input" type="email" name="email" required maxlength="150"></div>' +
            fields +
            '<button class="ea-btn" type="submit" id="ea-submit">' + esc(p.knop || 'Proefrit aanvragen') + '</button>' +
            '</form>' +
            '<div class="ea-foot">Mogelijk gemaakt door <a href="https://eazyautomotive.nl" target="_blank" rel="noopener">EazyAutomotive</a></div>' +
            '</div>';
    }

    function successHtml(message) {
        return '<div class="ea-card"><div class="ea-ok">' +
            '<div class="ea-ok-ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>' +
            '<h3 class="ea-ok-h">Aanvraag verstuurd</h3>' +
            '<p class="ea-ok-p">' + esc(message) + '</p>' +
            '</div></div>';
    }

    function mount() {
        var host = document.getElementById(CONTAINER_ID);
        if (!host) {
            console.error('[EazyAutomotive] proefrit: container #' + CONTAINER_ID + ' niet gevonden.');
            return;
        }
        var shadow = host.attachShadow ? host.attachShadow({ mode: 'open' }) : host;

        fetch(BASE_URL + '/api/embed/v1/proefrit/config?api_key=' + encodeURIComponent(API_KEY))
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
            .then(function (cfg) { render(shadow, cfg); })
            .catch(function () {
                shadow.innerHTML = '<div style="font:14px sans-serif;color:#b91c1c;padding:16px">Het proefrit-formulier kon niet worden geladen.</div>';
            });
    }

    function render(shadow, cfg) {
        var p = cfg.proefrit || {};
        var color = /^#[0-9A-Fa-f]{6}$/.test(cfg.primary_color || '') ? cfg.primary_color : '#0F9B9F';
        var cardRadius = (cfg.radius != null) ? Math.max(8, cfg.radius) : 18;
        var inputRadius = (cfg.radius != null) ? Math.min(cfg.radius, 14) : 10;
        var font = loadFont(cfg.font_family);
        var cardShadow = SHADOWS[cfg.card_shadow] || SHADOWS.none;
        var cardBg = isLightHex(cfg.card_bg_color) ? cfg.card_bg_color : '#fff';

        shadow.innerHTML = '<style>' + styles(color, cardRadius, inputRadius, font, cardShadow, cardBg) + '</style>' + formHtml(cfg);

        var form = shadow.getElementById('ea-form');
        var errEl = shadow.getElementById('ea-err');
        var btn = shadow.getElementById('ea-submit');

        var submitLabel = p.knop || 'Proefrit aanvragen';

        function fail(msg) {
            errEl.textContent = msg;
            errEl.style.display = 'block';
        }

        function clearErrors() {
            errEl.style.display = 'none';
            form.querySelectorAll('.ea-invalid').forEach(function (el) { el.classList.remove('ea-invalid'); });
            form.querySelectorAll('.ea-fielderr').forEach(function (el) { el.remove(); });
        }

        function invalid(name, msg) {
            var el = form.querySelector('[name="' + name + '"]');
            if (!el) { return; }
            el.classList.add('ea-invalid');
            if (msg) {
                var row = el.closest('.ea-row') || el.parentNode;
                if (row && !row.querySelector('.ea-fielderr')) {
                    var d = document.createElement('div');
                    d.className = 'ea-fielderr';
                    d.textContent = msg;
                    row.appendChild(d);
                }
            }
        }

        form.addEventListener('input', function (e) {
            if (e.target && e.target.classList) { e.target.classList.remove('ea-invalid'); }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearErrors();

            var data = {};
            new FormData(form).forEach(function (v, k) { data[k] = v; });

            var bad = false;
            if (!data.naam) { invalid('naam', 'Vul je naam in.'); bad = true; }
            if (!data.email) { invalid('email', 'Vul je e-mail in.'); bad = true; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) { invalid('email', 'Controleer je e-mailadres.'); bad = true; }
            if (!data.telefoon) { invalid('telefoon', 'Vul je telefoonnummer in.'); bad = true; }
            if (p.auto_verplicht && !data.car_id) { invalid('car_id', 'Kies een auto voor de proefrit.'); bad = true; }
            if (p.privacy && !data.privacy) { invalid('privacy', 'Ga akkoord om door te gaan.'); bad = true; }
            if (bad) {
                var first = form.querySelector('.ea-invalid');
                if (first) { first.focus(); }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="ea-spin"></span>';

            fetch(BASE_URL + '/api/embed/v1/proefrit?api_key=' + encodeURIComponent(API_KEY), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data),
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                .then(function (res) {
                    if (res.ok && res.body.ok) {
                        shadow.innerHTML = '<style>' + styles(color, cardRadius, inputRadius, font, cardShadow, cardBg) + '</style>' + successHtml(res.body.message || 'We nemen snel contact met je op.');
                    } else {
                        var msg = res.body && res.body.errors
                            ? Object.values(res.body.errors)[0][0]
                            : (res.body && res.body.message) || 'Er ging iets mis. Probeer het opnieuw.';
                        fail(msg);
                        btn.disabled = false;
                        btn.textContent = submitLabel;
                    }
                })
                .catch(function () {
                    fail('Verbinding mislukt. Probeer het later opnieuw.');
                    btn.disabled = false;
                    btn.textContent = p.knop || 'Proefrit aanvragen';
                });
        });
    }

    ready(mount);
})();
