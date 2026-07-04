

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const themeCookieName = 'pitstop_theme';
const themeMaxAge = 60 * 60 * 24 * 365;

const setCookie = (name, value) => {
    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${themeMaxAge}; SameSite=Lax`;
};

const applyTheme = (theme) => {
    const nextThemeLabel = theme === 'dark' ? 'terang' : 'gelap';

    document.documentElement.classList.remove('pitstop-theme-light', 'pitstop-theme-dark');
    document.documentElement.classList.add(`pitstop-theme-${theme}`);
    setCookie(themeCookieName, theme);

    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.setAttribute('aria-label', `Ganti ke tema ${nextThemeLabel}`);
        button.setAttribute('title', `Ganti ke tema ${nextThemeLabel}`);
    });

    document.querySelectorAll('[data-theme-logo]').forEach((logo) => {
        const source = theme === 'dark' ? logo.dataset.darkSrc : logo.dataset.lightSrc;

        if (source) {
            logo.setAttribute('src', source);
        }
    });

    document.querySelectorAll('[data-theme-favicon]').forEach((favicon) => {
        const source = theme === 'dark' ? favicon.dataset.darkHref : favicon.dataset.lightHref;

        if (source) {
            favicon.setAttribute('href', source);
        }
    });
};

const currentTheme = () => (
    document.documentElement.classList.contains('pitstop-theme-dark') ? 'dark' : 'light'
);

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-switch]');

    if (!button) {
        return;
    }

    applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
});
