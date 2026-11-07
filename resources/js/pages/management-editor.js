import { confirmAction } from '../components/feedback';

export function initManagementEditor() {
  const panel = document.querySelector('[data-content-editor]');
  if (!panel) return;
  const form = panel.querySelector('[data-editor-form]');
  const heading = panel.querySelector('[data-editor-heading]');
  const cancel = panel.querySelector('[data-editor-cancel]');
  const toggle = document.querySelector('[data-editor-panel-toggle]');
  const image = form.querySelector('input[type="file"]');
  const preview = panel.querySelector('[data-editor-preview]');
  const placeholder = panel.querySelector('[data-editor-no-image]');
  const statusHint = panel.querySelector('[data-editor-status-hint]');
  const preserveStatus = form.querySelector('[data-editor-preserve-status]');
  const method = form.elements.namedItem('_method');
  const idField = form.elements.namedItem('_editing_id');
  let editingId = panel.dataset.initialId;
  let currentImage = panel.dataset.editorCurrentImage || '';
  let localImage = null;
  let checking = false;
  let saving = false;
  let restoredErrors = panel.dataset.hasErrors === 'true';
  const label = panel.dataset.editorLabel;
  const snapshot = () => JSON.stringify([...new FormData(form)].map(([name, value]) => [name,
    value instanceof File ? (value.name ? [value.name, value.size, value.lastModified] : null) : value,
  ]));
  let baseline = snapshot();
  const dirty = () => restoredErrors || snapshot() !== baseline;
  const setVisible = (element, visible) => {
    if (!element) return;
    element.hidden = !visible;
    element.classList.toggle('hidden', !visible);
  };
  const panelOpen = open => {
    panel.classList.toggle('hidden', !open);
    panel.classList.toggle('order-1', open);
    panel.classList.toggle('order-2', !open);
    panel.nextElementSibling?.classList.toggle('order-2', open);
    panel.nextElementSibling?.classList.toggle('order-1', !open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.textContent = open ? 'Tutup' : editingId ? 'Edit' : 'Buat';
  };
  const releaseImage = () => { if (localImage) URL.revokeObjectURL(localImage); localImage = null; };
  const showImage = () => {
    const url = localImage || currentImage;
    if (url) preview.src = url; else preview.removeAttribute('src');
    setVisible(preview, Boolean(url)); setVisible(placeholder, !url);
  };
  const highlight = () => {
    document.querySelectorAll('[data-editor-item]').forEach(item => {
      const selected = item.dataset.editorItem === editingId;
      item.classList.toggle('bg-blue-50', selected);
      item.classList.toggle('bg-white', !selected);
      item.querySelectorAll('[data-editor-select]').forEach(link => {
        if (selected) link.setAttribute('aria-current', 'true'); else link.removeAttribute('aria-current');
      });
    });
  };
  const deleteForms = [...document.querySelectorAll('form[data-confirm-message]')].map(node => [node, node.dataset.confirmMessage]);
  const updateDeleteWarnings = () => deleteForms.forEach(([node, message]) => {
    node.dataset.confirmMessage = message + (dirty() ? ' Perubahan formulir yang belum disimpan juga akan dibuang.' : '');
  });
  const discard = async () => {
    if (!dirty()) return true;
    if (checking || saving) return false;
    checking = true;
    try {
      return await confirmAction({ title: 'Buang perubahan?', message: 'Perubahan formulir belum disimpan dan akan dibuang.', confirmLabel: 'Buang', danger: true });
    } finally { checking = false; }
  };
  const clearErrors = () => {
    const errorIds = [...panel.querySelectorAll('[data-editor-error][id]')].map(node => node.id);
    panel.querySelectorAll('[data-editor-error]').forEach(node => node.remove());
    panel.querySelector('[data-editor-errors]')?.replaceChildren();
    form.querySelectorAll('[aria-invalid]').forEach(field => field.removeAttribute('aria-invalid'));
    form.querySelectorAll('[aria-describedby]').forEach(field => {
      const ids = field.getAttribute('aria-describedby').split(' ').filter(id => !errorIds.includes(id));
      if (ids.length) field.setAttribute('aria-describedby', ids.join(' ')); else field.removeAttribute('aria-describedby');
    });
    restoredErrors = false;
  };
  const setMode = link => {
    releaseImage(); clearErrors();
    editingId = link?.dataset.id || '';
    idField.value = editingId;
    method.disabled = !editingId;
    form.action = link ? link.dataset.updateUrl : panel.dataset.createUrl;
    for (const name of ['title', 'excerpt', 'content', 'caption']) {
      const field = form.elements.namedItem(name);
      if (field) field.value = link?.dataset[name] || '';
    }
    if (form.elements.namedItem('type')) form.elements.namedItem('type').value = link?.dataset.type || 'reflection';
    if (preserveStatus) {
      setVisible(preserveStatus, Boolean(editingId)); preserveStatus.disabled = !editingId;
      form.elements.namedItem('publication_status').value = editingId ? '' : 'published';
      setVisible(statusHint, Boolean(editingId));
      statusHint.textContent = editingId ? `Status saat ini: ${link.dataset.statusLabel}` : '';
    }
    image.value = '';
    image.required = image.name === 'image' && !editingId;
    panel.querySelector('[data-editor-image-help]').textContent = editingId
      ? 'Kosongkan untuk mempertahankan gambar saat ini.'
      : image.required ? 'Gambar wajib dipilih.' : 'Gambar boleh dikosongkan.';
    currentImage = link?.dataset.image || '';
    showImage(); setVisible(cancel, Boolean(editingId)); highlight();
    heading.textContent = `${editingId ? 'Edit' : 'Tambah'} ${label}`;
    const url = new URL(location.href);
    if (editingId) url.searchParams.set('edit', editingId); else url.searchParams.delete('edit');
    url.hash = ''; history.replaceState(history.state, '', url);
    baseline = snapshot(); updateDeleteWarnings(); panelOpen(true);
  };
  const focusEditor = () => {
    panelOpen(true);
    panel.scrollIntoView({ block: 'start' });
    heading.focus({ preventScroll: true });
  };
  toggle.addEventListener('click', event => { event.preventDefault(); panelOpen(toggle.getAttribute('aria-expanded') !== 'true'); });
  cancel.addEventListener('click', async event => {
    event.preventDefault();
    if (saving || checking || !await discard()) return;
    setMode(null); focusEditor();
  });
  document.addEventListener('click', async event => {
    const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
    if (!link || link === cancel || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    if (link.matches('[data-editor-select]')) {
      event.preventDefault();
      if (saving || checking) return;
      if (link.dataset.id !== editingId) {
        if (!await discard()) return;
        setMode(link);
      }
      focusEditor();
    } else if (dirty() && !link.hasAttribute('download') && !link.target && !link.getAttribute('href').startsWith('#')) {
      event.preventDefault();
      if (saving || checking || !await discard()) return;
      location.assign(link.href);
    }
  });
  image.addEventListener('change', () => {
    releaseImage();
    if (image.files[0]) localImage = URL.createObjectURL(image.files[0]);
    showImage(); updateDeleteWarnings();
  });
  form.addEventListener('input', updateDeleteWarnings);
  form.addEventListener('change', updateDeleteWarnings);
  form.addEventListener('submit', event => {
    if (saving || checking) { event.preventDefault(); return; }
    saving = true;
    form.setAttribute('aria-busy', 'true');
    form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = true; });
  });
  window.addEventListener('pagehide', releaseImage);
  window.addEventListener('pageshow', event => {
    if (!event.persisted) return;
    saving = false;
    form.removeAttribute('aria-busy');
    form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = false; });
    releaseImage();
    if (image.files[0]) localImage = URL.createObjectURL(image.files[0]);
    showImage();
  });
  highlight(); showImage(); updateDeleteWarnings();
  panelOpen(Boolean(editingId) || restoredErrors);
  if (restoredErrors) (form.querySelector('[aria-invalid="true"]') || heading).focus();
}
