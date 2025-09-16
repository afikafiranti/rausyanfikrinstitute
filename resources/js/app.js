import './bootstrap';
import Alpine from 'alpinejs';
import { initDashboardCharts } from './pages/dashboard';

document.addEventListener('DOMContentLoaded', () => {
  initDashboardCharts();
});


window.Alpine = Alpine;

Alpine.start();
