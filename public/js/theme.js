// Apply before paint, including Bootstrap controls. Storage may be unavailable in private contexts.
(() => {
    const preference = window.matchMedia('(prefers-color-scheme: dark)');
    let saved;
    try {
        saved = localStorage.getItem('app-theme');
    } catch (_) {}
    function apply(theme) {
        const dark = theme === 'dark';
        document.documentElement.dataset.bsTheme = theme;
        document.documentElement.style.colorScheme = theme;
        document.body?.classList.toggle('dark-mode', dark);
        const button = document.getElementById('theme-toggle');
        if (button) {
            button.setAttribute(
                'aria-label',
                `Switch to ${dark ? 'light' : 'dark'} mode`,
            );
            button.setAttribute('aria-pressed', String(dark));
            button.querySelector('span').textContent = dark
                ? 'Light mode'
                : 'Dark mode';
            button.querySelector('i').className =
                `fas fa-${dark ? 'sun' : 'moon'}`;
        }
    }
    apply(
        saved === 'dark' || saved === 'light'
            ? saved
            : preference.matches
              ? 'dark'
              : 'light',
    );
    document.addEventListener('DOMContentLoaded', () => {
        apply(document.documentElement.dataset.bsTheme);
        document
            .getElementById('theme-toggle')
            ?.addEventListener('click', () => {
                saved =
                    document.documentElement.dataset.bsTheme === 'dark'
                        ? 'light'
                        : 'dark';
                try {
                    localStorage.setItem('app-theme', saved);
                } catch (_) {}
                apply(saved);
            });
    });
    preference.addEventListener('change', (event) => {
        if (!saved) apply(event.matches ? 'dark' : 'light');
    });
})();
