/**
 * Rich Text Editor Module (Quill)
 *
 * Uses Quill (free/open-source) for work description fields while keeping
 * hidden textareas synchronized for backward-compatible form behavior.
 */

const editors = new Map();
const changeCallbacks = new Map();
const lastSelections = new Map();

function normalizeHtml(html) {
    if (!html) return '';
    const trimmed = String(html).trim();
    if (!trimmed || trimmed === '<p><br></p>') {
        return '';
    }
    return trimmed;
}

function getEditor(textareaId) {
    return editors.get(textareaId) || null;
}

function syncTextarea(textareaId) {
    const entry = getEditor(textareaId);
    const textarea = document.getElementById(textareaId);

    if (!textarea || !entry) return;

    textarea.value = normalizeHtml(entry.quill.root.innerHTML);
}

export function initRichTextEditor(textareaId, options = {}) {
    const textarea = document.getElementById(textareaId);
    const editorContainer = document.getElementById(`${textareaId}-editor`);
    const toolbar = document.getElementById(`${textareaId}-toolbar`);

    if (!textarea || !editorContainer || !toolbar) {
        return null;
    }

    if (editors.has(textareaId)) {
        return editors.get(textareaId).quill;
    }

    if (!window.Quill) {
        return null;
    }

    textarea.style.display = 'none';

    const quill = new window.Quill(editorContainer, {
        theme: 'snow',
        modules: {
            toolbar: `#${toolbar.id}`,
        },
        placeholder: options.placeholder || textarea.getAttribute('placeholder') || '',
    });

    const initialValue = textarea.value ? String(textarea.value) : '';
    if (initialValue.trim() !== '') {
        quill.clipboard.dangerouslyPasteHTML(initialValue);
    }

    quill.on('text-change', (_delta, _oldDelta, source) => {
        syncTextarea(textareaId);

        const callbacks = changeCallbacks.get(textareaId) || [];
        callbacks.forEach((callback) => {
            try {
                callback({ source });
            } catch (_error) {
                // Ignore callback failures to avoid breaking editor input.
            }
        });
    });

    quill.on('selection-change', (range) => {
        if (range && typeof range.index === 'number' && typeof range.length === 'number') {
            lastSelections.set(textareaId, { index: range.index, length: range.length });
        }
    });

    editors.set(textareaId, { quill });
    syncTextarea(textareaId);

    return quill;
}

export function initWorkDescriptionEditors() {
    initRichTextEditor('edit-work-description', {
        placeholder: 'Describe what you accomplished during this work session...',
    });
    initRichTextEditor('work-description', {
        placeholder: 'Describe what you accomplished during this work session...',
    });
}

export function setEditorContent(textareaId, value) {
    const entry = getEditor(textareaId);
    const nextValue = value ? String(value) : '';

    if (!entry) {
        const textarea = document.getElementById(textareaId);
        if (textarea) textarea.value = nextValue;
        return;
    }

    if (!nextValue.trim()) {
        entry.quill.setText('');
        syncTextarea(textareaId);
        return;
    }

    entry.quill.clipboard.dangerouslyPasteHTML(nextValue);
    syncTextarea(textareaId);
}

export function clearEditorContent(textareaId) {
    setEditorContent(textareaId, '');
}

export function getEditorHtml(textareaId) {
    const entry = getEditor(textareaId);
    if (!entry) {
        const textarea = document.getElementById(textareaId);
        return textarea ? normalizeHtml(textarea.value) : '';
    }

    return normalizeHtml(entry.quill.root.innerHTML);
}

export function getEditorPlainText(textareaId) {
    const entry = getEditor(textareaId);
    if (!entry) {
        const textarea = document.getElementById(textareaId);
        return textarea ? String(textarea.value || '').trim() : '';
    }

    return String(entry.quill.getText() || '').trim();
}

export function getEditorSelection(textareaId) {
    const entry = getEditor(textareaId);
    if (!entry) return null;
    return entry.quill.getSelection() || lastSelections.get(textareaId) || null;
}

export function getSelectedText(textareaId) {
    const entry = getEditor(textareaId);
    if (!entry) return '';

    const range = getEditorSelection(textareaId);
    if (!range || !range.length) return '';

    return String(entry.quill.getText(range.index, range.length) || '');
}

export function replaceSelectedText(textareaId, text) {
    const entry = getEditor(textareaId);
    if (!entry) return false;

    const range = getEditorSelection(textareaId);
    if (!range || !range.length) return false;

    const replacement = String(text || '').trim();
    if (!replacement) return false;

    const escaped = replacement
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    const html = escaped.replace(/\n/g, '<br>');

    entry.quill.deleteText(range.index, range.length, 'user');
    entry.quill.clipboard.dangerouslyPasteHTML(range.index, html, 'user');
    entry.quill.setSelection(range.index + replacement.length, 0, 'silent');
    lastSelections.set(textareaId, { index: range.index, length: replacement.length });
    syncTextarea(textareaId);

    return true;
}

export function onEditorChange(textareaId, callback) {
    if (typeof callback !== 'function') return;

    const callbacks = changeCallbacks.get(textareaId) || [];
    callbacks.push(callback);
    changeCallbacks.set(textareaId, callbacks);
}
