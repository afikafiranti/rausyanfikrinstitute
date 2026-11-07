const status = (id, message) => {
  document.getElementById(id + '-status').textContent = message;
};

const series = (labels, values) => {
  if (!Array.isArray(labels) || !Array.isArray(values) || labels.length !== values.length ||
      labels.some(label => typeof label !== 'string') ||
      values.some(value => !Number.isFinite(Number(value)) || Number(value) < 0)) {
    throw new Error('Invalid chart data');
  }
  return { labels: labels.map(label => label || 'Tidak diisi'), values: values.map(Number) };
};

const mount = (Chart, id, type, labels, values, dark = false) => {
  const color = dark ? '#E2E8F0' : '#334155';
  const pie = type === 'pie';
  const bar = type === 'bar';
  new Chart(document.getElementById(id), {
    type,
    data: {
      labels,
      datasets: [{
        label: 'Jumlah anggota',
        data: values,
        borderColor: pie ? '#FFFFFF' : '#A5B4FC',
        backgroundColor: pie ? ['#4F46E5', '#0F766E', '#B45309', '#9F1239'] : bar ? '#4F46E5' : '#A5B4FC',
        borderWidth: pie ? 2 : bar ? 0 : 3,
        tension: type === 'line' ? 0.25 : undefined,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: false,
      ...(bar ? { indexAxis: 'y' } : {}),
      plugins: {
        legend: { display: pie, position: 'bottom', labels: { color } },
        tooltip: { enabled: true },
      },
      ...(pie ? {} : {
        scales: {
          x: { beginAtZero: bar, ticks: { color, ...(bar ? { precision: 0 } : {}) }, grid: { color: dark ? '#334155' : '#E2E8F0' } },
          y: { beginAtZero: !bar, ticks: { color, ...(!bar ? { precision: 0 } : {}) }, grid: { color: dark ? '#334155' : '#E2E8F0', display: !bar } },
        },
      }),
    },
  });
};

const showValues = (id, labels, values) => {
  const details = document.getElementById(id + '-data');
  const rows = labels.map((label, index) => {
    const row = document.createElement('tr');
    const heading = document.createElement('th');
    heading.scope = 'row';
    heading.className = 'py-1 font-normal';
    heading.textContent = label;
    const count = document.createElement('td');
    count.className = 'py-1';
    count.textContent = new Intl.NumberFormat('id-ID').format(values[index]);
    row.append(heading, count);
    return row;
  });
  details.querySelector('tbody').replaceChildren(...rows);
  details.classList.remove('hidden');
};

async function loadChart(Chart, id, url, type = 'pie') {
  try {
    const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error('HTTP ' + response.status);
    const data = await response.json();
    const { labels, values } = series(data.labels, data.values);
    if (values.reduce((sum, value) => sum + value, 0) === 0) {
      status(id, 'Belum ada data untuk grafik ini.');
      return;
    }
    mount(Chart, id, type, labels, values);
    showValues(id, labels, values);
    status(id, 'Grafik tersedia. Angka tiap kategori dapat dibaca pada tabel.');
  } catch {
    status(id, 'Data grafik gagal dimuat. Muat ulang halaman untuk mencoba lagi.');
  }
}

export async function initDashboardCharts() {
  if (!document.getElementById('chart-alumni')) return;

  let Chart;
  try {
    ({ default: Chart } = await import('chart.js/auto'));
  } catch {
    for (const id of ['chart-alumni', 'chartStatusPernikahan', 'chartJobs']) {
      status(id, 'Grafik tidak dapat dimuat. Muat ulang halaman untuk mencoba lagi.');
    }
    return;
  }

  try {
    const data = JSON.parse(document.getElementById('chart-alumni-data').textContent);
    const { labels, values } = series(data.labels, data.series);
    if (values.reduce((sum, value) => sum + value, 0) === 0) {
      status('chart-alumni', 'Belum ada anggota baru pada tahun ini.');
    } else {
      mount(Chart, 'chart-alumni', 'line', labels, values, true);
      status('chart-alumni', 'Grafik tersedia. Angka per bulan dapat dibaca pada tabel.');
    }
  } catch {
    status('chart-alumni', 'Grafik gagal ditampilkan. Angka per bulan tetap tersedia pada tabel.');
  }

  const urls = document.getElementById('chart-data-urls').dataset;
  await Promise.all([
    loadChart(Chart, 'chartStatusPernikahan', urls.marriageUrl),
    loadChart(Chart, 'chartJobs', urls.jobsUrl, 'bar'),
  ]);
}
