let activeConfirmation = false;
const confirmedForms = new WeakSet();

const syncToasts = () => {
  const container = document.getElementById('rf-feedback-toasts');
  if (!container) return;
  const host = [...document.querySelectorAll('dialog[open]')].at(-1) || document.body;
  const popover = typeof container.showPopover === 'function' && typeof container.hidePopover === 'function';
  try {
    if (popover && container.matches(':popover-open')) container.hidePopover();
    if (container.parentElement !== host) host.append(container);
    container.hidden = !container.children.length;
    if (popover && container.children.length) container.showPopover();
    else if (!popover) container.removeAttribute('popover');
  } catch {
    container.removeAttribute('popover');
    container.hidden = !container.children.length;
  }
};

export function confirmAction({ title, message, confirmLabel = 'Lanjut', danger = false }) {
  const dialog = document.getElementById('rf-feedback-dialog');
  if (!dialog || typeof dialog.showModal !== 'function' || activeConfirmation) return Promise.resolve(false);
  activeConfirmation = true;
  const returnFocus = document.activeElement;
  dialog.querySelector('#rf-feedback-title').textContent = title;
  dialog.querySelector('#rf-feedback-message').textContent = message;
  const accept = dialog.querySelector('[data-feedback-confirm]');
  const cancel = dialog.querySelector('[data-feedback-cancel]');
  accept.textContent = confirmLabel;
  accept.classList.toggle('rf-btn-danger', danger);
  accept.classList.toggle('rf-btn', !danger);
  dialog.showModal();
  syncToasts();
  cancel.focus();
  return new Promise(resolve => {
    const finish = answer => {
      dialog.close();
      syncToasts();
      activeConfirmation = false;
      accept.removeEventListener('click', approve);
      cancel.removeEventListener('click', reject);
      dialog.removeEventListener('cancel', escape);
      returnFocus?.isConnected && returnFocus.focus();
      resolve(answer);
    };
    const approve = () => finish(true);
    const reject = () => finish(false);
    const escape = event => { event.preventDefault(); reject(); };
    accept.addEventListener('click', approve);
    cancel.addEventListener('click', reject);
    dialog.addEventListener('cancel', escape);
  });
}

export function toast(message, { type = 'info' } = {}) {
  const container = document.getElementById('rf-feedback-toasts');
  if (!container || !message) return;
  const item = document.createElement('div');
  item.className = `rf-toast rf-toast-${['success', 'error', 'info'].includes(type) ? type : 'info'}`;
  item.setAttribute('role', type === 'error' ? 'alert' : 'status');
  const text = document.createElement('span');
  text.textContent = message;
  const dismiss = document.createElement('button');
  dismiss.type = 'button';
  dismiss.className = 'rf-toast-dismiss';
  dismiss.setAttribute('aria-label', 'Tutup notifikasi');
  dismiss.textContent = '×';
  item.append(text, dismiss);
  container.append(item);
  syncToasts();
  let timer;
  let remaining = 6000;
  let started;
  const stop = () => { clearTimeout(timer); if (started) remaining -= Date.now() - started; started = null; };
  const start = () => {
    if (type === 'error' || started || item.matches(':hover') || item.contains(document.activeElement)) return;
    started = Date.now();
    timer = setTimeout(remove, Math.max(0, remaining));
  };
  const remove = () => { stop(); item.remove(); syncToasts(); };
  dismiss.addEventListener('click', remove);
  item.addEventListener('mouseenter', stop);
  item.addEventListener('mouseleave', start);
  item.addEventListener('focusin', stop);
  item.addEventListener('focusout', event => { if (!item.contains(event.relatedTarget)) start(); });
  start();
}

export function initFeedback() {
  if (document.documentElement.dataset.feedbackReady) return;
  document.documentElement.dataset.feedbackReady = 'true';
  document.addEventListener('close', syncToasts, true);
  let tooltip = null;
  let tooltipTarget = null;
  const hideTooltip = () => {
    if (tooltipTarget) {
      const ids = (tooltipTarget.getAttribute('aria-describedby') || '').split(' ').filter(id => id && id !== 'rf-action-tooltip');
      if (ids.length) tooltipTarget.setAttribute('aria-describedby', ids.join(' '));
      else tooltipTarget.removeAttribute('aria-describedby');
    }
    tooltip?.remove(); tooltip = null; tooltipTarget = null;
  };
  const showTooltip = target => {
    if (target === tooltipTarget) return;
    hideTooltip(); tooltipTarget = target;
    target.removeAttribute('title');
    tooltip = document.createElement('div');
    tooltip.id = 'rf-action-tooltip'; tooltip.role = 'tooltip'; tooltip.className = 'rf-action-tooltip';
    tooltip.textContent = target.dataset.actionTooltip;
    ([...document.querySelectorAll('dialog[open]')].at(-1) || document.body).append(tooltip);
    target.setAttribute('aria-describedby', [target.getAttribute('aria-describedby'), tooltip.id].filter(Boolean).join(' '));
    const box = target.getBoundingClientRect(); const tip = tooltip.getBoundingClientRect();
    tooltip.style.left = `${Math.max(8, Math.min(innerWidth - tip.width - 8, box.left + (box.width - tip.width) / 2))}px`;
    tooltip.style.top = `${box.top > tip.height + 12 ? box.top - tip.height - 8 : Math.min(innerHeight - tip.height - 8, box.bottom + 8)}px`;
  };
  const actionTarget = event => event.target instanceof Element ? event.target.closest('[data-action-tooltip]') : null;
  document.addEventListener('pointerover', event => { const target = actionTarget(event); if (target && event.pointerType !== 'touch') showTooltip(target); });
  document.addEventListener('pointerout', event => { if (tooltipTarget && !tooltipTarget.contains(event.relatedTarget)) hideTooltip(); });
  document.addEventListener('focusin', event => { const target = actionTarget(event); if (target) showTooltip(target); });
  document.addEventListener('focusout', hideTooltip);
  document.addEventListener('click', hideTooltip);
  document.addEventListener('scroll', hideTooltip, true);
  document.addEventListener('close', hideTooltip, true);
  document.addEventListener('keydown', event => { if (event.key === 'Escape') hideTooltip(); });
  document.querySelectorAll('[data-feedback-flash]').forEach(node => toast(node.textContent.trim(), { type: node.dataset.feedbackType }));
  document.addEventListener('submit', async event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirmMessage) return;
    if (confirmedForms.has(form)) { confirmedForms.delete(form); return; }
    event.preventDefault();
    if (!await confirmAction({
      title: form.dataset.confirmTitle || 'Konfirmasi tindakan',
      message: form.dataset.confirmMessage,
      confirmLabel: form.dataset.confirmLabel || 'Lanjut',
      danger: form.hasAttribute('data-confirm-danger'),
    })) return;
    confirmedForms.add(form);
    form.requestSubmit(event.submitter || undefined);
  }, true);
}
