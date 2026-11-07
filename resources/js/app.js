import './bootstrap';
import 'flowbite';
import './notus';
import { initDashboardCharts } from './pages/dashboard';
import { initReportModals } from './pages/reports';
import { initMateriModals } from './pages/materi';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.start();

function initAlumniDialog() {
  const dialog = document.getElementById('modal-register-alumni');
  if (!dialog) return;
  const trigger = document.getElementById('open-alumni-modal');
  const close = document.getElementById('close-alumni-modal');
  let returnFocus = trigger;
  const focusable = () => [...dialog.querySelectorAll('button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), a[href]')]
    .filter(node => node.getClientRects().length > 0);
  const focusFirstField = () => (dialog.querySelector('[aria-invalid="true"]') || dialog.querySelector('#new-alumni-name') || dialog).focus();
  const toggle = show => {
    if (show) returnFocus = document.activeElement;
    dialog.classList.toggle('hidden', !show);
    dialog.setAttribute('aria-hidden', String(!show));
    trigger.setAttribute('aria-expanded', String(show));
    document.documentElement.classList.toggle('overflow-hidden', show);
    if (show) focusFirstField();
    else returnFocus?.focus();
  };

  trigger.addEventListener('click', () => toggle(true));
  close.addEventListener('click', () => toggle(false));
  dialog.addEventListener('click', event => {
    if (event.target === dialog) toggle(false);
  });
  document.addEventListener('keydown', event => {
    if (dialog.classList.contains('hidden')) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      toggle(false);
    } else if (event.key === 'Tab') {
      const nodes = focusable();
      const first = nodes[0];
      const last = nodes[nodes.length - 1];
      if (event.shiftKey && (document.activeElement === first || !dialog.contains(document.activeElement))) {
        event.preventDefault();
        last?.focus();
      } else if (!event.shiftKey && (document.activeElement === last || !dialog.contains(document.activeElement))) {
        event.preventDefault();
        first?.focus();
      }
    }
  });
  document.addEventListener('focusin', event => {
    if (!dialog.classList.contains('hidden') && !dialog.contains(event.target)) {
      focusable()[0]?.focus();
    }
  });
  if (!dialog.classList.contains('hidden')) {
    document.documentElement.classList.add('overflow-hidden');
    focusFirstField();
  }
}

function initializePage() {
  initDashboardCharts();
  initAlumniDialog();
  initReportModals();
  initMateriModals();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePage, { once: true });
} else {
  initializePage();
}
