export function initMateriModals() {
  const dialog = document.getElementById('materi-dialog');
  if (!dialog || typeof dialog.showModal !== 'function') return;

  const content = dialog.querySelector('[data-materi-content]');
  const live = dialog.querySelector('[data-materi-dialog-status]');
  let returnFocus = null;
  let returnHref = null;
  let currentUrl = '';
  let request = null;
  let saving = false;
  let baseline = '';
  let directReturnUrl = null;

  const sameOriginUrl = value => {
    const url = new URL(value, location.href);
    if (url.origin !== location.origin) throw new Error('Alamat materi tidak valid.');
    return url;
  };
  const updateSearchButton = form => {
    const button = form?.querySelector('[data-materi-search-button]');
    const input = form?.querySelector('[name="q"]');
    if (!button || !input) return;
    const applied = new URLSearchParams(location.search).get('q') || '';
    const reset = applied.trim() !== '' && input.value === applied;
    button.textContent = reset ? 'Reset' : 'Cari';
    button.dataset.materiSearchAction = reset ? 'reset' : 'search';
    button.classList.toggle('rf-btn', !reset);
    button.classList.toggle('rf-btn-secondary', reset);
  };
  updateSearchButton(document.querySelector('[data-materi-search]'));

  const snapshot = () => [...content.querySelectorAll('form[data-materi-form]:not([data-materi-delete])')]
    .map(form => [...new FormData(form)].map(([key, value]) => JSON.stringify([key, value])).join('|')).join('||');
  const isDirty = () => snapshot() !== baseline;
  const workspaceStatus = (message, error = false) => {
    const status = document.querySelector('[data-materi-workspace-status]');
    if (!status) return;
    status.hidden = false;
    status.textContent = message;
    status.classList.toggle('border-red-300', error);
    status.classList.toggle('bg-red-50', error);
    status.classList.toggle('text-red-800', error);
    status.focus();
  };
  const showState = (message, failed = false) => {
    content.replaceChildren();
    baseline = '';
    const panel = document.createElement('div');
    panel.className = 'p-6 sm:p-8';
    const heading = document.createElement('h2');
    heading.id = 'materi-modal-heading';
    heading.tabIndex = -1;
    heading.className = 'text-xl font-semibold text-ink';
    heading.textContent = failed ? 'Materi belum terbuka' : 'Memuat materi';
    const text = document.createElement('p');
    text.className = 'mt-2 text-sm leading-6 text-muted';
    text.textContent = message;
    const actions = document.createElement('div');
    actions.className = 'mt-5 flex flex-wrap gap-2';
    if (failed) {
      const retry = document.createElement('button');
      retry.type = 'button';
      retry.dataset.materiRetry = '';
      retry.className = 'rf-btn px-4';
      retry.textContent = 'Ulangi';
      actions.append(retry);
    }
    const close = document.createElement('button');
    close.type = 'button';
    close.dataset.materiClose = '';
    close.className = 'rf-btn-secondary px-4';
    close.textContent = 'Tutup';
    actions.append(close);
    panel.append(heading, text, actions);
    content.append(panel);
    heading.focus();
  };
  const open = () => {
    if (dialog.open) return;
    returnFocus = document.activeElement;
    returnHref = returnFocus instanceof HTMLAnchorElement ? returnFocus.href : null;
    dialog.showModal();
    document.documentElement.classList.add('overflow-hidden');
  };
  const close = (saved = false) => {
    if (saving && !saved) return;
    if (!saved && isDirty() && !confirm('Perubahan belum disimpan. Tutup formulir?')) return;
    baseline = snapshot();
    request?.abort();
    request = null;
    dialog.close();
    document.documentElement.classList.remove('overflow-hidden');
    if (directReturnUrl) { location.assign(directReturnUrl); return; }
    const workspace = document.querySelector('[data-materi-workspace]');
    const links = [...(workspace?.querySelectorAll('a[href]') || [])];
    const focus = returnFocus?.isConnected ? returnFocus
      : links.find(link => link.href === returnHref)
        || links.find(link => link.href === currentUrl)
        || workspace?.querySelector('a[href], button, input');
    focus?.focus();
  };
  const load = async value => {
    let url;
    try { url = sameOriginUrl(value); } catch { return false; }
    request?.abort();
    const loading = new AbortController();
    request = loading;
    currentUrl = url.href;
    open();
    showState('Mengambil formulir atau detail materi.');
    live.textContent = 'Memuat materi';
    try {
      const response = await fetch(url, {
        credentials: 'same-origin', cache: 'no-store', signal: loading.signal,
        headers: { 'X-Materi-Modal': '1', Accept: 'text/html' },
      });
      if (!response.ok || response.headers.get('X-Materi-Modal') !== '1') throw new Error('Materi tidak dapat dibuka. Periksa akses atau muat ulang halaman.');
      const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
      if (loading.signal.aborted || request !== loading || !dialog.open) return false;
      const root = parsed.querySelector('[data-materi-modal-content]');
      if (!root || root.querySelector('script')) throw new Error('Respons materi tidak valid. Muat ulang halaman.');
      content.replaceChildren(document.importNode(root, true));
      if (saving) content.querySelectorAll('[data-materi-close]').forEach(link => link.setAttribute('aria-disabled', 'true'));
      baseline = snapshot();
      live.textContent = 'Materi terbuka';
      content.querySelector('[aria-invalid="true"], #title, #video_title, #materi-modal-heading')?.focus();
      return true;
    } catch (error) {
      if (error.name === 'AbortError' || request !== loading || !dialog.open) return false;
      showState(error.message || 'Koneksi gagal. Coba lagi.', true);
      live.textContent = 'Gagal membuka materi';
      return false;
    }
  };
  const refreshWorkspace = async () => {
    const existing = document.querySelector('[data-materi-workspace]');
    if (!existing) return true;
    const response = await fetch(location.href, {
      credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'text/html' },
    });
    if (!response.ok) return false;
    const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
    const updated = parsed.querySelector('[data-materi-workspace]');
    if (!updated) return false;
    existing.replaceWith(document.importNode(updated, true));
    if (sameOriginUrl(response.url).href !== location.href) history.replaceState(history.state, '', response.url);
    updateSearchButton(document.querySelector('[data-materi-search]'));
    return true;
  };
  const clearErrors = form => {
    form.querySelectorAll('[data-materi-field-error]').forEach(node => node.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach(node => {
      node.removeAttribute('aria-invalid');
      const ids = (node.getAttribute('aria-describedby') || '').split(' ').filter(id => !id.startsWith('materi-error-'));
      if (ids.length) node.setAttribute('aria-describedby', ids.join(' '));
      else node.removeAttribute('aria-describedby');
    });
    const summary = form.querySelector('[data-materi-form-errors]');
    if (summary) { summary.hidden = true; summary.replaceChildren(); }
  };
  const showErrors = (form, payload) => {
    const summary = form.querySelector('[data-materi-form-errors]');
    if (!summary) return;
    summary.hidden = false;
    const title = document.createElement('strong');
    title.textContent = 'Periksa isian berikut:';
    const list = document.createElement('ul');
    list.className = 'mt-2 list-disc pl-5';
    Object.entries(payload.errors || {}).forEach(([field, messages], index) => {
      const message = Array.isArray(messages) ? messages[0] : String(messages);
      const item = document.createElement('li');
      item.textContent = message;
      list.append(item);
      const input = [...form.elements].find(node => node.name === field && node.type !== 'hidden');
      if (!input) return;
      input.setAttribute('aria-invalid', 'true');
      const error = document.createElement('p');
      error.id = `materi-error-${index}`;
      error.dataset.materiFieldError = '';
      error.className = 'mt-1 text-sm text-red-700';
      error.textContent = message;
      input.insertAdjacentElement('afterend', error);
      input.setAttribute('aria-describedby', [input.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
    });
    if (!list.children.length) {
      const item = document.createElement('li');
      item.textContent = payload.message || 'Permintaan gagal. Periksa isian lalu coba lagi.';
      list.append(item);
    }
    summary.append(title, list);
    summary.focus();
  };
  const submit = async form => {
    if (saving) return;
    const body = new FormData(form);
    saving = true;
    dialog.setAttribute('aria-busy', 'true');
    clearErrors(form);
    const controls = [...dialog.querySelectorAll('button, input, select, textarea')];
    const originalDisabled = controls.map(control => control.disabled);
    controls.forEach(control => { control.disabled = true; });
    dialog.querySelectorAll('[data-materi-close]').forEach(link => link.setAttribute('aria-disabled', 'true'));
    const pending = document.createElement('p');
    pending.className = 'rounded-lg bg-blue-50 p-3 text-sm font-medium text-blue-900 sm:col-span-2';
    pending.role = 'status';
    pending.textContent = 'Menyimpan materi…';
    form.prepend(pending);
    live.textContent = 'Menyimpan materi';
    try {
      const response = await fetch(sameOriginUrl(form.action), {
        method: 'POST', credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body,
      });
      const payload = await response.json().catch(() => ({}));
      if (response.status === 422) { showErrors(form, payload); return; }
      if (!response.ok || (!payload.modal_url && payload.deleted !== true)) {
        showErrors(form, { message: payload.message || `Permintaan gagal (${response.status}). Coba lagi.` });
        return;
      }
      let refreshed = false;
      try { refreshed = await refreshWorkspace(); } catch { /* mutation already succeeded */ }
      if (payload.deleted === true) {
        baseline = snapshot();
        if (directReturnUrl) {
          showState(payload.message || 'Playlist dihapus.');
          content.querySelector('#materi-modal-heading').textContent = 'Playlist dihapus';
          live.textContent = payload.message || 'Playlist dihapus.';
          return;
        }
        close(true);
        workspaceStatus(refreshed ? (payload.message || 'Playlist dihapus.') : 'Playlist dihapus, tetapi daftar belum terbarui. Muat ulang halaman.', !refreshed);
        return;
      }
      const opened = await load(payload.modal_url);
      if (!dialog.open) return;
      const notice = document.createElement('p');
      notice.role = 'status';
      notice.className = refreshed
        ? 'm-5 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-900'
        : 'm-5 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900';
      const visible = [...(document.querySelector('[data-materi-list]')?.querySelectorAll('a[data-materi-modal]') || [])]
        .some(link => link.href === payload.modal_url);
      notice.textContent = opened
        ? `${payload.message || 'Materi tersimpan.'} ${refreshed && !visible ? 'Playlist tidak tampil dalam daftar dengan pencarian atau halaman saat ini.' : ''}`
        : 'Perubahan tersimpan; detail belum terbuka.';
      if (!refreshed) notice.append(' Daftar belum terbarui. Muat ulang halaman.');
      content.prepend(notice);
      live.textContent = payload.message || 'Materi tersimpan';
    } catch {
      showErrors(form, { message: 'Koneksi terputus. Periksa daftar materi sebelum mencoba lagi agar data tidak terkirim dua kali.' });
    } finally {
      saving = false;
      dialog.removeAttribute('aria-busy');
      controls.forEach((control, index) => { control.disabled = originalDisabled[index]; });
      dialog.querySelectorAll('[data-materi-close]').forEach(link => link.removeAttribute('aria-disabled'));
      pending.remove();
      form.querySelector('[aria-invalid="true"]')?.focus();
    }
  };

  document.addEventListener('click', event => {
    const target = event.target instanceof Element ? event.target : null;
    if (!target) return;
    const closer = target.closest('[data-materi-close]');
    if (closer && dialog.contains(closer)) { event.preventDefault(); close(); return; }
    const retry = target.closest('[data-materi-retry]');
    if (retry && dialog.contains(retry)) { event.preventDefault(); load(currentUrl); return; }
    const link = target.closest('a[data-materi-modal]');
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') return;
    if (saving) { event.preventDefault(); return; }
    if (dialog.open && isDirty() && !confirm('Perubahan belum disimpan. Pindah formulir?')) { event.preventDefault(); return; }
    try { sameOriginUrl(link.href); } catch { return; }
    event.preventDefault();
    load(link.href);
  });
  document.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-materi-search]')) return;
    updateSearchButton(form);
    if (form.querySelector('[data-materi-search-button]')?.dataset.materiSearchAction === 'reset') {
      event.preventDefault();
      location.assign(form.action);
    }
  });
  document.addEventListener('input', event => {
    updateSearchButton(event.target instanceof Element ? event.target.closest('[data-materi-search]') : null);
  });
  document.addEventListener('change', event => {
    updateSearchButton(event.target instanceof Element ? event.target.closest('[data-materi-search]') : null);
  });
  window.addEventListener('pageshow', () => updateSearchButton(document.querySelector('[data-materi-search]')));
  dialog.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-materi-form]')) return;
    event.preventDefault();
    submit(form);
  });
  dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
  dialog.addEventListener('keydown', event => {
    if (event.key !== 'Tab') return;
    const nodes = [...dialog.querySelectorAll('a[href]:not([aria-disabled="true"]), button:not(:disabled), input:not(:disabled):not([type="hidden"]), select:not(:disabled), textarea:not(:disabled)')]
      .filter(node => node.getClientRects().length > 0);
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (!first) return;
    if (event.shiftKey && (document.activeElement === first || document.activeElement === dialog)) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && (document.activeElement === last || document.activeElement === dialog)) {
      event.preventDefault();
      first.focus();
    }
  });
  dialog.addEventListener('click', event => {
    if (event.target !== dialog) return;
    const bounds = content.getBoundingClientRect();
    if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
  });
  window.addEventListener('beforeunload', event => {
    if (saving || (dialog.open && isDirty())) { event.preventDefault(); event.returnValue = ''; }
  });

  const standalone = [...document.querySelectorAll('[data-materi-modal-content]')].find(node => !dialog.contains(node));
  if (standalone) {
    directReturnUrl = standalone.querySelector('[data-materi-close]')?.href || null;
    currentUrl = location.href;
    open();
    content.replaceChildren(standalone);
    baseline = snapshot();
    content.querySelector('#materi-modal-heading')?.focus();
  }
}
