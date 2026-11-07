import { confirmAction, toast } from '../components/feedback';

export function initAlumniModals() {
  const dialog = document.getElementById('alumni-dialog');
  if (!dialog || typeof dialog.showModal !== 'function') return;
  const createHost = document.getElementById('alumni-create-fallback');
  const createTemplate = document.createElement('template');
  createTemplate.content.append(createHost.querySelector('[data-alumni-modal-content]'));
  createHost.hidden = true;
  const content = dialog.querySelector('[data-alumni-content]');
  const live = dialog.querySelector('[data-alumni-dialog-status]');
  let trigger = null;
  let triggerHref = null;
  let currentUrl = '';
  let request = null;
  let baseline = '';
  let saving = false;
  let checking = false;
  const safeUrl = value => {
    const url = new URL(value, location.href);
    if (url.origin !== location.origin) throw new Error('Alamat alumni tidak valid.');
    return url;
  };
  const snapshot = () => [...content.querySelectorAll('form[data-alumni-form]:not([data-alumni-delete])')]
    .map(form => JSON.stringify([...new FormData(form)])).join('|');
  const discard = async () => {
    if (snapshot() === baseline) return true;
    if (checking) return false;
    checking = true;
    try {
      return await confirmAction({ title: 'Tutup formulir?', message: 'Perubahan belum disimpan dan akan dibuang.', confirmLabel: 'Buang', danger: true });
    } finally { checking = false; }
  };
  const close = async (saved = false) => {
    if (!saved && (saving || !await discard())) return;
    request?.abort();
    request = null;
    baseline = snapshot();
    dialog.close();
    document.documentElement.classList.remove('overflow-hidden');
    trigger?.setAttribute('aria-expanded', 'false');
    const fallback = [...document.querySelectorAll('[data-alumni-modal]')].find(link => link.href === triggerHref);
    (trigger?.isConnected ? trigger : fallback || document.getElementById('alumni-search'))?.focus();
  };
  const state = (message, retry = false) => {
    content.replaceChildren();
    baseline = '';
    const panel = document.createElement('section');
    panel.className = 'p-5 sm:p-6';
    const title = document.createElement('h2');
    title.id = 'alumni-modal-heading';
    title.tabIndex = -1;
    title.className = 'pr-20 text-xl font-semibold';
    title.textContent = retry ? 'Alumni belum terbuka' : 'Memuat alumni';
    const explanation = document.createElement('p');
    explanation.className = 'mt-4 text-sm text-muted';
    explanation.textContent = message;
    panel.append(title, explanation);
    if (retry) {
      const button = document.createElement('button');
      button.type = 'button'; button.className = 'rf-btn mt-5';
      button.dataset.alumniRetry = ''; button.textContent = 'Ulangi'; panel.append(button);
    }
    content.append(panel); title.focus();
  };
  const load = async (link, retry = false) => {
    if (saving || checking || (!retry && dialog.open && !await discard())) return;
    request?.abort();
    const loading = new AbortController(); request = loading;
    if (!dialog.open) {
      trigger = link; triggerHref = link.href;
      dialog.showModal(); document.documentElement.classList.add('overflow-hidden');
      trigger.setAttribute('aria-expanded', 'true');
    }
    if (link.dataset.alumniModal === 'create') {
      content.replaceChildren(createTemplate.content.cloneNode(true));
      baseline = snapshot(); content.querySelector('input:not([type="hidden"])')?.focus(); return;
    }
    const url = safeUrl(link.href);
    url.hash = '';
    url.searchParams.set('modal', link.dataset.alumniModal === 'edit' ? '1' : link.dataset.alumniModal);
    currentUrl = url.href;
    state('Mengambil data atau formulir alumni.');
    live.textContent = 'Memuat alumni';
    try {
      const response = await fetch(url, { credentials: 'same-origin', cache: 'no-store', signal: loading.signal, headers: { Accept: 'text/html' } });
      if (!response.ok || response.headers.get('X-Alumni-Modal') !== '1') throw new Error('Data tidak dapat dibuka. Periksa akses atau muat ulang halaman.');
      const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
      const root = parsed.querySelector('[data-alumni-modal-content]');
      if (!root || root.querySelector('script')) throw new Error('Respons data tidak valid. Muat ulang halaman.');
      if (loading.signal.aborted || request !== loading || !dialog.open) return;
      content.replaceChildren(document.importNode(root, true));
      baseline = snapshot(); live.textContent = 'Alumni terbuka';
      (content.querySelector('[aria-invalid="true"], #verification-level, input:not([type="hidden"])') || content.querySelector('#alumni-modal-heading'))?.focus();
    } catch (error) {
      if (error.name === 'AbortError' || request !== loading || !dialog.open) return;
      state(error.message || 'Koneksi terputus. Coba lagi.', true);
      toast('Data alumni gagal dibuka.', { type: 'error' });
    }
  };
  const clearErrors = form => {
    form.querySelectorAll('[data-alumni-error]').forEach(node => node.remove());
    form.querySelectorAll('[aria-invalid]').forEach(node => {
      node.removeAttribute('aria-invalid');
      const ids = (node.getAttribute('aria-describedby') || '').split(' ').filter(id => id && !id.startsWith('alumni-error-'));
      if (ids.length) node.setAttribute('aria-describedby', ids.join(' ')); else node.removeAttribute('aria-describedby');
    });
  };
  const errors = (form, payload) => {
    clearErrors(form);
    const summary = document.createElement('div');
    summary.dataset.alumniError = ''; summary.role = 'alert'; summary.tabIndex = -1;
    summary.className = 'mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800 sm:col-span-2';
    summary.textContent = payload.message || 'Permintaan belum berhasil. Periksa isian lalu coba lagi.';
    Object.entries(payload.errors || {}).forEach(([name, messages], index) => {
      const field = [...form.elements].find(node => node.name === name && node.type !== 'hidden');
      if (!field) return;
      const error = document.createElement('p');
      error.dataset.alumniError = ''; error.id = `alumni-error-${index}`;
      error.className = 'mt-1 text-sm text-red-700'; error.textContent = Array.isArray(messages) ? messages[0] : String(messages);
      field.insertAdjacentElement('afterend', error);
      field.setAttribute('aria-invalid', 'true');
      field.setAttribute('aria-describedby', [field.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
    });
    form.prepend(summary);
    toast(summary.textContent, { type: 'error' });
    summary.focus();
  };
  const refresh = async () => {
    const response = await fetch(location.href, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'text/html' } });
    if (!response.ok) return false;
    const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
    const updated = parsed.querySelector('[data-alumni-results]');
    const existing = document.querySelector('[data-alumni-results]');
    if (!updated || !existing) return false;
    existing.replaceWith(document.importNode(updated, true));
    if (safeUrl(response.url).href !== location.href) history.replaceState(history.state, '', response.url);
    return true;
  };
  document.addEventListener('click', async event => {
    const link = event.target instanceof Element ? event.target.closest('[data-alumni-modal]') : null;
    if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault(); await load(link);
  });
  dialog.addEventListener('click', async event => {
    if (event.target instanceof Element && event.target.closest('[data-alumni-close]')) { event.preventDefault(); await close(); }
    else if (event.target instanceof Element && event.target.closest('[data-alumni-retry]')) {
      const link = document.createElement('a'); link.href = currentUrl;
      const value = new URL(currentUrl).searchParams.get('modal'); link.dataset.alumniModal = value === '1' ? 'edit' : value;
      await load(link, true);
    } else if (event.target === dialog) {
      const box = dialog.getBoundingClientRect();
      if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) await close();
    }
  });
  dialog.addEventListener('cancel', event => { event.preventDefault(); void close(); });
  dialog.addEventListener('submit', async event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-alumni-form]')) return;
    event.preventDefault();
    if (saving || checking) return;
    if (form.dataset.alumniConfirm) {
      checking = true;
      try {
        if (!await confirmAction({ title: form.dataset.alumniConfirmTitle || 'Konfirmasi', message: form.dataset.alumniConfirm, confirmLabel: form.dataset.alumniConfirmLabel || 'Lanjut', danger: form.dataset.alumniDanger === 'true' })) return;
      } finally { checking = false; }
    }
    const body = new FormData(form);
    const controls = [...dialog.querySelectorAll('button, input, select, textarea')];
    const disabled = controls.map(control => control.disabled);
    saving = true; clearErrors(form); dialog.setAttribute('aria-busy', 'true');
    controls.forEach(control => { control.disabled = true; }); live.textContent = 'Menyimpan alumni';
    try {
      const response = await fetch(safeUrl(form.action), { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || typeof payload.message !== 'string') { errors(form, payload.message ? payload : { message: `Permintaan gagal (${response.status}). Coba lagi.` }); return; }
      let refreshed = false;
      try { refreshed = await refresh(); } catch { /* Mutation already saved. */ }
      await close(true);
      toast(payload.message, { type: 'success' });
      if (!refreshed) toast('Perubahan tersimpan, tetapi daftar belum terbarui. Muat ulang halaman.', { type: 'error' });
    } catch {
      errors(form, { message: 'Koneksi terputus. Periksa data sebelum mencoba kembali.' });
    } finally {
      saving = false; dialog.removeAttribute('aria-busy');
      controls.forEach((control, index) => { control.disabled = disabled[index]; });
      form.querySelector('[aria-invalid="true"]')?.focus();
    }
  });
  if (dialog.dataset.autoCreate === 'true') void load(document.getElementById('open-alumni-modal'));
}
