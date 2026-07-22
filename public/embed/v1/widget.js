(function(){(function(){"use strict";let e=document.currentScript,t=e?.getAttribute(`data-api-key`),n=(e?.getAttribute(`data-base-url`)||``).replace(/\/$/,``),r=e?.getAttribute(`data-container`)||`eazy-automotive-widget`;if(!t){console.error(`[EazyAutomotive] data-api-key attribuut ontbreekt op het script tag.`);return}if(!n){console.error(`[EazyAutomotive] data-base-url attribuut ontbreekt op het script tag.`);return}let i={none:`none`,sm:`0 1px 3px rgba(0,0,0,0.08)`,md:`0 4px 12px rgba(0,0,0,0.1)`,lg:`0 10px 30px rgba(0,0,0,0.15)`},a={EUR:`€`,USD:`$`,GBP:`£`,none:``},o={year:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,fuel:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2h0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>`,km:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,color:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`},s={Merk:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Model:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Bouwjaar:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,Kilometerstand:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>`,Brandstof:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2v0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>`,Kleur:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`,"Tweede kleur":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>`,Carrosserie:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>`,Vermogen:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>`,Cilinderinhoud:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>`,Zitplaatsen:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>`,Deuren:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg>`,"APK tot":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,"1e toelating":`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,Kenteken:`<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`},c=`-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`,l=[`Inter`,`Roboto`,`Open Sans`,`Lato`,`Montserrat`,`Poppins`];function u(e){if(!e||e===`system`||!l.includes(e))return;let t=`eazy-gfont-`+e.replace(/\s/g,`-`).toLowerCase();if(document.getElementById(t))return;let n=document.createElement(`link`);n.id=t,n.rel=`stylesheet`,n.href=`https://fonts.googleapis.com/css2?family=${e.replace(/\s/g,`+`)}:wght@400;500;600;700&display=swap`,document.head.appendChild(n)}class d{constructor(){this.cars=[],this.settings={},this.companyInfo={},this.facets={merken:[],brandstoffen:[],prijs_max:null},this.pagination={},this.filters={search:``,merk:``,brandstof:``,prijs_max:``,sort:`nieuwste`,page:1},this.init()}async init(){let t=document.getElementById(r);if(t||(t=document.createElement(`div`),t.id=r,e.parentNode.insertBefore(t,e)),this.shadow=t.attachShadow({mode:`open`}),this.shadow.innerHTML=this.loadingHTML(),!await this.fetchCars()){this.shadow.innerHTML=this.errorHTML();return}this.loadFont(),this.renderShell()}async fetchCars(){try{let e=new URLSearchParams({api_key:t,page:this.filters.page});this.filters.search&&e.set(`search`,this.filters.search),this.filters.merk&&e.set(`merk`,this.filters.merk),this.filters.brandstof&&e.set(`brandstof`,this.filters.brandstof),this.filters.prijs_max&&e.set(`prijs_max`,this.filters.prijs_max),this.filters.sort&&this.filters.sort!==`nieuwste`&&e.set(`sort`,this.filters.sort);let r=await fetch(`${n}/api/embed/v1/cars?${e.toString()}`);if(!r.ok)throw Error(`HTTP ${r.status}`);let i=await r.json();return this.cars=i.cars?.data||[],this.settings=i.settings||{},this.companyInfo=i.company||{},this.facets=i.filters||this.facets,this.pagination=i.cars||{},!0}catch(e){return console.error(`[EazyAutomotive]`,e),!1}}async fetchCarDetail(e){try{let r=`${n}/api/embed/v1/cars/${e}?api_key=${encodeURIComponent(t)}`,i=await fetch(r);if(!i.ok)throw Error(`HTTP ${i.status}`);return(await i.json()).car}catch(e){return console.error(`[EazyAutomotive] Detail fetch error:`,e),null}}get s(){return this.settings}opt(e,t){return this.s[e]??t}get primaryColor(){return this.opt(`primary_color`,`#4f46e5`)}get fontStack(){let e=this.opt(`font_family`,`system`);return e===`system`?c:`'${e}', ${c}`}formatPrice(e){if(!e)return``;let t=a[this.opt(`currency`,`EUR`)]??`€`,n=e.replace(/^[€$£]\s*/,``);return t?`${t} ${n}`:n}loadFont(){u(this.opt(`font_family`,`system`))}get detailCustom(){return this.opt(`detail_custom`,!1)}detailOpt(e,t){return this.detailCustom?this.opt(e,t):t}getDetailVarsComment(){return``}getHoverCSS(){let e=this.opt(`hover_effect`,`lift`),t=this.primaryColor;switch(e){case`lift`:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`;case`shadow`:return`.eazy-card:hover { box-shadow: 0 12px 35px rgba(0,0,0,0.18); }`;case`scale`:return`.eazy-card:hover { transform: scale(1.03); }`;case`glow`:return`.eazy-card:hover { box-shadow: 0 0 20px ${t}44; }`;case`none`:return``;default:return`.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }`}}getStyles(){let e=this.opt(`columns`,3),t=this.opt(`card_bg_color`,`#ffffff`),n=this.opt(`card_border_radius`,12),r=this.opt(`card_padding`,16),a=this.opt(`card_border_color`,`#e5e7eb`),o=this.opt(`card_border_width`,1),s=i[this.opt(`card_shadow`,`none`)]||`none`,c=this.opt(`image_height`,200),l=this.opt(`title_size`,16),u=this.opt(`title_color`,`#111827`),d=this.opt(`price_size`,20),f=this.opt(`label_bg_color`,`#f3f4f6`),p=this.opt(`label_text_color`,`#4b5563`),m=this.opt(`label_radius`,4),h=this.opt(`label_padding_x`,8),g=this.opt(`label_padding_y`,3),_=this.opt(`label_gap`,6),v=this.opt(`label_style`,`badge`),y=this.opt(`card_layout`,`classic`)===`list`?Math.min(2,e):e,b=this.detailCustom,x=b?this.opt(`detail_bg_color`,t):t,S=b?this.opt(`detail_border_color`,a):a,C=b?this.opt(`detail_border_width`,o):o,w=b?this.opt(`detail_border_radius`,n):n,T=b?this.opt(`detail_padding`,r):r,E=b?this.opt(`detail_title_size`,24):24,D=b?this.opt(`detail_title_color`,u):u,O=this.opt(`detail_subtitle_color`,`#9ca3af`),k=b?this.opt(`detail_price_size`,24):24,A=b?this.opt(`detail_price_color`,this.primaryColor):this.primaryColor,j=this.opt(`detail_desc_color`,`#6b7280`),M=this.opt(`detail_desc_size`,14),N=b?this.opt(`detail_gallery_height`,350):350,P=b?this.opt(`detail_spec_columns`,2):2,F=this.opt(`detail_spec_bg_color`,`#f9fafb`),I=this.opt(`detail_spec_label_color`,`#6b7280`),L=this.opt(`detail_spec_value_color`,D),R=this.opt(`detail_spec_radius`,6),z=this.opt(`detail_spec_gap`,6),B=this.opt(`detail_badge_style`,`pill`),V=this.opt(`detail_badge_bg_color`,`#f3f4f6`),H=this.opt(`detail_badge_text_color`,A),U=this.opt(`detail_badge_radius`,4),W={none:`none`,sm:`0 4px 12px rgba(0,0,0,0.1)`,md:`0 10px 25px rgba(0,0,0,0.18)`,lg:`0 25px 50px rgba(0,0,0,0.25)`},G=W[this.opt(`detail_shadow`,`lg`)]||W.lg;return`
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                :host { display: block; font-family: ${this.fontStack}; }
                .eazy-grid {
                    display: grid;
                    grid-template-columns: repeat(${y}, 1fr);
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
                /* Layout: horizontale lijst */
                .eazy-card[data-layout="list"] { display: flex; }
                .eazy-card[data-layout="list"] .eazy-card-img { width: 42%; height: auto; align-self: stretch; object-fit: cover; }
                .eazy-card[data-layout="list"] .eazy-no-img { width: 42%; height: auto; align-self: stretch; }
                .eazy-card[data-layout="list"] .eazy-card-body { flex: 1; display: flex; flex-direction: column; justify-content: center; }
                /* Layout: magazine (tekst over de foto) */
                .eazy-card[data-layout="overlay"] { position: relative; }
                .eazy-card[data-layout="overlay"] .eazy-card-body {
                    position: absolute; left: 0; right: 0; bottom: 0;
                    padding-top: 3rem;
                    background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.25) 65%, transparent);
                }
                .eazy-card[data-layout="overlay"] .eazy-card-title { color: #ffffff; text-shadow: 0 1px 6px rgba(0,0,0,0.65); }
                .eazy-card[data-layout="overlay"] .eazy-card-price { text-shadow: 0 1px 6px rgba(0,0,0,0.7); }
                .eazy-card[data-layout="overlay"] .eazy-spec { color: #ffffff; background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.45); }
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

                /* Detail modal: uses detail overrides or card fallbacks */
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
                    background: ${x};
                    border-radius: ${w}px;
                    border: ${C}px solid ${S};
                    max-width: 800px;
                    width: 100%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    box-shadow: ${G};
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
                    height: ${N}px;
                    background: #f3f4f6;
                    overflow: hidden;
                }
                @media (max-width: 640px) {
                    .eazy-modal-gallery { height: ${Math.round(N*.63)}px; }
                }
                .eazy-modal-gallery > img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    border-radius: ${Math.max(w-1,0)}px ${Math.max(w-1,0)}px 0 0;
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
                .eazy-modal-body { padding: ${T}px; }
                .eazy-modal-title {
                    font-size: ${E}px;
                    font-weight: 700;
                    color: ${D};
                    margin-bottom: 0.25rem;
                }
                .eazy-modal-subtitle {
                    font-size: 0.8125rem;
                    color: ${O};
                    margin-bottom: 0.75rem;
                }
                .eazy-modal-price {
                    font-size: ${k}px;
                    font-weight: 800;
                    color: ${A};
                    margin-bottom: 1rem;
                }
                .eazy-modal-section-title {
                    font-size: 0.6875rem;
                    font-weight: 600;
                    color: ${O};
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-bottom: 0.5rem;
                }
                .eazy-modal-specs {
                    display: grid;
                    grid-template-columns: repeat(${P}, 1fr);
                    gap: ${z}px;
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
                    background: ${F};
                    border-radius: ${R}px;
                    font-size: 0.8125rem;
                }
                .eazy-modal-spec-label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    color: ${I};
                }
                .eazy-modal-spec-label svg {
                    flex-shrink: 0;
                    color: ${I};
                }
                .eazy-modal-spec-value { color: ${L}; font-weight: 600; }
                .eazy-modal-desc {
                    color: ${j};
                    font-size: ${M}px;
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
                    color: ${H};
                    ${B===`pill`?`background: ${V}; padding: 0.3rem 0.75rem; border-radius: 9999px;`:``}
                    ${B===`badge`?`background: ${V}; padding: 0.3rem 0.75rem; border-radius: ${U}px;`:``}
                    ${B===`outline`?`background: transparent; border: 1px solid ${H}; padding: 0.3rem 0.75rem; border-radius: ${U}px;`:``}
                }
                .eazy-modal-loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 4rem;
                    color: ${p};
                    font-size: 0.9375rem;
                }

                /* Toolbar, pagination and loading skeleton */
                .eazy-toolbar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding-top: 0.25rem; }
                .eazy-search { position: relative; flex: 1 1 220px; min-width: 160px; }
                .eazy-search svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
                .eazy-search .eazy-field { width: 100%; padding-left: 34px; }
                .eazy-field { padding: 0.55rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.85rem; color: #374151; background: #fff; font-family: inherit; outline: none; }
                .eazy-field:focus { border-color: ${this.primaryColor}; box-shadow: 0 0 0 3px ${this.primaryColor}22; }
                .eazy-reset { background: none; border: none; color: ${this.primaryColor}; font-size: 0.82rem; font-weight: 600; cursor: pointer; padding: 0.4rem 0.5rem; font-family: inherit; }
                .eazy-reset:hover { text-decoration: underline; }
                .eazy-resultbar { font-size: 0.8rem; color: #6b7280; margin: 0.75rem 0 0; }
                .eazy-pagination { display: flex; align-items: center; justify-content: center; gap: 14px; padding: 1.25rem 0 0.5rem; }
                .eazy-page-btn { padding: 0.5rem 1.1rem; border: 1px solid #e5e7eb; border-radius: 9999px; background: #fff; font-size: 0.85rem; color: #374151; cursor: pointer; font-family: inherit; transition: border-color .15s, color .15s; }
                .eazy-page-btn:disabled { opacity: 0.4; cursor: default; }
                .eazy-page-btn:not(:disabled):hover { border-color: ${this.primaryColor}; color: ${this.primaryColor}; }
                .eazy-page-info { font-size: 0.85rem; color: #6b7280; }
                .eazy-empty { text-align: center; padding: 2.5rem 1rem; color: #9ca3af; font-size: 0.9rem; }
                .eazy-skel { border: 1px solid #eee; border-radius: ${n}px; overflow: hidden; background: #fff; }
                .eazy-skel-img { height: ${c}px; background: linear-gradient(90deg,#f3f4f6 25%,#eaecef 50%,#f3f4f6 75%); background-size: 200% 100%; animation: eazy-shimmer 1.3s infinite; }
                .eazy-skel-line { height: 12px; margin: 12px; border-radius: 6px; background: linear-gradient(90deg,#f3f4f6 25%,#eaecef 50%,#f3f4f6 75%); background-size: 200% 100%; animation: eazy-shimmer 1.3s infinite; }
                @keyframes eazy-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
            `}renderShell(){let e=(this.facets.merken||[]).map(e=>`<option value="${this.escapeHtml(e)}">${this.escapeHtml(e)}</option>`).join(``),t=(this.facets.brandstoffen||[]).map(e=>`<option value="${this.escapeHtml(e)}">${this.escapeHtml(e)}</option>`).join(``);this.shadow.innerHTML=`
                <style>${this.getStyles()}</style>
                <div class="eazy-toolbar">
                    <div class="eazy-search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input class="eazy-field" type="search" id="eazy-q" placeholder="Zoek op merk, model of kenteken" value="${this.escapeHtml(this.filters.search)}">
                    </div>
                    <select class="eazy-field" id="eazy-merk"><option value="">Alle merken</option>${e}</select>
                    <select class="eazy-field" id="eazy-brandstof"><option value="">Alle brandstof</option>${t}</select>
                    <select class="eazy-field" id="eazy-sort">
                        <option value="nieuwste">Nieuwste eerst</option>
                        <option value="prijs_op">Prijs oplopend</option>
                        <option value="prijs_af">Prijs aflopend</option>
                        <option value="bouwjaar">Bouwjaar</option>
                        <option value="km">Km-stand</option>
                    </select>
                    <button class="eazy-reset" id="eazy-reset" type="button">Wissen</button>
                </div>
                <div id="eazy-results"></div>
                <div class="eazy-powered">
                    Powered by <a href="https://eazyonline.nl" target="_blank" rel="noopener">Eazyonline</a>
                </div>
            `,this.bindToolbar(),this.renderResults()}bindToolbar(){let e=this.shadow.getElementById(`eazy-q`),t=this.shadow.getElementById(`eazy-merk`),n=this.shadow.getElementById(`eazy-brandstof`),r=this.shadow.getElementById(`eazy-sort`),i=this.shadow.getElementById(`eazy-reset`);t&&(t.value=this.filters.merk),n&&(n.value=this.filters.brandstof),r&&(r.value=this.filters.sort);let a;e&&e.addEventListener(`input`,()=>{clearTimeout(a),a=setTimeout(()=>{this.filters.search=e.value.trim(),this.applyFilters()},350)}),t&&t.addEventListener(`change`,()=>{this.filters.merk=t.value,this.applyFilters()}),n&&n.addEventListener(`change`,()=>{this.filters.brandstof=n.value,this.applyFilters()}),r&&r.addEventListener(`change`,()=>{this.filters.sort=r.value,this.applyFilters()}),i&&i.addEventListener(`click`,()=>{this.filters={search:``,merk:``,brandstof:``,prijs_max:``,sort:`nieuwste`,page:1},e&&(e.value=``),t&&(t.value=``),n&&(n.value=``),r&&(r.value=`nieuwste`),this.applyFilters()})}async applyFilters(){this.filters.page=1;let e=this.shadow.getElementById(`eazy-results`);e&&(e.innerHTML=this.skeletonHTML()),await this.fetchCars(),this.renderResults()}async goToPage(e){if(e<1||this.pagination.last_page&&e>this.pagination.last_page)return;this.filters.page=e;let t=this.shadow.getElementById(`eazy-results`);t&&(t.innerHTML=this.skeletonHTML()),await this.fetchCars(),this.renderResults(),this.shadow.host&&this.shadow.host.scrollIntoView({behavior:`smooth`,block:`start`})}hasActiveFilters(){return!!(this.filters.search||this.filters.merk||this.filters.brandstof||this.filters.prijs_max)}renderResults(){let e=this.shadow.getElementById(`eazy-results`);if(!e)return;let t=this.s.show_price!==!1,n=this.s.show_km!==!1,r=this.s.show_fuel!==!1,i=this.pagination.total??this.cars.length;if(this.cars.length===0){e.innerHTML=`<div class="eazy-empty">Geen auto's gevonden${this.hasActiveFilters()?` met deze filters.`:`.`}</div>`;return}e.innerHTML=`
                <div class="eazy-resultbar">${i} ${i===1?`auto`:`auto's`} gevonden</div>
                <div class="eazy-grid">
                    ${this.cars.map(e=>this.renderCard(e,{showPrice:t,showKm:n,showFuel:r})).join(``)}
                </div>
                ${this.paginationHTML()}
            `,e.querySelectorAll(`.eazy-card`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.getAttribute(`data-car-id`);t&&this.openDetail(parseInt(t,10))})});let a=this.shadow.getElementById(`eazy-prev`),o=this.shadow.getElementById(`eazy-next`);a&&a.addEventListener(`click`,()=>this.goToPage((this.pagination.current_page||1)-1)),o&&o.addEventListener(`click`,()=>this.goToPage((this.pagination.current_page||1)+1)),this.cars.forEach(e=>this.trackView(e.id))}paginationHTML(){let e=this.pagination.current_page||1,t=this.pagination.last_page||1;return t<=1?``:`
                <div class="eazy-pagination">
                    <button class="eazy-page-btn" id="eazy-prev" ${e<=1?`disabled`:``}>Vorige</button>
                    <span class="eazy-page-info">Pagina ${e} van ${t}</span>
                    <button class="eazy-page-btn" id="eazy-next" ${e>=t?`disabled`:``}>Volgende</button>
                </div>
            `}skeletonHTML(){return`<div class="eazy-grid">${`<div class="eazy-skel"><div class="eazy-skel-img"></div><div class="eazy-skel-line" style="width:70%"></div><div class="eazy-skel-line" style="width:40%"></div><div class="eazy-skel-line" style="width:55%"></div></div>`.repeat(6)}</div>`}renderCard(e,t){let n=this.opt(`image_position`,`top`),r=this.opt(`card_layout`,`classic`),i=e.image?`<img class="eazy-card-img" src="${this.escapeHtml(e.image)}" alt="${this.escapeHtml(e.title)}" loading="lazy">`:`<div class="eazy-no-img">Geen afbeelding</div>`,a=[];e.bouwjaar&&a.push({type:`year`,value:e.bouwjaar}),t.showFuel&&e.brandstof&&a.push({type:`fuel`,value:e.brandstof}),t.showKm&&e.km_stand&&a.push({type:`km`,value:e.km_stand.toLocaleString(`nl-NL`)+` km`}),e.kleur&&a.push({type:`color`,value:e.kleur});let s=`
                <div class="eazy-card-body">
                    <div class="eazy-card-title">${this.escapeHtml(e.title)}</div>
                    ${t.showPrice?`<div class="eazy-card-price">${this.escapeHtml(this.formatPrice(e.prijs))}</div>`:``}
                    ${a.length>0?`
                        <div class="eazy-card-specs">
                            ${a.map(e=>`<span class="eazy-spec">${o[e.type]||``}${this.escapeHtml(String(e.value))}</span>`).join(``)}
                        </div>
                    `:``}
                </div>
            `,c=r===`classic`&&n===`bottom`?s+i:i+s;return`
                <div class="eazy-card" data-layout="${r}" data-car-id="${e.id}">
                    ${c}
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