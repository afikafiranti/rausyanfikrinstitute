export function initVerificationDialog() {
  const dialog = document.getElementById('verification-dialog');
  if (!dialog || typeof dialog.showModal !== 'function') return;

  const forms = dialog.querySelector('[data-verification-forms]');
  const result = dialog.querySelector('[data-verification-result]');
  let trigger = null;
  let triggerHref = null;
  let saving = false;
  let baseline = '';
  const snapshot = () => forms.hidden ? '' : [...forms.querySelectorAll('form')]
    .map(form => JSON.stringify([...new FormData(form)])).join('|');
  const safeUrl = value => {
    const url = new URL(value, location.href);
    if (url.origin !== location.origin) throw new Error('Alamat verifikasi tidak valid.');
    return url;
  };
  const clearErrors = form => {
    form.querySelectorAll('[aria-invalid]').forEach(field => field.removeAttribute('aria-invalid'));
    const summary = form.querySelector('[data-verification-errors]');
    summary.hidden = true;
    summary.replaceChildren();
  };
  const showErrors = (form, payload) => {
    const summary = form.querySelector('[data-verification-errors]');
    summary.hidden = false;
    summary.replaceChildren();
    const list = document.createElement('ul');
    list.className = 'list-disc pl-5';
    for (const [name, messages] of Object.entries(payload.errors || {})) {
      const item = document.createElement('li');
      item.textContent = Array.isArray(messages) ? messages[0] : String(messages);
      list.append(item);
      [...form.elements].find(field => field.name === name)?.setAttribute('aria-invalid', 'true');
    }
    if (!list.children.length) list.textContent = payload.message || 'Permintaan gagal. Coba lagi.';
    summary.append(list);
    summary.focus();
  };
  const close = () => {
    if (saving || (snapshot() !== baseline && !confirm('Perubahan belum disimpan. Tutup verifikasi?'))) return;
    dialog.close();
    document.documentElement.classList.remove('overflow-hidden');
    const fallback = [...document.querySelectorAll('[data-verification-user]')].find(link => link.href === triggerHref);
    (trigger?.isConnected ? trigger : fallback || document.getElementById('alumni-search'))?.focus();
  };
  const refreshResults = async () => {
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
  document.addEventListener('click', event => {
    const link = event.target instanceof Element ? event.target.closest('[data-verification-user]') : null;
    if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    let approveUrl;
    let rejectUrl;
    try { approveUrl = safeUrl(link.dataset.approveUrl); rejectUrl = safeUrl(link.dataset.rejectUrl); } catch { return; }
    event.preventDefault();
    trigger = link;
    triggerHref = link.href;
    forms.hidden = false;
    result.hidden = true;
    result.replaceChildren();
    dialog.querySelector('[data-verification-name]').textContent = link.dataset.name;
    dialog.querySelector('[data-verification-email]').textContent = link.dataset.email;
    forms.querySelectorAll('form').forEach(form => {
      form.reset();
      clearErrors(form);
      form.action = form.dataset.verificationForm === 'approve' ? approveUrl.href : rejectUrl.href;
    });
    baseline = snapshot();
    dialog.showModal();
    document.documentElement.classList.add('overflow-hidden');
    dialog.querySelector('#verification-level').focus();
  });
  dialog.addEventListener('click', event => {
    if (event.target instanceof Element && event.target.closest('[data-verification-close]')) close();
    else if (event.target === dialog) {
      const box = dialog.getBoundingClientRect();
      if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) close();
    }
  });
  dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
  dialog.addEventListener('keydown', event => {
    if (event.key !== 'Tab') return;
    const focusable = [...dialog.querySelectorAll('button:not(:disabled), select:not(:disabled), textarea:not(:disabled), a[href]')]
      .filter(element => element.getClientRects().length);
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
  });
  dialog.addEventListener('submit', async event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-verification-form]')) return;
    event.preventDefault();
    if (saving) return;
    if (!confirm(form.dataset.verificationForm === 'approve'
      ? 'Setujui akun ini dan aktifkan akses sesuai level yang dipilih?'
      : 'Kirim alasan perbaikan kepada pemilik akun? Akun tetap menunggu verifikasi.')) return;
    const body = new FormData(form);
    const controls = [...dialog.querySelectorAll('button, select, textarea, input')];
    const disabled = controls.map(control => control.disabled);
    saving = true;
    clearErrors(form);
    dialog.setAttribute('aria-busy', 'true');
    controls.forEach(control => { control.disabled = true; });
    result.textContent = 'Menyimpan keputusan…';
    result.hidden = false;
    try {
      const response = await fetch(safeUrl(form.action), {
        method: 'POST', credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body,
      });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || typeof payload.message !== 'string') {
        result.hidden = true;
        showErrors(form, payload.message ? payload : { message: `Verifikasi gagal (${response.status}). Coba lagi.` });
        return;
      }
      let refreshed = false;
      try { refreshed = await refreshResults(); } catch { /* decision already saved */ }
      forms.hidden = true;
      baseline = snapshot();
      result.textContent = payload.message;
      if (!refreshed) result.append(' Daftar belum terbarui. Muat ulang halaman setelah menutup modal.');
      result.focus();
    } catch {
      result.hidden = true;
      showErrors(form, { message: 'Koneksi terputus. Periksa status akun sebelum mencoba kembali.' });
    } finally {
      saving = false;
      dialog.removeAttribute('aria-busy');
      controls.forEach((control, index) => { control.disabled = disabled[index]; });
      form.querySelector('[aria-invalid="true"]')?.focus();
    }
  });
  window.addEventListener('beforeunload', event => {
    if (saving || (dialog.open && snapshot() !== baseline)) { event.preventDefault(); event.returnValue = ''; }
  });
}
