import Chart from 'chart.js/auto';

const charts = {};
const destroy = (id) => { if (charts[id]) { charts[id].destroy(); delete charts[id]; } };
const mount = (id, type, h, labels, data) => {
  const el = document.getElementById(id);
  if (!el) return;
  el.height = h;
  destroy(id);
  charts[id] = new Chart(el.getContext('2d'), {
    type,
    data: { labels, datasets: [{ label: 'Alumni', data, borderWidth: 2, fill: type === 'line' ? false : undefined }] },
    options: {
      responsive: true, maintainAspectRatio: false, animation: false,
      plugins: { legend: { display: false }, tooltip: { enabled: true } },
      scales: { y: { beginAtZero: true } }
    }
  });
};

async function getJSON(url) {
  try {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
    if (!r.ok) throw new Error('HTTP '+r.status);
    return await r.json();
  } catch (_) { return null; }
}

export async function initDashboardCharts() {
  const year = new Date().getFullYear();
  const monthly = await getJSON(`/charts/alumni/monthly?year=${year}`);
  if (!monthly) return;

  // Desktop: line 12 bulan
  mount('chartKajianDesktop', 'line', 400, monthly.labels, monthly.values);

  // Mobile: bar ambil 6 bulan terakhir
  const last6Labels = monthly.labels.slice(-6);
  const last6Values = monthly.values.slice(-6);
  mount('chartKajianMobile', 'bar', 300, last6Labels, last6Values);
}
