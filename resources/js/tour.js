import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

/**
 * First-login guided tour: highlights real elements and walks the user
 * through the key features with a branded popover. Exposed as
 * window.startEazyTour(config). config: { completeUrl, csrf }
 */

function popoverHtml(icon, title, text) {
    return (
        `<div class="et-ico"><i class="fa-solid ${icon}" aria-hidden="true"></i></div>` +
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
        step('fa-hand', 'Welkom bij EazyAutomotive', 'We lopen samen langs de belangrijkste functies. Dit duurt nog geen minuut.', { center: true }),
        step('fa-gauge-high', 'Je dashboard', 'Hier zie je in één oogopslag je voorraad en hoeveel mensen je auto’s bekijken.', { element: '[data-tour="stats"]', side: 'bottom', align: 'start' }),
        step('fa-car', 'Auto’s beheren', 'Voeg auto’s toe met alleen een kenteken. De RDW vult merk, model, bouwjaar en APK automatisch in.', { element: '[data-tour="nav-cars"]', side: 'right', align: 'start' }),
        step('fa-palette', 'Ontwerp je widget', 'Pas kleuren, kolommen en lettertype aan zodat je voorraad bij je huisstijl past.', { element: '[data-tour="nav-design"]', side: 'right', align: 'start' }),
        step('fa-code', 'Op je eigen website', 'Kopieer één regel code en je voorraad staat live op je site, automatisch bijgewerkt bij elke wijziging.', { element: '[data-tour="nav-embed"]', side: 'right', align: 'start' }),
        step('fa-rss', 'Publiceren naar portals', 'Exporteer je voorraad als feed naar platforms zoals Marktplaats en AutoTrack. Eén bron, overal actueel.', { element: '[data-tour="nav-publish"]', side: 'right', align: 'start' }),
        step('fa-circle-plus', 'Begin hier', 'Klaar voor de start? Voeg nu je eerste auto toe en je staat zo live.', { element: '[data-tour="quick-add"]', side: 'bottom', align: 'start' }),
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
