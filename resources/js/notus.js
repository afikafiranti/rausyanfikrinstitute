// Tutup dropdown saat klik di luar trigger/menu
document.addEventListener('click', (e) => {
  document.querySelectorAll('[data-dropdown-trigger]').forEach((trigger) => {
    const id = trigger.getAttribute('data-dropdown-trigger');
    const menu = document.getElementById(id);
    if (!menu) return;
    const clickedInside = trigger.contains(e.target) || menu.contains(e.target);
    if (!clickedInside) {
      menu.classList.add('hidden');
      trigger.setAttribute('aria-expanded', 'false');
    }
  });
});
let drawerTrigger = null;
const backgroundState = new Map();
const focusableIn = el => [...el.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex="0"]')]
  .filter(node => node.getClientRects().length > 0);

window.rfToggleDrawer = (id, open) => {
  const el = document.getElementById(id);
  if (!el) return;
  const show = open ?? el.classList.contains('hidden');
  if (show === !el.classList.contains('hidden')) return;
  if (show) drawerTrigger = document.activeElement;
  el.classList.toggle('hidden', !show);
  el.setAttribute('aria-hidden', String(!show));
  document.querySelectorAll(`[aria-controls="${id}"]`).forEach(trigger => trigger.setAttribute('aria-expanded', String(show)));
  document.documentElement.classList.toggle('overflow-hidden', show); // lock scroll
  if (show) {
    for (const node of document.body.children) {
      if (node === el || node.tagName === 'SCRIPT') continue;
      backgroundState.set(node, node.inert);
      node.inert = true;
    }
    focusableIn(el)[0]?.focus();
  } else {
    for (const [node, wasInert] of backgroundState) node.inert = wasInert;
    backgroundState.clear();
    drawerTrigger?.focus();
    drawerTrigger = null;
  }
};
document.addEventListener('keydown', e => {
  const drawer = document.getElementById('rf-mobile-drawer');
  if (drawer && !drawer.classList.contains('hidden')) {
    if (e.key === 'Escape') {
      e.preventDefault();
      window.rfToggleDrawer(drawer.id, false);
    } else if (e.key === 'Tab') {
      const nodes = focusableIn(drawer);
      const first = nodes[0];
      const last = nodes[nodes.length - 1];
      if (e.shiftKey && (document.activeElement === first || !drawer.contains(document.activeElement))) {
        e.preventDefault();
        last?.focus();
      } else if (!e.shiftKey && (document.activeElement === last || !drawer.contains(document.activeElement))) {
        e.preventDefault();
        first?.focus();
      }
    }
  } else if (e.key === 'Escape') {
    document.querySelectorAll('[data-dropdown-trigger]').forEach(trigger => {
      const menu = document.getElementById(trigger.getAttribute('data-dropdown-trigger'));
      if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.focus();
      }
    });
  }
});
