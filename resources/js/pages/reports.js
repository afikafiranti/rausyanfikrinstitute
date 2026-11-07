import { confirmAction, toast } from '../components/feedback';

export function initReportModals() {
  const dialog = document.getElementById('report-dialog');
  if (!dialog || typeof dialog.showModal !== 'function') return;

  const content = dialog.querySelector('[data-report-content]');
  const live = dialog.querySelector('[data-report-dialog-status]');
  let returnFocus = null;
  let returnFocusHref = null;
  let currentUrl = '';
  let request = null;
  let saving = false;
  let baseline = '';
  let previewUrl = null;
  let tooltip = null;
  let tooltipTarget = null;
  let tooltipTimer = null;
  let directReturnUrl = null;
  let reviewFocus = null;

  const sameOriginUrl = value => {
    const url = new URL(value, location.href);
    if (url.origin !== location.origin) throw new Error('Alamat laporan tidak valid.');
    return url;
  };
  const updateFilterButton = form => {
    const button = form?.querySelector('[data-report-filter-button]');
    if (!button) return;
    const applied = new URLSearchParams(location.search);
    const fields = [...form.elements].filter(field => field.name && field.matches('input, select'));
    const reset = fields.some(field => applied.get(field.name))
      && fields.every(field => field.value === (applied.get(field.name) || ''));
    button.textContent = reset ? 'Reset' : 'Filter';
    button.dataset.reportFilterAction = reset ? 'reset' : 'filter';
    button.classList.toggle('rf-btn', !reset);
    button.classList.toggle('rf-btn-secondary', reset);
  };
  updateFilterButton(document.querySelector('[data-report-filters]'));

  const formSnapshot = form => [...new FormData(form)].map(([key, value]) => [
    key,
    value instanceof File ? (value.name ? `${value.name}:${value.size}:${value.lastModified}` : '') : value,
  ]).map(pair => JSON.stringify(pair)).join('|');
  const contentSnapshot = () => [...dialog.querySelectorAll('form[data-report-form]')].map(formSnapshot).join('||');
  const isDirty = () => contentSnapshot() !== baseline;
  const releasePreview = () => {
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = null;
  };
  const hideTooltip = () => {
    clearTimeout(tooltipTimer);
    if (tooltipTarget) {
      const ids = (tooltipTarget.getAttribute('aria-describedby') || '').split(' ').filter(id => id && id !== 'report-tooltip');
      if (ids.length) tooltipTarget.setAttribute('aria-describedby', ids.join(' '));
      else tooltipTarget.removeAttribute('aria-describedby');
    }
    tooltip?.remove();
    tooltip = null;
    tooltipTarget = null;
  };
  const showTooltip = target => {
    if (tooltipTarget === target || !target.dataset.reportTooltip) return;
    hideTooltip();
    tooltipTarget = target;
    tooltip = document.createElement('div');
    tooltip.id = 'report-tooltip';
    tooltip.role = 'tooltip';
    tooltip.className = 'rf-report-tooltip';
    tooltip.textContent = target.dataset.reportTooltip;
    (dialog.contains(target) ? dialog : document.body).append(tooltip);
    target.setAttribute('aria-describedby', [target.getAttribute('aria-describedby'), tooltip.id].filter(Boolean).join(' '));
    const box = target.getBoundingClientRect();
    const tip = tooltip.getBoundingClientRect();
    tooltip.style.left = `${Math.min(innerWidth - tip.width - 8, Math.max(8, box.left + (box.width - tip.width) / 2))}px`;
    tooltip.style.top = `${box.top > tip.height + 12 ? box.top - tip.height - 8 : box.bottom + 8}px`;
  };

  const showState = (message, { retry = false, refresh = false } = {}) => {
    releasePreview();
    hideTooltip();
    content.replaceChildren();
    baseline = '';
    const panel = document.createElement('div');
    panel.className = 'p-6 sm:p-8';
    const heading = document.createElement('h2');
    heading.id = 'report-modal-heading';
    heading.tabIndex = -1;
    heading.className = 'text-xl font-semibold text-ink';
    heading.textContent = retry ? 'Laporan belum terbuka' : 'Memuat laporan';
    const explanation = document.createElement('p');
    explanation.className = 'mt-2 text-sm leading-6 text-muted';
    explanation.textContent = message;
    panel.append(heading, explanation);
    const actions = document.createElement('div');
    actions.className = 'mt-5 flex flex-wrap gap-2';
    if (retry) {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.reportRetry = '';
      button.className = 'rf-btn px-4';
      button.textContent = 'Ulangi';
      actions.append(button);
    }
    if (refresh) {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.reportRefresh = '';
      button.className = 'rf-btn-secondary px-4';
      button.textContent = 'Segarkan';
      actions.append(button);
    }
    const close = document.createElement('button');
    close.type = 'button';
    close.dataset.reportClose = '';
    if (saving) close.setAttribute('aria-disabled', 'true');
    close.className = 'rf-btn-secondary px-4';
    close.textContent = 'Tutup';
    actions.append(close);
    panel.append(actions);
    content.append(panel);
    heading.focus();
  };

  const open = () => {
    if (dialog.open) return;
    returnFocus = document.activeElement;
    returnFocusHref = returnFocus instanceof HTMLAnchorElement ? returnFocus.href : null;
    dialog.showModal();
    document.documentElement.classList.add('overflow-hidden');
  };
  const close = async () => {
    if (saving) return;
    if (isDirty() && !await confirmAction({ title: 'Tutup formulir?', message: 'Perubahan laporan belum disimpan dan akan hilang.', confirmLabel: 'Tutup', danger: true })) return;
    if (saving || !dialog.open) return;
    baseline = contentSnapshot();
    request?.abort();
    request = null;
    releasePreview();
    hideTooltip();
    dialog.close();
    document.documentElement.classList.remove('overflow-hidden');
    if (directReturnUrl) { location.assign(directReturnUrl); return; }
    const workspace = document.querySelector('[data-report-workspace]');
    const links = [...(workspace?.querySelectorAll('a[href]') || [])];
    const focus = returnFocus?.isConnected ? returnFocus
      : links.find(link => link.href === returnFocusHref)
        || links.find(link => link.href === currentUrl)
        || workspace?.querySelector('a[href], button, input, select');
    focus?.focus();
  };

  const load = async (value, intent = null) => {
    let url;
    try { url = sameOriginUrl(value); } catch { return; }
    request?.abort();
    const loading = new AbortController();
    request = loading;
    currentUrl = url.href;
    reviewFocus = intent;
    open();
    showState('Mengambil formulir atau detail laporan.');
    live.textContent = 'Memuat laporan';
    try {
      const response = await fetch(url, {
        credentials: 'same-origin', cache: 'no-store', signal: loading.signal,
        headers: { 'X-Report-Modal': '1', Accept: 'text/html' },
      });
      if (!response.ok || response.headers.get('X-Report-Modal') !== '1') throw new Error('Laporan tidak dapat dibuka. Periksa akses atau muat ulang halaman.');
      const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
      if (loading.signal.aborted || request !== loading || !dialog.open) return false;
      const root = documentFragment.querySelector('[data-report-modal-content]');
      if (!root || root.querySelector('script')) throw new Error('Respons laporan tidak valid. Muat ulang halaman.');
      releasePreview();
      hideTooltip();
      content.replaceChildren(document.importNode(root, true));
      checkPhotos();
      if (saving) content.querySelectorAll('[data-report-close]').forEach(link => link.setAttribute('aria-disabled', 'true'));
      baseline = contentSnapshot();
      live.textContent = 'Laporan terbuka';
      if (reviewFocus && content.querySelector('#decision-heading')) {
        const note = document.createElement('p');
        note.className = 'mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-900';
        note.textContent = reviewFocus === 'reject'
          ? 'Periksa isi dan foto, lalu isi alasan sebelum menolak.'
          : 'Periksa isi dan foto sebelum menyetujui.';
        content.querySelector('#decision-heading').before(note);
      }
      const decisionFocus = reviewFocus === 'reject'
        ? content.querySelector('#reject_reason')
        : reviewFocus === 'approve' ? content.querySelector('#decision-heading') : null;
      const focus = decisionFocus || content.querySelector('[aria-invalid="true"], #judul, #report-modal-heading');
      focus?.focus();
      if (reviewFocus) focus?.scrollIntoView({ block: 'center' });
      return true;
    } catch (error) {
      if (error.name === 'AbortError' || request !== loading || !dialog.open) return false;
      showState(error.message || 'Koneksi gagal. Coba lagi.', { retry: true });
      toast(error.message || 'Koneksi gagal. Coba lagi.', { type: 'error' });
      live.textContent = 'Gagal membuka laporan';
      return false;
    }
  };

  const refreshWorkspace = async () => {
    const existing = document.querySelector('[data-report-workspace]');
    if (!existing) return true;
    const response = await fetch(location.href, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'text/html' } });
    if (!response.ok) return false;
    const page = new DOMParser().parseFromString(await response.text(), 'text/html');
    const updated = page.querySelector('[data-report-workspace]');
    if (!updated) return false;
    existing.replaceWith(document.importNode(updated, true));
    if (sameOriginUrl(response.url).href !== location.href) history.replaceState(history.state, '', response.url);
    updateFilterButton(document.querySelector('[data-report-filters]'));
    return true;
  };

  const workspaceStatus = (message, error = false) => {
    const status = document.querySelector('[data-report-workspace-status]');
    if (!status) return;
    status.hidden = !message;
    status.textContent = message;
    status.classList.toggle('border-red-300', error);
    status.classList.toggle('bg-red-50', error);
    status.classList.toggle('text-red-800', error);
    status.focus();
  };

  const clearErrors = form => {
    form.querySelectorAll('[data-report-field-error]').forEach(node => node.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach(node => {
      node.removeAttribute('aria-invalid');
      const ids = (node.getAttribute('aria-describedby') || '').split(' ').filter(id => !id.startsWith('report-error-'));
      if (ids.length) node.setAttribute('aria-describedby', ids.join(' '));
      else node.removeAttribute('aria-describedby');
    });
    const summary = form.querySelector('[data-report-form-errors]');
    if (summary) { summary.hidden = true; summary.replaceChildren(); }
  };
  const showErrors = (form, payload) => {
    toast(payload.errors ? 'Periksa isian yang ditandai.' : (payload.message || 'Permintaan gagal.'), { type: 'error' });
    const summary = form.querySelector('[data-report-form-errors]');
    if (!summary) return;
    summary.hidden = false;
    const title = document.createElement('strong');
    title.textContent = 'Periksa isian berikut:';
    summary.append(title);
    const list = document.createElement('ul');
    list.className = 'mt-2 list-disc pl-5';
    Object.entries(payload.errors || {}).forEach(([field, messages], index) => {
      const message = Array.isArray(messages) ? messages[0] : String(messages);
      const item = document.createElement('li');
      item.textContent = message;
      list.append(item);
      const input = [...form.elements].find(node => node.name === field);
      if (!input) return;
      input.setAttribute('aria-invalid', 'true');
      const error = document.createElement('p');
      error.id = `report-error-${index}`;
      error.dataset.reportFieldError = '';
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
    summary.append(list);
    summary.focus();
  };

  const submit = async form => {
    if (saving) return;
    if (form.dataset.reportDecision && !await confirmAction({
      title: form.dataset.reportDecision === 'approve' ? 'Setujui laporan?' : 'Tolak laporan?',
      message: form.dataset.reportDecision === 'approve'
        ? 'Laporan akan disetujui. Keputusan ini tidak dapat diubah.'
        : 'Laporan akan ditolak dengan alasan yang ditulis. Keputusan ini tidak dapat diubah.',
      confirmLabel: form.dataset.reportDecision === 'approve' ? 'Setujui' : 'Tolak',
      danger: form.dataset.reportDecision !== 'approve',
    })) return;
    if (saving || !dialog.open) return;
    const body = new FormData(form);
    saving = true;
    dialog.setAttribute('aria-busy', 'true');
    clearErrors(form);
    const controls = [...dialog.querySelectorAll('button, input, select, textarea')];
    const originalDisabled = controls.map(control => control.disabled);
    controls.forEach(control => { control.disabled = true; });
    const closers = [...dialog.querySelectorAll('a[data-report-close]')];
    closers.forEach(link => link.setAttribute('aria-disabled', 'true'));
    const pending = document.createElement('p');
    pending.className = 'rounded-lg bg-blue-50 p-3 text-sm font-medium text-blue-900 sm:col-span-2';
    pending.setAttribute('role', 'status');
    pending.textContent = 'Menyimpan laporan…';
    form.prepend(pending);
    live.textContent = 'Menyimpan laporan';
    try {
      const response = await fetch(sameOriginUrl(form.action), {
        method: 'POST', credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body,
      });
      const payload = await response.json().catch(() => ({}));
      if (response.status === 422) {
        showErrors(form, payload);
        return;
      }
      if (!response.ok || !payload.modal_url) {
        showErrors(form, { message: payload.message || `Permintaan gagal (${response.status}). Coba lagi.` });
        return;
      }
      live.textContent = payload.message || 'Laporan tersimpan';
      toast(payload.message || 'Laporan tersimpan.', { type: 'success' });
      let refreshed = false;
      try { refreshed = await refreshWorkspace(); } catch { /* save already succeeded */ }
      const opened = await load(payload.modal_url);
      if (dialog.open) {
        const visible = [...(document.querySelector('[data-report-list]')?.querySelectorAll('a[data-report-modal]') || [])].some(link => link.href === payload.modal_url);
        if (!refreshed || !opened || !visible) {
          const notice = document.createElement('div');
          notice.setAttribute('role', 'status');
          notice.className = refreshed
            ? 'm-5 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-900'
            : 'm-5 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900';
          notice.textContent = opened
            ? (refreshed ? 'Laporan tidak tampil dalam daftar dengan filter atau halaman saat ini. ' : 'Daftar laporan belum terbarui. ')
            : 'Detail laporan belum terbuka. ';
          if (!refreshed) {
            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.reportRefresh = '';
            button.className = 'ml-2 font-semibold underline';
            button.textContent = 'Segarkan';
            notice.append(button);
          }
          content.prepend(notice);
        }
      }
    } catch {
      showErrors(form, { message: 'Koneksi terputus. Periksa daftar laporan sebelum mencoba lagi agar data tidak terkirim dua kali.' });
    } finally {
      saving = false;
      dialog.removeAttribute('aria-busy');
      controls.forEach((control, index) => { control.disabled = originalDisabled[index]; });
      dialog.querySelectorAll('[data-report-close]').forEach(link => link.removeAttribute('aria-disabled'));
      pending.remove();
    }
  };

  const deleteReport = async form => {
    if (saving || !await confirmAction({ title: 'Hapus laporan?', message: 'Laporan dan foto dokumentasinya akan dihapus permanen.', confirmLabel: 'Hapus', danger: true })) return;
    if (saving) return;
    saving = true;
    const button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;
    workspaceStatus('Menghapus laporan…');
    try {
      const response = await fetch(sameOriginUrl(form.action), {
        method: 'POST', credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
      });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || payload.deleted !== true) {
        workspaceStatus('');
        toast(payload.message || 'Laporan gagal dihapus.', { type: 'error' });
        return;
      }
      let refreshed = false;
      try { refreshed = await refreshWorkspace(); } catch { /* deletion already succeeded */ }
      workspaceStatus('');
      document.querySelector('[data-report-list] a[data-report-modal], [data-report-workspace] a[data-report-modal]')?.focus();
      toast(refreshed ? (payload.message || 'Laporan dihapus.') : 'Laporan dihapus, tetapi daftar belum terbarui. Muat ulang halaman.', { type: refreshed ? 'success' : 'error' });
    } catch {
      workspaceStatus('');
      toast('Koneksi terputus. Periksa daftar sebelum mencoba menghapus lagi.', { type: 'error' });
    } finally {
      saving = false;
      if (button?.isConnected) button.disabled = false;
    }
  };

  const restorePhoto = () => {
    releasePreview();
    const image = dialog.querySelector('[data-report-photo-preview]');
    const placeholder = dialog.querySelector('[data-report-photo-placeholder]');
    const clear = dialog.querySelector('[data-report-photo-clear]');
    if (!image) return;
    const original = image.dataset.originalSrc;
    if (original) image.src = original;
    else image.removeAttribute('src');
    image.hidden = !original;
    if (placeholder) {
      placeholder.textContent = original ? 'Foto tidak tersedia.' : 'Belum ada foto.';
      placeholder.hidden = Boolean(original);
    }
    if (clear) clear.hidden = true;
  };
  const photoFailed = image => {
    if (!(image instanceof HTMLImageElement) || !image.matches('[data-report-photo-preview], [data-report-photo]')) return;
    image.hidden = true;
    const placeholder = image.parentElement.querySelector('[data-report-photo-placeholder]');
    if (placeholder) {
      placeholder.textContent = 'Foto tidak tersedia.';
      placeholder.hidden = false;
    }
  };
  const checkPhotos = () => {
    content.querySelectorAll('img[data-report-photo-preview], img[data-report-photo]').forEach(image => {
      if (image.getAttribute('src') && image.complete && image.naturalWidth === 0) photoFailed(image);
    });
  };

  document.addEventListener('click', async event => {
    const target = event.target instanceof Element ? event.target : null;
    if (!target) return;
    const closer = target.closest('[data-report-close]');
    if (closer && dialog.contains(closer)) { event.preventDefault(); close(); return; }
    const retry = target.closest('[data-report-retry]');
    if (retry && dialog.contains(retry)) { event.preventDefault(); load(currentUrl); return; }
    const refresh = target.closest('[data-report-refresh]');
    if (refresh && dialog.contains(refresh)) { event.preventDefault(); location.reload(); return; }
    const clear = target.closest('[data-report-photo-clear]');
    if (clear && dialog.contains(clear)) {
      event.preventDefault();
      const input = dialog.querySelector('input[name="foto"]');
      if (!input) return;
      input.value = '';
      restorePhoto();
      input.focus();
      return;
    }
    const link = target.closest('a[data-report-modal]');
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') return;
    if (saving) { event.preventDefault(); return; }
    if (dialog.open && isDirty()) {
      event.preventDefault();
      if (!await confirmAction({ title: 'Pindah halaman?', message: 'Perubahan laporan belum disimpan dan akan hilang.', confirmLabel: 'Pindah', danger: true })) return;
      if (saving || !dialog.open) return;
    }
    try { sameOriginUrl(link.href); } catch { return; }
    event.preventDefault();
    load(link.href, link.dataset.reportFocus || null);
  });
  document.addEventListener('submit', event => {
    const form = event.target;
    if (form instanceof HTMLFormElement && form.matches('[data-report-filters]')) {
      updateFilterButton(form);
      if (form.querySelector('[data-report-filter-button]')?.dataset.reportFilterAction === 'reset') {
        event.preventDefault();
        location.assign(form.action);
      }
      return;
    }
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-report-row-form]')) return;
    event.preventDefault();
    deleteReport(form);
  });
  document.addEventListener('input', event => {
    updateFilterButton(event.target instanceof Element ? event.target.closest('[data-report-filters]') : null);
  });
  document.addEventListener('change', event => {
    updateFilterButton(event.target instanceof Element ? event.target.closest('[data-report-filters]') : null);
  });
  window.addEventListener('pageshow', () => updateFilterButton(document.querySelector('[data-report-filters]')));
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
  dialog.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-report-form]')) return;
    event.preventDefault();
    submit(form);
  });
  dialog.addEventListener('change', event => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || input.name !== 'foto') return;
    releasePreview();
    const image = dialog.querySelector('[data-report-photo-preview]');
    const placeholder = dialog.querySelector('[data-report-photo-placeholder]');
    const clear = dialog.querySelector('[data-report-photo-clear]');
    if (!image) return;
    if (!input.files?.[0]) { restorePhoto(); return; }
    previewUrl = URL.createObjectURL(input.files[0]);
    image.src = previewUrl;
    image.hidden = false;
    if (placeholder) placeholder.hidden = true;
    if (clear) clear.hidden = false;
  });
  dialog.addEventListener('error', event => photoFailed(event.target), true);
  document.addEventListener('pointerover', event => {
    const target = event.target instanceof Element ? event.target.closest('[data-report-tooltip]') : null;
    if (target && target !== tooltipTarget) showTooltip(target);
  });
  document.addEventListener('pointerout', event => {
    if (!tooltipTarget || tooltipTarget.contains(event.relatedTarget) || tooltip?.contains(event.relatedTarget)) return;
    clearTimeout(tooltipTimer);
    tooltipTimer = setTimeout(() => {
      if (!tooltip?.matches(':hover') && document.activeElement !== tooltipTarget) hideTooltip();
    }, 120);
  });
  document.addEventListener('focusin', event => {
    const target = event.target instanceof Element ? event.target.closest('[data-report-tooltip]') : null;
    if (target) showTooltip(target);
  });
  document.addEventListener('focusout', event => {
    if (event.target === tooltipTarget) hideTooltip();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && tooltip) {
      event.preventDefault();
      hideTooltip();
    }
  });
  window.addEventListener('scroll', hideTooltip, true);
  const standalone = [...document.querySelectorAll('[data-report-modal-content]')].find(node => !dialog.contains(node));
  if (standalone) {
    directReturnUrl = standalone.querySelector('[data-report-close]')?.href || null;
    currentUrl = location.href;
    open();
    content.replaceChildren(standalone);
    checkPhotos();
    baseline = contentSnapshot();
    content.querySelector('#report-modal-heading')?.focus();
  }
}
