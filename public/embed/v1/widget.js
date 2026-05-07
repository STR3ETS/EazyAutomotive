(function(){(function(){"use strict";let e=document.currentScript,t=e?.getAttribute(`data-api-key`),n=(e?.getAttribute(`data-base-url`)||``).replace(/\/$/,``),r=e?.getAttribute(`data-container`)||`eazy-automotive-widget`;if(!t){console.error(`[EazyAutomotive] data-api-key attribuut ontbreekt op het script tag.`);return}if(!n){console.error(`[EazyAutomotive] data-base-url attribuut ontbreekt op het script tag.`);return}let i={none:`none`,sm:`0 1px 3px rgba(0,0,0,0.08)`,md:`0 4px 12px rgba(0,0,0,0.1)`,lg:`0 10px 30px rgba(0,0,0,0.15)`},a={EUR:`€`,USD:`$`,GBP:`£`,none:``},o={year:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,fuel:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2h0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>`,km:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,color:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`},s=`-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`,c=[`Inter`,`Roboto`,`Open Sans`,`Lato`,`Montserrat`,`Poppins`];function l(e){if(!e||e===`system`||!c.includes(e))return;let t=`eazy-gfont-`+e.replace(/\s/g,`-`).toLowerCase();if(document.getElementById(t))return;let n=document.createElement(`link`);n.id=t,n.rel=`stylesheet`,n.href=`https://fonts.googleapis.com/css2?family=${e.replace(/\s/g,`+`)}:wght@400;500;600;700&display=swap`,document.head.appendChild(n)}class u{constructor(){this.cars=[],this.settings={},this.companyInfo={},this.init()}async init(){let t=document.getElementById(r);t||(t=document.createElement(`div`),t.id=r,e.parentNode.insertBefore(t,e)),this.shadow=t.attachShadow({mode:`open`}),this.shadow.innerHTML=this.loadingHTML(),await this.fetchCars(),this.render()}async fetchCars(e=1){try{let r=`${n}/api/embed/v1/cars?api_key=${encodeURIComponent(t)}&page=${e}`,i=await fetch(r);if(!i.ok)throw Error(`HTTP ${i.status}`);let a=await i.json();this.cars=a.cars?.data||[],this.settings=a.settings||{},this.companyInfo=a.company||{},this.pagination=a.cars||{}}catch(e){console.error(`[EazyAutomotive]`,e),this.shadow.innerHTML=this.errorHTML()}}async fetchCarDetail(e){try{let r=`${n}/api/embed/v1/cars/${e}?api_key=${encodeURIComponent(t)}`,i=await fetch(r);if(!i.ok)throw Error(`HTTP ${i.status}`);return(await i.json()).car}catch(e){return console.error(`[EazyAutomotive] Detail fetch error:`,e),null}}get s(){return this.settings}opt(e,t){return this.s[e]??t}get primaryColor(){return this.opt(`primary_color`,`#4f46e5`)}get fontStack(){let e=this.opt(`font_family`,`system`);return e===`system`?s:`'${e}', ${s}`}formatPrice(e){if(!e)return``;let t=a[this.opt(`currency`,`EUR`)]??`€`,n=e.replace(/^[€$£]\s*/,``);return t?`${t} ${n}`:n}loadFont(){l(this.opt(`font_family`,`system`))}get detailCustom(){return this.opt(`detail_custom`,!1)}detailOpt(e,t){return this.detailCustom?this.opt(e,t):t}getDetailVarsComment(){return``}getHoverCSS(){let e=this.opt(`hover_effect`,`lift`),t=this.primaryColor;switch(e){case`lift`:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`;case`shadow`:return`.eazy-card:hover { box-shadow: 0 12px 35px rgba(0,0,0,0.18); }`;case`scale`:return`.eazy-card:hover { transform: scale(1.03); }`;case`glow`:return`.eazy-card:hover { box-shadow: 0 0 20px ${t}44; }`;case`none`:return``;default:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`}}getStyles(){let e=this.opt(`columns`,3),t=this.opt(`card_bg_color`,`#ffffff`),n=this.opt(`card_border_radius`,12),r=this.opt(`card_padding`,16),a=this.opt(`card_border_color`,`#e5e7eb`),o=this.opt(`card_border_width`,1),s=i[this.opt(`card_shadow`,`none`)]||`none`,c=this.opt(`image_height`,200),l=this.opt(`title_size`,16),u=this.opt(`title_color`,`#111827`),d=this.opt(`price_size`,20),f=this.opt(`label_bg_color`,`#f3f4f6`),p=this.opt(`label_text_color`,`#4b5563`),m=this.opt(`label_radius`,4),h=this.opt(`label_padding_x`,8),g=this.opt(`label_padding_y`,3),_=this.opt(`label_gap`,6),v=this.opt(`label_style`,`badge`),y=this.detailCustom,b=y?this.opt(`detail_bg_color`,t):t,x=y?this.opt(`detail_border_radius`,n):n,S=y?this.opt(`detail_padding`,r):r,C=y?this.opt(`detail_title_size`,24):24,w=y?this.opt(`detail_title_color`,u):u,T=y?this.opt(`detail_price_size`,24):24,E=y?this.opt(`detail_gallery_height`,350):350,D=y?this.opt(`detail_spec_columns`,2):2;return`
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                :host { display: block; font-family: ${this.fontStack}; }
                .eazy-grid {
                    display: grid;
                    grid-template-columns: repeat(${e}, 1fr);
                    gap: 1.25rem;
                    padding: 1rem 0;
                }
                @media (max-width: 640px) {
                    .eazy-grid { grid-template-columns: 1fr; }
                }
                @media (min-width: 641px) and (max-width: 1024px) {
                    .eazy-grid { grid-template-columns: repeat(2, 1fr); }
                }
                .eazy-card {
                    border: ${o}px solid ${a};
                    border-radius: ${n}px;
                    overflow: hidden;
                    background: ${t};
                    box-shadow: ${s};
                    transition: box-shadow 0.2s ease, transform 0.2s ease;
                    cursor: pointer;
                }
                ${this.getHoverCSS()}
                .eazy-card-img {
                    width: 100%;
                    height: ${c}px;
                    object-fit: cover;
                    display: block;
                }
                .eazy-no-img {
                    width: 100%;
                    height: ${c}px;
                    background: #f3f4f6;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #9ca3af;
                    font-size: 0.875rem;
                }
                .eazy-card-body { padding: ${r}px; }
                .eazy-card-title {
                    font-size: ${l}px;
                    font-weight: 600;
                    color: ${u};
                    margin-bottom: 0.25rem;
                    line-height: 1.3;
                }
                .eazy-card-price {
                    font-size: ${d}px;
                    font-weight: 700;
                    color: ${this.primaryColor};
                    margin-bottom: 0.75rem;
                }
                .eazy-card-specs {
                    display: flex;
                    flex-wrap: wrap;
                    gap: ${_}px;
                }
                .eazy-spec {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    font-size: 0.75rem;
                    color: ${p};
                    ${v===`badge`?`background: ${f}; padding: ${g}px ${h}px; border-radius: ${m}px;`:``}
                    ${v===`outline`?`background: transparent; border: 1px solid ${p}; padding: ${g}px ${h}px; border-radius: ${m}px;`:``}
                    ${v===`icon-text`?`background: transparent; padding: ${g}px 0;`:``}
                    ${v===`pill`?`background: ${f}; padding: ${g}px ${h}px; border-radius: 9999px;`:``}
                }
                .eazy-spec svg {
                    flex-shrink: 0;
                    display: ${v===`icon-text`?`inline-block`:`none`};
                }
                .eazy-powered {
                    text-align: center;
                    padding: 1rem;
                    font-size: 0.7rem;
                    color: #9ca3af;
                }
                .eazy-powered a {
                    color: #6b7280;
                    text-decoration: none;
                }
                .eazy-powered a:hover { text-decoration: underline; }

                /* Detail modal — uses detail overrides or card fallbacks */
                ${this.getDetailVarsComment()}
                .eazy-overlay {
                    position: fixed;
                    inset: 0;
                    background: rgba(0,0,0,${this.detailOpt(`detail_overlay_opacity`,60)/100});
                    z-index: 999999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 1rem;
                    animation: eazy-fade-in 0.2s ease;
                }
                @keyframes eazy-fade-in {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .eazy-modal {
                    background: ${b};
                    border-radius: ${x}px;
                    border: ${o}px solid ${a};
                    max-width: 800px;
                    width: 100%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
                    font-family: ${this.fontStack};
                }
                .eazy-modal-close {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    border: none;
                    background: rgba(0,0,0,0.5);
                    color: #fff;
                    font-size: 1.25rem;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10;
                    transition: background 0.15s;
                }
                .eazy-modal-close:hover { background: rgba(0,0,0,0.7); }
                .eazy-modal-gallery {
                    position: relative;
                    width: 100%;
                    height: ${E}px;
                    background: #f3f4f6;
                }
                @media (max-width: 640px) {
                    .eazy-modal-gallery { height: ${Math.round(E*.63)}px; }
                }
                .eazy-modal-gallery img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    border-radius: ${Math.max(x-1,0)}px ${Math.max(x-1,0)}px 0 0;
                }
                .eazy-modal-thumbs {
                    display: flex;
                    gap: 0.5rem;
                    padding: 0.75rem 1.5rem;
                    overflow-x: auto;
                    background: rgba(0,0,0,0.03);
                }
                .eazy-modal-thumb {
                    width: 64px;
                    height: 48px;
                    object-fit: cover;
                    border-radius: ${Math.min(m,6)}px;
                    cursor: pointer;
                    border: 2px solid transparent;
                    flex-shrink: 0;
                    opacity: 0.6;
                    transition: opacity 0.15s, border-color 0.15s;
                }
                .eazy-modal-thumb:hover,
                .eazy-modal-thumb.active {
                    opacity: 1;
                    border-color: ${this.primaryColor};
                }
                .eazy-modal-body { padding: ${S}px; }
                .eazy-modal-title {
                    font-size: ${C}px;
                    font-weight: 700;
                    color: ${w};
                    margin-bottom: 0.25rem;
                }
                .eazy-modal-price {
                    font-size: ${T}px;
                    font-weight: 700;
                    color: ${this.primaryColor};
                    margin-bottom: 1rem;
                }
                .eazy-modal-desc {
                    color: ${p};
                    font-size: 0.9375rem;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                    white-space: pre-line;
                }
                .eazy-modal-specs {
                    display: grid;
                    grid-template-columns: repeat(${D}, 1fr);
                    gap: 0.5rem 1.5rem;
                    margin-bottom: 1.5rem;
                }
                @media (max-width: 500px) {
                    .eazy-modal-specs { grid-template-columns: 1fr; }
                }
                .eazy-modal-spec {
                    display: flex;
                    justify-content: space-between;
                    padding: 0.5rem 0;
                    border-bottom: 1px solid ${a};
                    font-size: 0.875rem;
                }
                .eazy-modal-spec-label { color: ${p}; }
                .eazy-modal-spec-value { color: ${w}; font-weight: 500; }
                .eazy-modal-opties {
                    display: flex;
                    flex-wrap: wrap;
                    gap: ${_}px;
                    margin-bottom: 1rem;
                }
                .eazy-modal-optie {
                    display: inline-flex;
                    align-items: center;
                    font-size: 0.8rem;
                    color: ${p};
                    ${v===`badge`?`background: ${f}; padding: 0.3rem 0.75rem; border-radius: ${m}px;`:``}
                    ${v===`outline`?`background: transparent; border: 1px solid ${p}; padding: 0.3rem 0.75rem; border-radius: ${m}px;`:``}
                    ${v===`icon-text`?`background: transparent; padding: 0.3rem 0;`:``}
                    ${v===`pill`?`background: ${f}; padding: 0.3rem 0.75rem; border-radius: 9999px;`:``}
                }
                .eazy-modal-section-title {
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: ${w};
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-bottom: 0.75rem;
                }
                .eazy-modal-loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 4rem;
                    color: ${p};
                    font-size: 0.9375rem;
                }
            `}render(){let e=this.s.show_price!==!1,t=this.s.show_km!==!1,n=this.s.show_fuel!==!1;if(this.loadFont(),this.cars.length===0){this.shadow.innerHTML=this.emptyHTML();return}this.shadow.innerHTML=`
                <style>${this.getStyles()}</style>
                <div class="eazy-grid">
                    ${this.cars.map(r=>this.renderCard(r,{showPrice:e,showKm:t,showFuel:n})).join(``)}
                </div>
                <div class="eazy-powered">
                    Powered by <a href="https://eazyonline.nl" target="_blank" rel="noopener">Eazyonline</a>
                </div>
            `,this.shadow.querySelectorAll(`.eazy-card`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.getAttribute(`data-car-id`);t&&this.openDetail(parseInt(t,10))})}),this.cars.forEach(e=>this.trackView(e.id))}renderCard(e,t){let n=this.opt(`image_position`,`top`),r=e.image?`<img class="eazy-card-img" src="${this.escapeHtml(e.image)}" alt="${this.escapeHtml(e.title)}" loading="lazy">`:`<div class="eazy-no-img">Geen afbeelding</div>`,i=[];e.bouwjaar&&i.push({type:`year`,value:e.bouwjaar}),t.showFuel&&e.brandstof&&i.push({type:`fuel`,value:e.brandstof}),t.showKm&&e.km_stand&&i.push({type:`km`,value:e.km_stand.toLocaleString(`nl-NL`)+` km`}),e.kleur&&i.push({type:`color`,value:e.kleur});let a=`
                <div class="eazy-card-body">
                    <div class="eazy-card-title">${this.escapeHtml(e.title)}</div>
                    ${t.showPrice?`<div class="eazy-card-price">${this.escapeHtml(this.formatPrice(e.prijs))}</div>`:``}
                    ${i.length>0?`
                        <div class="eazy-card-specs">
                            ${i.map(e=>`<span class="eazy-spec">${o[e.type]||``}${this.escapeHtml(String(e.value))}</span>`).join(``)}
                        </div>
                    `:``}
                </div>
            `;return`
                <div class="eazy-card" data-car-id="${e.id}">
                    ${n===`top`?r+a:a+r}
                </div>
            `}async openDetail(e){let t=document.createElement(`div`);t.className=`eazy-overlay`,t.innerHTML=`
                <div class="eazy-modal">
                    <button class="eazy-modal-close" aria-label="Sluiten">&times;</button>
                    <div class="eazy-modal-loading">Auto laden...</div>
                </div>
            `,this.shadow.appendChild(t),this.bindModalClose(t);let n=await this.fetchCarDetail(e);if(!n){t.remove();return}this.renderDetail(t,n)}renderDetail(e,t){let n=t.images||[],r=n[0]||null,i=[];t.merk&&i.push([`Merk`,t.merk]),t.model&&i.push([`Model`,t.model]),t.bouwjaar&&i.push([`Bouwjaar`,t.bouwjaar]),t.km_stand&&i.push([`Kilometerstand`,t.km_stand.toLocaleString(`nl-NL`)+` km`]),t.brandstof&&i.push([`Brandstof`,t.brandstof]),t.kleur&&i.push([`Kleur`,t.kleur]),t.tweede_kleur&&i.push([`Tweede kleur`,t.tweede_kleur]),t.inrichting&&i.push([`Carrosserie`,t.inrichting]),t.vermogen&&i.push([`Vermogen`,t.vermogen+` kW`]),t.cilinderinhoud&&i.push([`Cilinderinhoud`,t.cilinderinhoud+` cc`]),t.aantal_zitplaatsen&&i.push([`Zitplaatsen`,t.aantal_zitplaatsen]),t.aantal_deuren&&i.push([`Deuren`,t.aantal_deuren]),t.apk_tot&&i.push([`APK tot`,t.apk_tot]),t.datum_eerste_toelating&&i.push([`1e toelating`,t.datum_eerste_toelating]),t.kenteken&&i.push([`Kenteken`,t.kenteken]);let a=e.querySelector(`.eazy-modal`);a.innerHTML=`
                <button class="eazy-modal-close" aria-label="Sluiten">&times;</button>

                ${r?`
                    <div class="eazy-modal-gallery">
                        <img src="${this.escapeHtml(r)}" alt="${this.escapeHtml(t.title)}" id="eazy-detail-main-img">
                    </div>
                `:``}

                ${n.length>1?`
                    <div class="eazy-modal-thumbs">
                        ${n.map((e,t)=>`
                            <img class="eazy-modal-thumb${t===0?` active`:``}" src="${this.escapeHtml(e)}" data-idx="${t}" alt="">
                        `).join(``)}
                    </div>
                `:``}

                <div class="eazy-modal-body">
                    <div class="eazy-modal-title">${this.escapeHtml(t.title)}</div>
                    <div class="eazy-modal-price">${this.escapeHtml(this.formatPrice(t.prijs))}</div>

                    ${t.beschrijving?`
                        <div class="eazy-modal-desc">${this.escapeHtml(t.beschrijving)}</div>
                    `:``}

                    ${i.length>0?`
                        <div class="eazy-modal-section-title">Specificaties</div>
                        <div class="eazy-modal-specs">
                            ${i.map(([e,t])=>`
                                <div class="eazy-modal-spec">
                                    <span class="eazy-modal-spec-label">${this.escapeHtml(e)}</span>
                                    <span class="eazy-modal-spec-value">${this.escapeHtml(String(t))}</span>
                                </div>
                            `).join(``)}
                        </div>
                    `:``}

                    ${t.extra_opties&&t.extra_opties.length>0?`
                        <div class="eazy-modal-section-title">Opties & Accessoires</div>
                        <div class="eazy-modal-opties">
                            ${t.extra_opties.map(e=>`<span class="eazy-modal-optie">${this.escapeHtml(e)}</span>`).join(``)}
                        </div>
                    `:``}
                </div>
            `,this.bindModalClose(e);let o=e.querySelectorAll(`.eazy-modal-thumb`),s=e.querySelector(`#eazy-detail-main-img`);o.length>0&&s&&o.forEach(e=>{e.addEventListener(`click`,t=>{t.stopPropagation(),s.src=e.src,o.forEach(e=>e.classList.remove(`active`)),e.classList.add(`active`)})})}bindModalClose(e){let t=e.querySelector(`.eazy-modal-close`);t&&t.addEventListener(`click`,t=>{t.stopPropagation(),e.remove()}),e.addEventListener(`click`,t=>{t.target===e&&e.remove()});let n=t=>{t.key===`Escape`&&(e.remove(),document.removeEventListener(`keydown`,n))};document.addEventListener(`keydown`,n)}async trackView(e){try{await fetch(`${n}/api/embed/v1/cars/${e}/view`,{method:`POST`,headers:{"Content-Type":`application/json`,"X-Api-Key":t}})}catch{}}escapeHtml(e){if(!e)return``;let t=document.createElement(`div`);return t.textContent=e,t.innerHTML}loadingHTML(){return`<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Aanbod laden...</div>`}errorHTML(){return`<div style="text-align:center;padding:2rem;color:#ef4444;font-family:sans-serif;">Kon het aanbod niet laden. Probeer het later opnieuw.</div>`}emptyHTML(){return`<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Momenteel geen auto's beschikbaar.</div>`}}new u})()})();