(function(){(function(){"use strict";let e=document.currentScript,t=e?.getAttribute(`data-api-key`),n=(e?.getAttribute(`data-base-url`)||``).replace(/\/$/,``),r=e?.getAttribute(`data-container`)||`eazy-automotive-widget`;if(!t){console.error(`[EazyAutomotive] data-api-key attribuut ontbreekt op het script tag.`);return}if(!n){console.error(`[EazyAutomotive] data-base-url attribuut ontbreekt op het script tag.`);return}let i={none:`none`,sm:`0 1px 3px rgba(0,0,0,0.08)`,md:`0 4px 12px rgba(0,0,0,0.1)`,lg:`0 10px 30px rgba(0,0,0,0.15)`},a={EUR:`€`,USD:`$`,GBP:`£`,none:``},o={year:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,fuel:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2h0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>`,km:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,color:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`},s={Merk:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Model:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Bouwjaar:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,Kilometerstand:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>`,Brandstof:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2v0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>`,Kleur:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`,"Tweede kleur":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`,Carrosserie:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Vermogen:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>`,Cilinderinhoud:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>`,Zitplaatsen:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>`,Deuren:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg>`,"APK tot":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,"1e toelating":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,Kenteken:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`},c=`-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`,l=[`Inter`,`Roboto`,`Open Sans`,`Lato`,`Montserrat`,`Poppins`];function u(e){if(!e||e===`system`||!l.includes(e))return;let t=`eazy-gfont-`+e.replace(/\s/g,`-`).toLowerCase();if(document.getElementById(t))return;let n=document.createElement(`link`);n.id=t,n.rel=`stylesheet`,n.href=`https://fonts.googleapis.com/css2?family=${e.replace(/\s/g,`+`)}:wght@400;500;600;700&display=swap`,document.head.appendChild(n)}class d{constructor(){this.cars=[],this.settings={},this.companyInfo={},this.init()}async init(){let t=document.getElementById(r);t||(t=document.createElement(`div`),t.id=r,e.parentNode.insertBefore(t,e)),this.shadow=t.attachShadow({mode:`open`}),this.shadow.innerHTML=this.loadingHTML(),await this.fetchCars(),this.render()}async fetchCars(e=1){try{let r=`${n}/api/embed/v1/cars?api_key=${encodeURIComponent(t)}&page=${e}`,i=await fetch(r);if(!i.ok)throw Error(`HTTP ${i.status}`);let a=await i.json();this.cars=a.cars?.data||[],this.settings=a.settings||{},this.companyInfo=a.company||{},this.pagination=a.cars||{}}catch(e){console.error(`[EazyAutomotive]`,e),this.shadow.innerHTML=this.errorHTML()}}async fetchCarDetail(e){try{let r=`${n}/api/embed/v1/cars/${e}?api_key=${encodeURIComponent(t)}`,i=await fetch(r);if(!i.ok)throw Error(`HTTP ${i.status}`);return(await i.json()).car}catch(e){return console.error(`[EazyAutomotive] Detail fetch error:`,e),null}}get s(){return this.settings}opt(e,t){return this.s[e]??t}get primaryColor(){return this.opt(`primary_color`,`#4f46e5`)}get fontStack(){let e=this.opt(`font_family`,`system`);return e===`system`?c:`'${e}', ${c}`}formatPrice(e){if(!e)return``;let t=a[this.opt(`currency`,`EUR`)]??`€`,n=e.replace(/^[€$£]\s*/,``);return t?`${t} ${n}`:n}loadFont(){u(this.opt(`font_family`,`system`))}get detailCustom(){return this.opt(`detail_custom`,!1)}detailOpt(e,t){return this.detailCustom?this.opt(e,t):t}getDetailVarsComment(){return``}getHoverCSS(){let e=this.opt(`hover_effect`,`lift`),t=this.primaryColor;switch(e){case`lift`:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`;case`shadow`:return`.eazy-card:hover { box-shadow: 0 12px 35px rgba(0,0,0,0.18); }`;case`scale`:return`.eazy-card:hover { transform: scale(1.03); }`;case`glow`:return`.eazy-card:hover { box-shadow: 0 0 20px ${t}44; }`;case`none`:return``;default:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`}}getStyles(){let e=this.opt(`columns`,3),t=this.opt(`card_bg_color`,`#ffffff`),n=this.opt(`card_border_radius`,12),r=this.opt(`card_padding`,16),a=this.opt(`card_border_color`,`#e5e7eb`),o=this.opt(`card_border_width`,1),s=i[this.opt(`card_shadow`,`none`)]||`none`,c=this.opt(`image_height`,200),l=this.opt(`title_size`,16),u=this.opt(`title_color`,`#111827`),d=this.opt(`price_size`,20),f=this.opt(`label_bg_color`,`#f3f4f6`),p=this.opt(`label_text_color`,`#4b5563`),m=this.opt(`label_radius`,4),h=this.opt(`label_padding_x`,8),g=this.opt(`label_padding_y`,3),_=this.opt(`label_gap`,6),v=this.opt(`label_style`,`badge`),y=this.detailCustom,b=y?this.opt(`detail_bg_color`,t):t,x=y?this.opt(`detail_border_color`,a):a,S=y?this.opt(`detail_border_width`,o):o,C=y?this.opt(`detail_border_radius`,n):n,w=y?this.opt(`detail_padding`,r):r,T=y?this.opt(`detail_title_size`,24):24,E=y?this.opt(`detail_title_color`,u):u,D=this.opt(`detail_subtitle_color`,`#9ca3af`),O=y?this.opt(`detail_price_size`,24):24,k=y?this.opt(`detail_price_color`,this.primaryColor):this.primaryColor,A=this.opt(`detail_desc_color`,`#6b7280`),j=this.opt(`detail_desc_size`,14),M=y?this.opt(`detail_gallery_height`,350):350,N=y?this.opt(`detail_spec_columns`,2):2,P=this.opt(`detail_spec_bg_color`,`#f9fafb`),F=this.opt(`detail_spec_label_color`,`#6b7280`),I=this.opt(`detail_spec_value_color`,E),L=this.opt(`detail_spec_radius`,6),R=this.opt(`detail_spec_gap`,6),z=this.opt(`detail_badge_style`,`pill`),B=this.opt(`detail_badge_bg_color`,`#f3f4f6`),V=this.opt(`detail_badge_text_color`,k),H=this.opt(`detail_badge_radius`,4),U={none:`none`,sm:`0 4px 12px rgba(0,0,0,0.1)`,md:`0 10px 25px rgba(0,0,0,0.18)`,lg:`0 25px 50px rgba(0,0,0,0.25)`},W=U[this.opt(`detail_shadow`,`lg`)]||U.lg;return`
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
                    border-radius: ${C}px;
                    border: ${S}px solid ${x};
                    max-width: 800px;
                    width: 100%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    box-shadow: ${W};
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
                    height: ${M}px;
                    background: #f3f4f6;
                    overflow: hidden;
                }
                @media (max-width: 640px) {
                    .eazy-modal-gallery { height: ${Math.round(M*.63)}px; }
                }
                .eazy-modal-gallery > img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    border-radius: ${Math.max(C-1,0)}px ${Math.max(C-1,0)}px 0 0;
                }
                .eazy-modal-thumbs {
                    position: absolute;
                    bottom: 10px;
                    left: 50%;
                    transform: translateX(-50%);
                    display: flex;
                    gap: 6px;
                    z-index: 5;
                }
                .eazy-modal-thumb {
                    width: 40px;
                    height: 30px;
                    object-fit: cover;
                    border-radius: 4px;
                    cursor: pointer;
                    border: 2px solid rgba(255,255,255,0.6);
                    flex-shrink: 0;
                    opacity: 0.7;
                    transition: opacity 0.15s, border-color 0.15s;
                }
                .eazy-modal-thumb:hover,
                .eazy-modal-thumb.active {
                    opacity: 1;
                    border-color: #fff;
                }
                .eazy-modal-body { padding: ${w}px; }
                .eazy-modal-title {
                    font-size: ${T}px;
                    font-weight: 700;
                    color: ${E};
                    margin-bottom: 0.25rem;
                }
                .eazy-modal-subtitle {
                    font-size: 0.8125rem;
                    color: ${D};
                    margin-bottom: 0.75rem;
                }
                .eazy-modal-price {
                    font-size: ${O}px;
                    font-weight: 800;
                    color: ${k};
                    margin-bottom: 1rem;
                }
                .eazy-modal-section-title {
                    font-size: 0.6875rem;
                    font-weight: 600;
                    color: ${D};
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-bottom: 0.5rem;
                }
                .eazy-modal-specs {
                    display: grid;
                    grid-template-columns: repeat(${N}, 1fr);
                    gap: ${R}px;
                    margin-bottom: 1.5rem;
                }
                @media (max-width: 500px) {
                    .eazy-modal-specs { grid-template-columns: 1fr; }
                }
                .eazy-modal-spec {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0.5rem 0.75rem;
                    background: ${P};
                    border-radius: ${L}px;
                    font-size: 0.8125rem;
                }
                .eazy-modal-spec-label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    color: ${F};
                }
                .eazy-modal-spec-label svg {
                    flex-shrink: 0;
                    color: ${F};
                }
                .eazy-modal-spec-value { color: ${I}; font-weight: 600; }
                .eazy-modal-desc {
                    color: ${A};
                    font-size: ${j}px;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                    white-space: pre-line;
                }
                .eazy-modal-opties {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                    margin-bottom: 1rem;
                }
                .eazy-modal-optie {
                    display: inline-flex;
                    align-items: center;
                    font-size: 0.8rem;
                    font-weight: 500;
                    color: ${V};
                    ${z===`pill`?`background: ${B}; padding: 0.3rem 0.75rem; border-radius: 9999px;`:``}
                    ${z===`badge`?`background: ${B}; padding: 0.3rem 0.75rem; border-radius: ${H}px;`:``}
                    ${z===`outline`?`background: transparent; border: 1px solid ${V}; padding: 0.3rem 0.75rem; border-radius: ${H}px;`:``}
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
            `,this.shadow.appendChild(t),this.bindModalClose(t);let n=await this.fetchCarDetail(e);if(!n){t.remove();return}this.renderDetail(t,n)}renderDetail(e,t){let n=t.images||[],r=n[0]||null,i=this.opt(`detail_show_subtitle`,!0)!==!1&&this.opt(`detail_show_subtitle`,!0)!==0,a=this.opt(`detail_show_specs`,!0)!==!1&&this.opt(`detail_show_specs`,!0)!==0,o=this.opt(`detail_show_description`,!0)!==!1&&this.opt(`detail_show_description`,!0)!==0,c=this.opt(`detail_show_options`,!0)!==!1&&this.opt(`detail_show_options`,!0)!==0,l=[];t.bouwjaar&&l.push(t.bouwjaar),t.brandstof&&l.push(t.brandstof),t.km_stand&&l.push(t.km_stand.toLocaleString(`nl-NL`)+` km`);let u=[];t.bouwjaar&&u.push([`Bouwjaar`,t.bouwjaar]),t.km_stand&&u.push([`Kilometerstand`,t.km_stand.toLocaleString(`nl-NL`)+` km`]),t.brandstof&&u.push([`Brandstof`,t.brandstof]),t.kleur&&u.push([`Kleur`,t.kleur]),t.tweede_kleur&&u.push([`Tweede kleur`,t.tweede_kleur]),t.inrichting&&u.push([`Carrosserie`,t.inrichting]),t.vermogen&&u.push([`Vermogen`,t.vermogen+` kW`]),t.cilinderinhoud&&u.push([`Cilinderinhoud`,t.cilinderinhoud+` cc`]),t.aantal_zitplaatsen&&u.push([`Zitplaatsen`,t.aantal_zitplaatsen]),t.aantal_deuren&&u.push([`Deuren`,t.aantal_deuren]),t.apk_tot&&u.push([`APK tot`,t.apk_tot]),t.datum_eerste_toelating&&u.push([`1e toelating`,t.datum_eerste_toelating]),t.kenteken&&u.push([`Kenteken`,t.kenteken]);let d=e.querySelector(`.eazy-modal`);d.innerHTML=`
                <button class="eazy-modal-close" aria-label="Sluiten">&times;</button>

                ${r?`
                    <div class="eazy-modal-gallery">
                        <img src="${this.escapeHtml(r)}" alt="${this.escapeHtml(t.title)}" id="eazy-detail-main-img">
                        ${n.length>1?`
                            <div class="eazy-modal-thumbs">
                                ${n.map((e,t)=>`
                                    <img class="eazy-modal-thumb${t===0?` active`:``}" src="${this.escapeHtml(e)}" data-idx="${t}" alt="">
                                `).join(``)}
                            </div>
                        `:``}
                    </div>
                `:``}

                <div class="eazy-modal-body">
                    <div class="eazy-modal-title">${this.escapeHtml(t.title)}</div>
                    ${i&&l.length>0?`
                        <div class="eazy-modal-subtitle">${l.map(e=>this.escapeHtml(String(e))).join(` &middot; `)}</div>
                    `:``}
                    <div class="eazy-modal-price">${this.escapeHtml(this.formatPrice(t.prijs))}</div>

                    ${a&&u.length>0?`
                        <div class="eazy-modal-section-title">Specificaties</div>
                        <div class="eazy-modal-specs">
                            ${u.map(([e,t])=>`
                                <div class="eazy-modal-spec">
                                    <span class="eazy-modal-spec-label">${s[e]||`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>`}${this.escapeHtml(e)}</span>
                                    <span class="eazy-modal-spec-value">${this.escapeHtml(String(t))}</span>
                                </div>
                            `).join(``)}
                        </div>
                    `:``}

                    ${o&&t.beschrijving?`
                        <div class="eazy-modal-section-title">Beschrijving</div>
                        <div class="eazy-modal-desc">${this.escapeHtml(t.beschrijving)}</div>
                    `:``}

                    ${c&&t.extra_opties&&t.extra_opties.length>0?`
                        <div class="eazy-modal-section-title">Opties</div>
                        <div class="eazy-modal-opties">
                            ${t.extra_opties.map(e=>`<span class="eazy-modal-optie">${this.escapeHtml(e)}</span>`).join(``)}
                        </div>
                    `:``}
                </div>
            `,this.bindModalClose(e);let f=e.querySelectorAll(`.eazy-modal-thumb`),p=e.querySelector(`#eazy-detail-main-img`);f.length>0&&p&&f.forEach(e=>{e.addEventListener(`click`,t=>{t.stopPropagation(),p.src=e.src,f.forEach(e=>e.classList.remove(`active`)),e.classList.add(`active`)})})}bindModalClose(e){let t=e.querySelector(`.eazy-modal-close`);t&&t.addEventListener(`click`,t=>{t.stopPropagation(),e.remove()}),e.addEventListener(`click`,t=>{t.target===e&&e.remove()});let n=t=>{t.key===`Escape`&&(e.remove(),document.removeEventListener(`keydown`,n))};document.addEventListener(`keydown`,n)}async trackView(e){try{await fetch(`${n}/api/embed/v1/cars/${e}/view`,{method:`POST`,headers:{"Content-Type":`application/json`,"X-Api-Key":t}})}catch{}}escapeHtml(e){if(!e)return``;let t=document.createElement(`div`);return t.textContent=e,t.innerHTML}loadingHTML(){return`<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Aanbod laden...</div>`}errorHTML(){return`<div style="text-align:center;padding:2rem;color:#ef4444;font-family:sans-serif;">Kon het aanbod niet laden. Probeer het later opnieuw.</div>`}emptyHTML(){return`<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Momenteel geen auto's beschikbaar.</div>`}}new d})()})();