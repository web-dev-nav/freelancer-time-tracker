/**
 * Rich Text Editor Module (Quill)
 *
 * Uses Quill (free/open-source) for work description fields while keeping
 * hidden textareas synchronized for backward-compatible form behavior.
 */

const editors = new Map();

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

    quill.on('text-change', () => {
        syncTextarea(textareaId);
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

