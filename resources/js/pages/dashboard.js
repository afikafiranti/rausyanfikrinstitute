import Chart from 'chart.js/auto';

export function initDashboardCharts() {
  const el = document.getElementById('chartKajian');
  if (!el) return;
  const ctx = el.getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
      datasets: [{ label:'Kajian', data:[12,18,25,20,30,28] }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
}
