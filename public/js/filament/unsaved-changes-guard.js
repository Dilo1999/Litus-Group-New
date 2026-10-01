/**
 * Warns before leaving the Blog post create/edit page with unsaved form changes.
 *
 * - Refresh, closing the tab or typing a new URL: the browser's own "Leave site?" dialog.
 * - Clicking a link inside the admin (sidebar, breadcrumbs, Cancel…): a dialog offering
 *   Save changes / Leave without saving / Stay on page.
 *
 * Changes are detected by comparing the Livewire `data` (including deferred wire:model
 * values that have not been sent yet) with a snapshot taken when the form was loaded or
 * last saved.
 */
(function () {
    const FORM_SELECTOR = 'form[wire\\:submit\\.prevent="save"], form[wire\\:submit\\.prevent="create"]';
    // Only forms with the blog post HTML preview.
    const GUARDED_FORM_MARKER = '[data-blog-html-preview]';
    const SAVE_METHODS = ['save', 'create', 'createAnother'];
    const BASELINE_DELAY_MS = 1500;

    let baseline = null;
    let baselineComponentId = null;
    let leaving = false;
    let pendingSaveUrl = null;
    let pendingSaveTimer = null;
    let dialog = null;

    function findForm() {
        const form = document.querySelector(FORM_SELECTOR);

        return form && form.querySelector(GUARDED_FORM_MARKER) ? form : null;
    }

    function findComponent() {
        const form = findForm();
        const el = form && form.closest('[wire\\:id]');

        if (! el || ! window.Livewire || typeof window.Livewire.find !== 'function') {
            return null;
        }

        try {
            const wire = window.Livewire.find(el.getAttribute('wire:id'));
            return wire ? wire.__instance || null : null;
        } catch (error) {
            return null;
        }
    }

    function setPath(target, path, value) {
        const segments = String(path).split('.');
        let node = target;

        for (let i = 0; i < segments.length - 1; i++) {
            if (node[segments[i]] === null || typeof node[segments[i]] !== 'object') {
                node[segments[i]] = {};
            }
            node = node[segments[i]];
        }

        node[segments[segments.length - 1]] = value;
    }

    function snapshot(component) {
        let data;

        try {
            data = JSON.parse(JSON.stringify(component.data || {}));
        } catch (error) {
            return null;
        }

        Object.values(component.deferredActions || {}).forEach((action) => {
            if (action && action.payload && typeof action.payload.name === 'string') {
                setPath(data, action.payload.name, action.payload.value);
            }
        });

        try {
            return JSON.stringify(data.data === undefined ? null : data.data);
        } catch (error) {
            return null;
        }
    }

    function captureBaseline() {
        const component = findComponent();

        if (! component) {
            return;
        }

        const value = snapshot(component);

        if (value !== null) {
            baseline = value;
            baselineComponentId = component.id;
        }
    }

    function ensureBaseline() {
        const component = findComponent();

        if (component && baselineComponentId !== component.id) {
            baseline = null;
        }

        if (baseline === null) {
            captureBaseline();
        }
    }

    function isDirty() {
        if (leaving) {
            return false;
        }

        const component = findComponent();

        if (! component || baseline === null || baselineComponentId !== component.id) {
            return false;
        }

        const current = snapshot(component);

        return current !== null && current !== baseline;
    }

    function messageCallsSave(message) {
        const queue = (message && message.updateQueue) || [];

        return queue.some((action) => {
            if (! action || action.type !== 'callMethod') {
                return false;
            }

            const method = action.method || (action.payload && action.payload.method);

            return SAVE_METHODS.indexOf(method) !== -1;
        });
    }

    function responseHasErrors(message) {
        const memo = message && message.response && message.response.serverMemo;
        const errors = memo && memo.errors;

        return !! errors && Object.keys(errors).length > 0;
    }

    function isGuardedComponent(component) {
        const current = findComponent();

        return !! current && !! component && current.id === component.id;
    }

    function shouldGuardLink(anchor, event) {
        if (! anchor || event.defaultPrevented || event.button !== 0) {
            return false;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return false;
        }

        if (anchor.hasAttribute('download')) {
            return false;
        }

        const target = (anchor.getAttribute('target') || '').toLowerCase();
        if (target && target !== '_self') {
            return false;
        }

        const href = anchor.getAttribute('href') || '';
        if (! href || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) {
            return false;
        }

        let url;
        try {
            url = new URL(href, window.location.href);
        } catch (error) {
            return false;
        }

        // Same page, different hash only: nothing is lost.
        if (url.origin === window.location.origin
            && url.pathname === window.location.pathname
            && url.search === window.location.search
            && url.hash) {
            return false;
        }

        return true;
    }

    function leaveTo(url) {
        leaving = true;
        window.location.href = url;
    }

    function saveThenLeave(url) {
        const form = findForm();

        if (! form) {
            leaveTo(url);
            return;
        }

        pendingSaveUrl = url;
        clearTimeout(pendingSaveTimer);
        // If the browser blocks the submit (e.g. a required field), forget the pending redirect.
        pendingSaveTimer = setTimeout(() => {
            pendingSaveUrl = null;
        }, 1500);

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    }

    function buildDialog() {
        const style = document.createElement('style');
        style.textContent = [
            '.lb-unsaved{position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;padding:1rem;}',
            '.lb-unsaved.is-open{display:flex;}',
            '.lb-unsaved__backdrop{position:absolute;inset:0;background:rgba(3,7,18,.6);}',
            '.lb-unsaved__panel{position:relative;width:100%;max-width:28rem;border-radius:.75rem;background:#fff;color:#111827;box-shadow:0 25px 50px -12px rgba(0,0,0,.45);padding:1.5rem;font-family:inherit;}',
            '.dark .lb-unsaved__panel{background:#1f2937;color:#f9fafb;border:1px solid #374151;}',
            '.lb-unsaved__icon{display:flex;align-items:center;justify-content:center;width:2.75rem;height:2.75rem;border-radius:9999px;background:#e6edff;color:#1e3a9e;margin:0 auto .75rem;}',
            '.dark .lb-unsaved__icon{background:rgba(245,158,11,.15);color:#fbbf24;}',
            '.lb-unsaved__title{margin:0;text-align:center;font-size:1.125rem;font-weight:700;}',
            '.lb-unsaved__text{margin:.5rem 0 1.25rem;text-align:center;font-size:.875rem;line-height:1.5;color:#4b5563;}',
            '.dark .lb-unsaved__text{color:#d1d5db;}',
            '.lb-unsaved__actions{display:flex;flex-direction:column;gap:.5rem;}',
            '.lb-unsaved__btn{appearance:none;cursor:pointer;width:100%;border-radius:.5rem;padding:.6rem 1rem;font-size:.875rem;font-weight:600;border:1px solid transparent;}',
            '.lb-unsaved__btn--save{background:#142d87;color:#fff;}',
            '.lb-unsaved__btn--save:hover{background:#0e2675;}',
            '.lb-unsaved__btn--leave{background:#fff;color:#dc2626;border-color:#fca5a5;}',
            '.lb-unsaved__btn--leave:hover{background:#fef2f2;}',
            '.dark .lb-unsaved__btn--leave{background:transparent;color:#f87171;border-color:rgba(248,113,113,.5);}',
            '.dark .lb-unsaved__btn--leave:hover{background:rgba(220,38,38,.12);}',
            '.lb-unsaved__btn--stay{background:#fff;color:#374151;border-color:#d1d5db;}',
            '.lb-unsaved__btn--stay:hover{background:#f9fafb;}',
            '.dark .lb-unsaved__btn--stay{background:#374151;color:#f9fafb;border-color:#4b5563;}',
            '.dark .lb-unsaved__btn--stay:hover{background:#4b5563;}',
        ].join('');
        document.head.appendChild(style);

        const el = document.createElement('div');
        el.className = 'lb-unsaved';
        el.setAttribute('role', 'dialog');
        el.setAttribute('aria-modal', 'true');
        el.setAttribute('aria-labelledby', 'lb-unsaved-title');
        el.innerHTML =
            '<div class="lb-unsaved__backdrop" data-unsaved-stay></div>' +
            '<div class="lb-unsaved__panel">' +
            '<div class="lb-unsaved__icon" aria-hidden="true">' +
            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>' +
            '</div>' +
            '<h2 class="lb-unsaved__title" id="lb-unsaved-title">You have unsaved changes</h2>' +
            '<p class="lb-unsaved__text">Do you want to save your changes before leaving this page? If you leave without saving, they will be lost.</p>' +
            '<div class="lb-unsaved__actions">' +
            '<button type="button" class="lb-unsaved__btn lb-unsaved__btn--save" data-unsaved-save>Save changes</button>' +
            '<button type="button" class="lb-unsaved__btn lb-unsaved__btn--leave" data-unsaved-leave>Leave without saving</button>' +
            '<button type="button" class="lb-unsaved__btn lb-unsaved__btn--stay" data-unsaved-stay>Stay on this page</button>' +
            '</div>' +
            '</div>';

        el.addEventListener('click', (event) => {
            const url = el.dataset.url || '';

            if (event.target.closest('[data-unsaved-save]')) {
                closeDialog();
                saveThenLeave(url);
            } else if (event.target.closest('[data-unsaved-leave]')) {
                closeDialog();
                leaveTo(url);
            } else if (event.target.closest('[data-unsaved-stay]')) {
                closeDialog();
            }
        });

        document.body.appendChild(el);

        return el;
    }

    function openDialog(url) {
        if (! dialog) {
            dialog = buildDialog();
        }

        dialog.dataset.url = url;
        dialog.classList.add('is-open');

        const saveBtn = dialog.querySelector('[data-unsaved-save]');
        if (saveBtn) {
            saveBtn.focus();
        }
    }

    function closeDialog() {
        if (dialog) {
            dialog.classList.remove('is-open');
        }
    }

    function isDialogOpen() {
        return !! dialog && dialog.classList.contains('is-open');
    }

    document.addEventListener('click', (event) => {
        const anchor = event.target && event.target.closest ? event.target.closest('a[href]') : null;

        if (! shouldGuardLink(anchor, event) || ! isDirty()) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        openDialog(new URL(anchor.getAttribute('href'), window.location.href).href);
    }, true);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isDialogOpen()) {
            closeDialog();
        }
    });

    window.addEventListener('beforeunload', (event) => {
        if (! isDirty()) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';

        return '';
    });

    // Take the baseline before the user's first edit, after fields have finished initialising.
    ['pointerdown', 'keydown', 'focusin', 'paste', 'drop'].forEach((type) => {
        document.addEventListener(type, (event) => {
            const form = findForm();

            if (form && event.target && form.contains(event.target)) {
                ensureBaseline();
            }
        }, true);
    });

    function registerHooks() {
        if (! window.Livewire || typeof window.Livewire.hook !== 'function' || registerHooks.done) {
            return;
        }

        registerHooks.done = true;

        window.Livewire.hook('message.sent', (message, component) => {
            if (pendingSaveUrl && isGuardedComponent(component) && messageCallsSave(message)) {
                message.lbRedirectAfterSave = pendingSaveUrl;
                pendingSaveUrl = null;
                clearTimeout(pendingSaveTimer);
            }
        });

        window.Livewire.hook('message.processed', (message, component) => {
            const effects = (message.response && message.response.effects) || {};

            if (isGuardedComponent(component) && messageCallsSave(message) && ! responseHasErrors(message)) {
                if (message.lbRedirectAfterSave) {
                    effects.redirect = message.lbRedirectAfterSave;
                }

                captureBaseline();
            }

            // Server redirects (after save, create, delete…) are deliberate: don't block them.
            if (effects.redirect) {
                leaving = true;
            }
        });
    }

    function boot() {
        registerHooks();

        if (findForm()) {
            setTimeout(ensureBaseline, BASELINE_DELAY_MS);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    document.addEventListener('livewire:load', boot);
})();
