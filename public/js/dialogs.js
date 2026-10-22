// Keep keyboard focus in the active dialog and return it to the invoking control.
(() => {
    const dialogs = [...document.querySelectorAll('.modal')];
    const returnFocus = new WeakMap();
    let previous = null;
    const focusable = (dialog) =>
        [
            ...dialog.querySelectorAll(
                'button, a[href], input, select, textarea, [tabindex], [contenteditable="true"]',
            ),
        ].filter(
            (el) =>
                !el.disabled &&
                (el.tabIndex >= 0 || el.isContentEditable) &&
                el.getClientRects().length,
        );
    const current = () =>
        dialogs.filter((dialog) => dialog.classList.contains('show')).at(-1);
    dialogs.forEach((dialog) => {
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.tabIndex = -1;
        const heading = dialog.querySelector('h2,h3,h4');
        if (heading) {
            heading.id ||= `${dialog.id}-title`;
            dialog.setAttribute('aria-labelledby', heading.id);
        }
        dialog
            .querySelectorAll('.modal-close')
            .forEach((button) =>
                button.setAttribute('aria-label', 'Close dialog'),
            );
        new MutationObserver(() => {
            const active = current();
            if (active === previous) return;
            if (active) {
                returnFocus.set(active, document.activeElement);
                (focusable(active)[0] || active).focus();
            } else if (previous) {
                const trigger = returnFocus.get(previous);
                if (trigger?.isConnected && trigger.getClientRects().length)
                    trigger.focus();
                else document.getElementById('workspace-title')?.focus();
            }
            previous = active;
        }).observe(dialog, { attributes: true, attributeFilter: ['class'] });
    });
    document.addEventListener('keydown', (event) => {
        const dialog = current();
        if (!dialog || event.key !== 'Tab') return;
        const elements = focusable(dialog);
        const first = elements[0],
            last = elements.at(-1);
        if (!first) {
            event.preventDefault();
            dialog.focus();
            return;
        }
        if (
            event.shiftKey &&
            (document.activeElement === first ||
                !dialog.contains(document.activeElement))
        ) {
            event.preventDefault();
            last.focus();
        } else if (
            !event.shiftKey &&
            (document.activeElement === last ||
                !dialog.contains(document.activeElement))
        ) {
            event.preventDefault();
            first.focus();
        }
    });
})();
