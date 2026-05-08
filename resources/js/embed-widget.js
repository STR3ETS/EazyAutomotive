(function () {
    'use strict';

    const SCRIPT_TAG = document.currentScript;
    const API_KEY = SCRIPT_TAG?.getAttribute('data-api-key');
    const BASE_URL = (SCRIPT_TAG?.getAttribute('data-base-url') || '').replace(/\/$/, '');
    const CONTAINER_ID = SCRIPT_TAG?.getAttribute('data-container') || 'eazy-automotive-widget';

    if (!API_KEY) {
        console.error('[EazyAutomotive] data-api-key attribuut ontbreekt op het script tag.');
        return;
    }

    if (!BASE_URL) {
        console.error('[EazyAutomotive] data-base-url attribuut ontbreekt op het script tag.');
        return;
    }

    const SHADOW_MAP = {
        none: 'none',
        sm: '0 1px 3px rgba(0,0,0,0.08)',
        md: '0 4px 12px rgba(0,0,0,0.1)',
        lg: '0 10px 30px rgba(0,0,0,0.15)',
    };

    const CURRENCY_MAP = { EUR: '€', USD: '$', GBP: '£', none: '' };

    const SPEC_ICONS = {
        year: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        fuel: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2h0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>',
        km: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        color: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>',
    };

    const DETAIL_SPEC_ICONS = {
        'Merk': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>',
        'Model': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>',
        'Bouwjaar': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'Kilometerstand': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        'Brandstof': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2v0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>',
        'Kleur': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>',
        'Tweede kleur': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>',
        'Carrosserie': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M17 17m-2 0a2 2 0 104 0 2 2 0 10-4 0"/><path d="M5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2m-4 0H9"/></svg>',
        'Vermogen': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>',
        'Cilinderinhoud': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>',
        'Zitplaatsen': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
        'Deuren': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg>',
        'APK tot': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        '1e toelating': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'Kenteken': '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
    };

    const SYSTEM_FONT = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";

    const GOOGLE_FONTS = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins'];

    // Load Google Font in main document <head> — @import inside Shadow DOM doesn't work
    function loadGoogleFont(fontName) {
        if (!fontName || fontName === 'system' || !GOOGLE_FONTS.includes(fontName)) return;
        const id = 'eazy-gfont-' + fontName.replace(/\s/g, '-').toLowerCase();
        if (document.getElementById(id)) return; // already loaded
        const link = document.createElement('link');
        link.id = id;
        link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/\s/g, '+')}:wght@400;500;600;700&display=swap`;
        document.head.appendChild(link);
    }

    class EazyAutomotiveWidget {
        constructor() {
            this.cars = [];
            this.settings = {};
            this.companyInfo = {};
            this.init();
        }

        async init() {
            let container = document.getElementById(CONTAINER_ID);
            if (!container) {
                container = document.createElement('div');
                container.id = CONTAINER_ID;
                SCRIPT_TAG.parentNode.insertBefore(container, SCRIPT_TAG);
            }

            this.shadow = container.attachShadow({ mode: 'open' });
            this.shadow.innerHTML = this.loadingHTML();

            await this.fetchCars();
            this.render();
        }

        async fetchCars(page = 1) {
            try {
                const url = `${BASE_URL}/api/embed/v1/cars?api_key=${encodeURIComponent(API_KEY)}&page=${page}`;
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                this.cars = data.cars?.data || [];
                this.settings = data.settings || {};
                this.companyInfo = data.company || {};
                this.pagination = data.cars || {};
            } catch (error) {
                console.error('[EazyAutomotive]', error);
                this.shadow.innerHTML = this.errorHTML();
            }
        }

        async fetchCarDetail(carId) {
            try {
                const url = `${BASE_URL}/api/embed/v1/cars/${carId}?api_key=${encodeURIComponent(API_KEY)}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                return data.car;
            } catch (error) {
                console.error('[EazyAutomotive] Detail fetch error:', error);
                return null;
            }
        }

        // Settings helpers
        get s() { return this.settings; }
        opt(key, fallback) { return this.s[key] ?? fallback; }

        get primaryColor() { return this.opt('primary_color', '#4f46e5'); }
        get fontStack() {
            const f = this.opt('font_family', 'system');
            return f === 'system' ? SYSTEM_FONT : `'${f}', ${SYSTEM_FONT}`;
        }

        formatPrice(priceStr) {
            if (!priceStr) return '';
            const currency = this.opt('currency', 'EUR');
            const symbol = CURRENCY_MAP[currency] ?? '€';
            // priceStr comes formatted as "€ 24.950" from the API — strip original symbol
            const num = priceStr.replace(/^[€$£]\s*/, '');
            return symbol ? `${symbol} ${num}` : num;
        }

        loadFont() {
            loadGoogleFont(this.opt('font_family', 'system'));
        }

        // Detail page: use detail_* if detail_custom is on, otherwise card fallback
        get detailCustom() { return this.opt('detail_custom', false); }
        detailOpt(key, fallback) {
            if (this.detailCustom) return this.opt(key, fallback);
            return fallback;
        }
        getDetailVarsComment() { return ''; }

        getHoverCSS() {
            const effect = this.opt('hover_effect', 'lift');
            const pc = this.primaryColor;
            switch (effect) {
                case 'lift': return '.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }';
                case 'shadow': return '.eazy-card:hover { box-shadow: 0 12px 35px rgba(0,0,0,0.18); }';
                case 'scale': return '.eazy-card:hover { transform: scale(1.03); }';
                case 'glow': return `.eazy-card:hover { box-shadow: 0 0 20px ${pc}44; }`;
                case 'none': return '';
                default: return '.eazy-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.12); transform: translateY(-4px); }';
            }
        }

        getStyles() {
            const columns = this.opt('columns', 3);
            const cardBg = this.opt('card_bg_color', '#ffffff');
            const borderRadius = this.opt('card_border_radius', 12);
            const cardPadding = this.opt('card_padding', 16);
            const borderColor = this.opt('card_border_color', '#e5e7eb');
            const borderWidth = this.opt('card_border_width', 1);
            const cardShadow = SHADOW_MAP[this.opt('card_shadow', 'none')] || 'none';
            const imageHeight = this.opt('image_height', 200);
            const titleSize = this.opt('title_size', 16);
            const titleColor = this.opt('title_color', '#111827');
            const priceSize = this.opt('price_size', 20);
            const labelBg = this.opt('label_bg_color', '#f3f4f6');
            const labelColor = this.opt('label_text_color', '#4b5563');
            const labelRadius = this.opt('label_radius', 4);
            const labelPadX = this.opt('label_padding_x', 8);
            const labelPadY = this.opt('label_padding_y', 3);
            const labelGap = this.opt('label_gap', 6);
            const labelStyle = this.opt('label_style', 'badge');

            // Detail page overrides (fall back to card values when detail_custom is off)
            const dc = this.detailCustom;
            const dBg = dc ? this.opt('detail_bg_color', cardBg) : cardBg;
            const dBorderColor = dc ? this.opt('detail_border_color', borderColor) : borderColor;
            const dBorderWidth = dc ? this.opt('detail_border_width', borderWidth) : borderWidth;
            const dRadius = dc ? this.opt('detail_border_radius', borderRadius) : borderRadius;
            const dPadding = dc ? this.opt('detail_padding', cardPadding) : cardPadding;
            const dTitleSize = dc ? this.opt('detail_title_size', 24) : 24;
            const dTitleColor = dc ? this.opt('detail_title_color', titleColor) : titleColor;
            const dSubtitleColor = this.opt('detail_subtitle_color', '#9ca3af');
            const dPriceSize = dc ? this.opt('detail_price_size', 24) : 24;
            const dPriceColor = dc ? this.opt('detail_price_color', this.primaryColor) : this.primaryColor;
            const dDescColor = this.opt('detail_desc_color', '#6b7280');
            const dDescSize = this.opt('detail_desc_size', 14);
            const dGalleryH = dc ? this.opt('detail_gallery_height', 350) : 350;
            const dSpecCols = dc ? this.opt('detail_spec_columns', 2) : 2;
            const dSpecBg = this.opt('detail_spec_bg_color', '#f9fafb');
            const dSpecLabel = this.opt('detail_spec_label_color', '#6b7280');
            const dSpecValue = this.opt('detail_spec_value_color', dTitleColor);
            const dSpecRadius = this.opt('detail_spec_radius', 6);
            const dSpecGap = this.opt('detail_spec_gap', 6);
            const dBadgeStyle = this.opt('detail_badge_style', 'pill');
            const dBadgeBg = this.opt('detail_badge_bg_color', '#f3f4f6');
            const dBadgeText = this.opt('detail_badge_text_color', dPriceColor);
            const dBadgeRadius = this.opt('detail_badge_radius', 4);

            const MODAL_SHADOWS = {
                none: 'none',
                sm: '0 4px 12px rgba(0,0,0,0.1)',
                md: '0 10px 25px rgba(0,0,0,0.18)',
                lg: '0 25px 50px rgba(0,0,0,0.25)',
            };
            const dShadow = MODAL_SHADOWS[this.opt('detail_shadow', 'lg')] || MODAL_SHADOWS.lg;

            return `
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                :host { display: block; font-family: ${this.fontStack}; }
                .eazy-grid {
                    display: grid;
                    grid-template-columns: repeat(${columns}, 1fr);
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
                    border: ${borderWidth}px solid ${borderColor};
                    border-radius: ${borderRadius}px;
                    overflow: hidden;
                    background: ${cardBg};
                    box-shadow: ${cardShadow};
                    transition: box-shadow 0.2s ease, transform 0.2s ease;
                    cursor: pointer;
                }
                ${this.getHoverCSS()}
                .eazy-card-img {
                    width: 100%;
                    height: ${imageHeight}px;
                    object-fit: cover;
                    display: block;
                }
                .eazy-no-img {
                    width: 100%;
                    height: ${imageHeight}px;
                    background: #f3f4f6;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #9ca3af;
                    font-size: 0.875rem;
                }
                .eazy-card-body { padding: ${cardPadding}px; }
                .eazy-card-title {
                    font-size: ${titleSize}px;
                    font-weight: 600;
                    color: ${titleColor};
                    margin-bottom: 0.25rem;
                    line-height: 1.3;
                }
                .eazy-card-price {
                    font-size: ${priceSize}px;
                    font-weight: 700;
                    color: ${this.primaryColor};
                    margin-bottom: 0.75rem;
                }
                .eazy-card-specs {
                    display: flex;
                    flex-wrap: wrap;
                    gap: ${labelGap}px;
                }
                .eazy-spec {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    font-size: 0.75rem;
                    color: ${labelColor};
                    ${labelStyle === 'badge' ? `background: ${labelBg}; padding: ${labelPadY}px ${labelPadX}px; border-radius: ${labelRadius}px;` : ''}
                    ${labelStyle === 'outline' ? `background: transparent; border: 1px solid ${labelColor}; padding: ${labelPadY}px ${labelPadX}px; border-radius: ${labelRadius}px;` : ''}
                    ${labelStyle === 'icon-text' ? `background: transparent; padding: ${labelPadY}px 0;` : ''}
                    ${labelStyle === 'pill' ? `background: ${labelBg}; padding: ${labelPadY}px ${labelPadX}px; border-radius: 9999px;` : ''}
                }
                .eazy-spec svg {
                    flex-shrink: 0;
                    display: ${labelStyle === 'icon-text' ? 'inline-block' : 'none'};
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
                    background: rgba(0,0,0,${this.detailOpt('detail_overlay_opacity', 60) / 100});
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
                    background: ${dBg};
                    border-radius: ${dRadius}px;
                    border: ${dBorderWidth}px solid ${dBorderColor};
                    max-width: 800px;
                    width: 100%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    box-shadow: ${dShadow};
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
                    height: ${dGalleryH}px;
                    background: #f3f4f6;
                    overflow: hidden;
                }
                @media (max-width: 640px) {
                    .eazy-modal-gallery { height: ${Math.round(dGalleryH * 0.63)}px; }
                }
                .eazy-modal-gallery > img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    border-radius: ${Math.max(dRadius - 1, 0)}px ${Math.max(dRadius - 1, 0)}px 0 0;
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
                .eazy-modal-body { padding: ${dPadding}px; }
                .eazy-modal-title {
                    font-size: ${dTitleSize}px;
                    font-weight: 700;
                    color: ${dTitleColor};
                    margin-bottom: 0.25rem;
                }
                .eazy-modal-subtitle {
                    font-size: 0.8125rem;
                    color: ${dSubtitleColor};
                    margin-bottom: 0.75rem;
                }
                .eazy-modal-price {
                    font-size: ${dPriceSize}px;
                    font-weight: 800;
                    color: ${dPriceColor};
                    margin-bottom: 1rem;
                }
                .eazy-modal-section-title {
                    font-size: 0.6875rem;
                    font-weight: 600;
                    color: ${dSubtitleColor};
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-bottom: 0.5rem;
                }
                .eazy-modal-specs {
                    display: grid;
                    grid-template-columns: repeat(${dSpecCols}, 1fr);
                    gap: ${dSpecGap}px;
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
                    background: ${dSpecBg};
                    border-radius: ${dSpecRadius}px;
                    font-size: 0.8125rem;
                }
                .eazy-modal-spec-label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    color: ${dSpecLabel};
                }
                .eazy-modal-spec-label svg {
                    flex-shrink: 0;
                    color: ${dSpecLabel};
                }
                .eazy-modal-spec-value { color: ${dSpecValue}; font-weight: 600; }
                .eazy-modal-desc {
                    color: ${dDescColor};
                    font-size: ${dDescSize}px;
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
                    color: ${dBadgeText};
                    ${dBadgeStyle === 'pill' ? `background: ${dBadgeBg}; padding: 0.3rem 0.75rem; border-radius: 9999px;` : ''}
                    ${dBadgeStyle === 'badge' ? `background: ${dBadgeBg}; padding: 0.3rem 0.75rem; border-radius: ${dBadgeRadius}px;` : ''}
                    ${dBadgeStyle === 'outline' ? `background: transparent; border: 1px solid ${dBadgeText}; padding: 0.3rem 0.75rem; border-radius: ${dBadgeRadius}px;` : ''}
                }
                .eazy-modal-loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 4rem;
                    color: ${labelColor};
                    font-size: 0.9375rem;
                }
            `;
        }

        render() {
            const showPrice = this.s.show_price !== false;
            const showKm = this.s.show_km !== false;
            const showFuel = this.s.show_fuel !== false;

            // Load Google Font in main document head (Shadow DOM @import doesn't work)
            this.loadFont();

            if (this.cars.length === 0) {
                this.shadow.innerHTML = this.emptyHTML();
                return;
            }

            this.shadow.innerHTML = `
                <style>${this.getStyles()}</style>
                <div class="eazy-grid">
                    ${this.cars.map(car => this.renderCard(car, { showPrice, showKm, showFuel })).join('')}
                </div>
                <div class="eazy-powered">
                    Powered by <a href="https://eazyonline.nl" target="_blank" rel="noopener">Eazyonline</a>
                </div>
            `;

            // Bind card click events
            this.shadow.querySelectorAll('.eazy-card').forEach(card => {
                card.addEventListener('click', () => {
                    const carId = card.getAttribute('data-car-id');
                    if (carId) this.openDetail(parseInt(carId, 10));
                });
            });

            // Track views (fire and forget)
            this.cars.forEach(car => this.trackView(car.id));
        }

        renderCard(car, opts) {
            const imagePosition = this.opt('image_position', 'top');

            const imgHtml = car.image
                ? `<img class="eazy-card-img" src="${this.escapeHtml(car.image)}" alt="${this.escapeHtml(car.title)}" loading="lazy">`
                : `<div class="eazy-no-img">Geen afbeelding</div>`;

            const specs = [];
            if (car.bouwjaar) specs.push({ type: 'year', value: car.bouwjaar });
            if (opts.showFuel && car.brandstof) specs.push({ type: 'fuel', value: car.brandstof });
            if (opts.showKm && car.km_stand) specs.push({ type: 'km', value: car.km_stand.toLocaleString('nl-NL') + ' km' });
            if (car.kleur) specs.push({ type: 'color', value: car.kleur });

            const bodyHtml = `
                <div class="eazy-card-body">
                    <div class="eazy-card-title">${this.escapeHtml(car.title)}</div>
                    ${opts.showPrice ? `<div class="eazy-card-price">${this.escapeHtml(this.formatPrice(car.prijs))}</div>` : ''}
                    ${specs.length > 0 ? `
                        <div class="eazy-card-specs">
                            ${specs.map(s => `<span class="eazy-spec">${SPEC_ICONS[s.type] || ''}${this.escapeHtml(String(s.value))}</span>`).join('')}
                        </div>
                    ` : ''}
                </div>
            `;

            return `
                <div class="eazy-card" data-car-id="${car.id}">
                    ${imagePosition === 'top' ? imgHtml + bodyHtml : bodyHtml + imgHtml}
                </div>
            `;
        }

        async openDetail(carId) {
            const overlay = document.createElement('div');
            overlay.className = 'eazy-overlay';
            overlay.innerHTML = `
                <div class="eazy-modal">
                    <button class="eazy-modal-close" aria-label="Sluiten">&times;</button>
                    <div class="eazy-modal-loading">Auto laden...</div>
                </div>
            `;
            this.shadow.appendChild(overlay);
            this.bindModalClose(overlay);

            const car = await this.fetchCarDetail(carId);
            if (!car) {
                overlay.remove();
                return;
            }

            this.renderDetail(overlay, car);
        }

        renderDetail(overlay, car) {
            const images = car.images || [];
            const mainImage = images[0] || null;

            // Visibility settings (default to true)
            const showSubtitle = this.opt('detail_show_subtitle', true) !== false && this.opt('detail_show_subtitle', true) !== 0;
            const showSpecs = this.opt('detail_show_specs', true) !== false && this.opt('detail_show_specs', true) !== 0;
            const showDesc = this.opt('detail_show_description', true) !== false && this.opt('detail_show_description', true) !== 0;
            const showOpts = this.opt('detail_show_options', true) !== false && this.opt('detail_show_options', true) !== 0;

            // Build subtitle parts (year · fuel · km)
            const subtitleParts = [];
            if (car.bouwjaar) subtitleParts.push(car.bouwjaar);
            if (car.brandstof) subtitleParts.push(car.brandstof);
            if (car.km_stand) subtitleParts.push(car.km_stand.toLocaleString('nl-NL') + ' km');

            const specs = [];
            if (car.bouwjaar) specs.push(['Bouwjaar', car.bouwjaar]);
            if (car.km_stand) specs.push(['Kilometerstand', car.km_stand.toLocaleString('nl-NL') + ' km']);
            if (car.brandstof) specs.push(['Brandstof', car.brandstof]);
            if (car.kleur) specs.push(['Kleur', car.kleur]);
            if (car.tweede_kleur) specs.push(['Tweede kleur', car.tweede_kleur]);
            if (car.inrichting) specs.push(['Carrosserie', car.inrichting]);
            if (car.vermogen) specs.push(['Vermogen', car.vermogen + ' kW']);
            if (car.cilinderinhoud) specs.push(['Cilinderinhoud', car.cilinderinhoud + ' cc']);
            if (car.aantal_zitplaatsen) specs.push(['Zitplaatsen', car.aantal_zitplaatsen]);
            if (car.aantal_deuren) specs.push(['Deuren', car.aantal_deuren]);
            if (car.apk_tot) specs.push(['APK tot', car.apk_tot]);
            if (car.datum_eerste_toelating) specs.push(['1e toelating', car.datum_eerste_toelating]);
            if (car.kenteken) specs.push(['Kenteken', car.kenteken]);

            const defaultIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>';

            const modal = overlay.querySelector('.eazy-modal');
            modal.innerHTML = `
                <button class="eazy-modal-close" aria-label="Sluiten">&times;</button>

                ${mainImage ? `
                    <div class="eazy-modal-gallery">
                        <img src="${this.escapeHtml(mainImage)}" alt="${this.escapeHtml(car.title)}" id="eazy-detail-main-img">
                        ${images.length > 1 ? `
                            <div class="eazy-modal-thumbs">
                                ${images.map((img, i) => `
                                    <img class="eazy-modal-thumb${i === 0 ? ' active' : ''}" src="${this.escapeHtml(img)}" data-idx="${i}" alt="">
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                ` : ''}

                <div class="eazy-modal-body">
                    <div class="eazy-modal-title">${this.escapeHtml(car.title)}</div>
                    ${showSubtitle && subtitleParts.length > 0 ? `
                        <div class="eazy-modal-subtitle">${subtitleParts.map(p => this.escapeHtml(String(p))).join(' &middot; ')}</div>
                    ` : ''}
                    <div class="eazy-modal-price">${this.escapeHtml(this.formatPrice(car.prijs))}</div>

                    ${showSpecs && specs.length > 0 ? `
                        <div class="eazy-modal-section-title">Specificaties</div>
                        <div class="eazy-modal-specs">
                            ${specs.map(([label, val]) => `
                                <div class="eazy-modal-spec">
                                    <span class="eazy-modal-spec-label">${DETAIL_SPEC_ICONS[label] || defaultIcon}${this.escapeHtml(label)}</span>
                                    <span class="eazy-modal-spec-value">${this.escapeHtml(String(val))}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}

                    ${showDesc && car.beschrijving ? `
                        <div class="eazy-modal-section-title">Beschrijving</div>
                        <div class="eazy-modal-desc">${this.escapeHtml(car.beschrijving)}</div>
                    ` : ''}

                    ${showOpts && car.extra_opties && car.extra_opties.length > 0 ? `
                        <div class="eazy-modal-section-title">Opties</div>
                        <div class="eazy-modal-opties">
                            ${car.extra_opties.map(o => `<span class="eazy-modal-optie">${this.escapeHtml(o)}</span>`).join('')}
                        </div>
                    ` : ''}
                </div>
            `;

            this.bindModalClose(overlay);

            const thumbs = overlay.querySelectorAll('.eazy-modal-thumb');
            const mainImg = overlay.querySelector('#eazy-detail-main-img');
            if (thumbs.length > 0 && mainImg) {
                thumbs.forEach(thumb => {
                    thumb.addEventListener('click', (e) => {
                        e.stopPropagation();
                        mainImg.src = thumb.src;
                        thumbs.forEach(t => t.classList.remove('active'));
                        thumb.classList.add('active');
                    });
                });
            }
        }

        bindModalClose(overlay) {
            const closeBtn = overlay.querySelector('.eazy-modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    overlay.remove();
                });
            }

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.remove();
            });

            const keyHandler = (e) => {
                if (e.key === 'Escape') {
                    overlay.remove();
                    document.removeEventListener('keydown', keyHandler);
                }
            };
            document.addEventListener('keydown', keyHandler);
        }

        async trackView(carId) {
            try {
                await fetch(`${BASE_URL}/api/embed/v1/cars/${carId}/view`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Api-Key': API_KEY,
                    },
                });
            } catch (e) {
                // Silent fail for tracking
            }
        }

        escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        loadingHTML() {
            return `<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Aanbod laden...</div>`;
        }

        errorHTML() {
            return `<div style="text-align:center;padding:2rem;color:#ef4444;font-family:sans-serif;">Kon het aanbod niet laden. Probeer het later opnieuw.</div>`;
        }

        emptyHTML() {
            return `<div style="text-align:center;padding:2rem;color:#9ca3af;font-family:sans-serif;">Momenteel geen auto's beschikbaar.</div>`;
        }
    }

    new EazyAutomotiveWidget();
})();
