import Chart from 'chart.js/auto';

function spawn(elId, h=300) {
  const el = document.getElementById(elId);
  if (!el) return;
  el.height = h;
  return new Chart(el, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
      datasets: [{ label:'Kajian', data:[12,18,25,20,30,28] }]
    },
    options: { responsive:true, maintainAspectRatio:false }
  });
}

export function initDashboardCharts() {
  spawn('chartKajianDesktop', 400);
  spawn('chartKajianMobile', 300);
}
