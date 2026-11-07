import './bootstrap';
import 'flowbite';
import './notus';
import { initDashboardCharts } from './pages/dashboard';
import { initReportModals } from './pages/reports';
import { initMateriModals } from './pages/materi';
import { initAlumniModals } from './pages/alumni';
import { initFeedback } from './components/feedback';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.start();

function initializePage() {
  initFeedback();
  initDashboardCharts();
  initReportModals();
  initMateriModals();
  initAlumniModals();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePage, { once: true });
} else {
  initializePage();
}
