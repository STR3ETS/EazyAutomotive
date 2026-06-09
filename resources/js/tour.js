import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

/**
 * First-login guided tour: highlights real elements and walks the user
 * through the key features with a branded popover. Exposed as
 * window.startEazyTour(config). config: { completeUrl, csrf }
 *
 * Icons are inlined as SVG (FontAwesome Pro sources) rather than <i class="fa-*">
 * so the tour never depends on the icon webfont loading/caching in the browser.
 */

const ICONS = {
    hand: { vb: '0 0 512 512', d: 'M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-176c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 272c0 1.5 0 3.1 .1 4.6L67.6 283c-16-15.2-41.3-14.6-56.6 1.4S-3.6 325.7 12.4 341L124.8 448c43.1 41.1 100.4 64 160 64l19.2 0c97.2 0 176-78.8 176-176l0-208c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-176c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 176c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208z' },
    'gauge-high': { vb: '0 0 512 512', d: 'M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM288 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM256 416c35.3 0 64-28.7 64-64 0-16.2-6-31.1-16-42.3l69.5-138.9c5.9-11.9 1.1-26.3-10.7-32.2s-26.3-1.1-32.2 10.7L261.1 288.2c-1.7-.1-3.4-.2-5.1-.2-35.3 0-64 28.7-64 64s28.7 64 64 64zM176 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM96 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm352-32a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z' },
    car: { vb: '0 0 512 512', d: 'M135.2 117.4l-26.1 74.6 293.8 0-26.1-74.6C372.3 104.6 360.2 96 346.6 96L165.4 96c-13.6 0-25.7 8.6-30.2 21.4zM39.6 196.8L74.8 96.3C88.3 57.8 124.6 32 165.4 32l181.2 0c40.8 0 77.1 25.8 90.6 64.3l35.2 100.5c23.2 9.6 39.6 32.5 39.6 59.2l0 192c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32l0-32-320 0 0 32c0 17.7-14.3 32-32 32l-32 0c-17.7 0-32-14.3-32-32L0 256c0-26.7 16.4-49.6 39.6-59.2zM128 304a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64z' },
    palette: { vb: '0 0 512 512', d: 'M512 256c0 .9 0 1.8 0 2.7-.4 36.5-33.6 61.3-70.1 61.3L344 320c-26.5 0-48 21.5-48 48 0 3.4 .4 6.7 1 9.9 2.1 10.2 6.5 20 10.8 29.9 6.1 13.8 12.1 27.5 12.1 42 0 31.8-21.6 60.7-53.4 62-3.5 .1-7 .2-10.6 .2-141.4 0-256-114.6-256-256S114.6 0 256 0 512 114.6 512 256zM128 288a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm0-96a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM288 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm96 96a32 32 0 1 0 0-64 32 32 0 1 0 0 64z' },
    code: { vb: '0 0 576 512', d: 'M360.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm64.6 136.1c-12.5 12.5-12.5 32.8 0 45.3l73.4 73.4-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l96-96c12.5-12.5 12.5-32.8 0-45.3l-96-96c-12.5-12.5-32.8-12.5-45.3 0zm-274.7 0c-12.5-12.5-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3l96 96c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 150.6 182.6c12.5-12.5 12.5-32.8 0-45.3z' },
    rss: { vb: '0 0 448 512', d: 'M0 64c0-17.7 14.3-32 32-32 229.8 0 416 186.2 416 416 0 17.7-14.3 32-32 32s-32-14.3-32-32C384 253.6 226.4 96 32 96 14.3 96 0 81.7 0 64zM0 416a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM32 160c159.1 0 288 128.9 288 288 0 17.7-14.3 32-32 32s-32-14.3-32-32c0-123.7-100.3-224-224-224-17.7 0-32-14.3-32-32s14.3-32 32-32z' },
    'circle-plus': { vb: '0 0 512 512', d: 'M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z' },
};

function iconSvg(key) {
    const ic = ICONS[key] || ICONS.hand;
    return `<svg viewBox="${ic.vb}" width="22" height="22" fill="currentColor" aria-hidden="true" style="display:block"><path d="${ic.d}"/></svg>`;
}

function popoverHtml(icon, title, text) {
    return (
        `<div class="et-ico">${iconSvg(icon)}</div>` +
        `<h3 class="et-title">${title}</h3>` +
        `<p class="et-text">${text}</p>`
    );
}

function step(icon, title, text, opts = {}) {
    const popover = {
        title, // hidden visually (CSS), but gives the dialog its accessible name
        description: popoverHtml(icon, title, text),
        popoverClass: 'eazy-tour' + (opts.center ? ' eazy-tour--center' : ''),
    };
    if (opts.side) popover.side = opts.side;
    if (opts.align) popover.align = opts.align;

    const result = { popover };
    if (opts.element) result.element = opts.element;
    return result;
}

window.startEazyTour = function (config = {}) {
    const isMobile = window.innerWidth < 1024;
    const prefersReducedMotion =
        typeof window.matchMedia === 'function' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (isMobile) {
        window.dispatchEvent(new Event('eazy-open-sidebar'));
    }

    const candidateSteps = [
        step('hand', 'Welkom bij EazyAutomotive', 'We lopen samen langs de belangrijkste functies. Dit duurt nog geen minuut.', { center: true }),
        step('gauge-high', 'Je dashboard', 'Hier zie je in één oogopslag je voorraad en hoeveel mensen je auto’s bekijken.', { element: '[data-tour="stats"]', side: 'bottom', align: 'start' }),
        step('car', 'Auto’s beheren', 'Voeg auto’s toe met alleen een kenteken. De RDW vult merk, model en bouwjaar automatisch in.', { element: '[data-tour="nav-cars"]', side: 'right', align: 'start' }),
        step('palette', 'Ontwerp je widget', 'Pas kleuren, kolommen en lettertype aan zodat je voorraad bij je huisstijl past.', { element: '[data-tour="nav-design"]', side: 'right', align: 'start' }),
        step('code', 'Op je eigen website', 'Kopieer één regel code en je voorraad staat live op je site, automatisch bijgewerkt bij elke wijziging.', { element: '[data-tour="nav-embed"]', side: 'right', align: 'start' }),
        step('rss', 'Publiceren naar portals', 'Exporteer je voorraad als feed naar platforms zoals Marktplaats en AutoTrack. Eén bron, overal actueel.', { element: '[data-tour="nav-publish"]', side: 'right', align: 'start' }),
        step('circle-plus', 'Begin hier', 'Klaar voor de start? Voeg nu je eerste auto toe en je staat zo live.', { element: '[data-tour="quick-add"]', side: 'bottom', align: 'start' }),
    ];

    // Keep only steps whose target exists (robust across pages / screen sizes).
    const steps = candidateSteps.filter((s) => !s.element || document.querySelector(s.element));

    const markComplete = () => {
        if (!config.completeUrl) {
            return;
        }
        fetch(config.completeUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': config.csrf || '', Accept: 'application/json' },
            credentials: 'same-origin',
        }).catch(() => {});
    };

    const tour = driver({
        showProgress: true,
        progressText: 'Stap {{current}} van {{total}}',
        nextBtnText: 'Volgende',
        prevBtnText: 'Vorige',
        doneBtnText: 'Aan de slag',
        overlayColor: '#10302F',
        overlayOpacity: 0.72,
        stagePadding: 8,
        stageRadius: 14,
        popoverClass: 'eazy-tour',
        allowClose: true,
        disableActiveInteraction: true, // spotlighted links stay un-clickable so the user finishes via the buttons
        animate: !prefersReducedMotion,
        steps,
        onDestroyed: () => {
            if (isMobile) {
                window.dispatchEvent(new Event('eazy-close-sidebar'));
            }
            markComplete();
        },
    });

    tour.drive();
};
