// Tutup dropdown saat klik di luar trigger/menu
document.addEventListener('click', (e) => {
  document.querySelectorAll('[data-dropdown-trigger]').forEach((trigger) => {
    const id = trigger.getAttribute('data-dropdown-trigger');
    const menu = document.getElementById(id);
    if (!menu) return;
    const clickedInside = trigger.contains(e.target) || menu.contains(e.target);
    if (!clickedInside) menu.classList.add('hidden');
  });
});
window.rfToggleDrawer = (id, open) => {
  const el = document.getElementById(id);
  if (!el) return;
  const show = open ?? el.classList.contains('hidden');
  el.classList.toggle('hidden', !show);
  document.documentElement.classList.toggle('overflow-hidden', show); // lock scroll
};
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') rfToggleDrawer('rf-mobile-drawer', false);
});
