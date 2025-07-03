function applyTheme(theme) {
    const html = document.documentElement;
    html.classList.remove('light', 'dark');
    html.classList.add(theme);
    localStorage.setItem('stratum-theme', theme);

    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.innerHTML = theme === 'dark'
            ? '☀️<span class="ml-3">Mode clair</span>'
            : '🌙<span class="ml-3">Mode sombre</span>';
    }
}

function toggleTheme() {
    const html = document.documentElement;
    const current = html.classList.contains('dark') ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);
}

document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const current = html.classList.contains('dark') ? 'dark' : 'light';
    applyTheme(current);

    // Charge le graphique par défaut
    if (typeof loadVisitorChart === 'function') {
        loadVisitorChart(7);
    }

    // Boutons de filtre
    document.querySelectorAll('[data-range]').forEach(btn => {
        btn.addEventListener('click', () => {
            const days = btn.dataset.range;
            loadVisitorChart(days);

            document.querySelectorAll('[data-range]').forEach(b => {
                b.classList.remove('bg-primary', 'text-primary-foreground');
                b.classList.add('text-muted-foreground');
            });

            btn.classList.add('bg-primary', 'text-primary-foreground');
            btn.classList.remove('text-muted-foreground');
        });
    });
});

/* Chargement AJAX des données visiteurs */
function loadVisitorChart(days = 7) {
    fetch(`/admin/visitors/data/${days}`)
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.label);
            const values = data.map(d => d.count);
            initVisitorChart(labels, values);
        });
}

/* Initialise le chart visiteurs avec Chart.js */
function initVisitorChart(labels, data) {
    const chartEl = document.getElementById('visitorsChart');
    if (!chartEl) return;

    const ctx = chartEl.getContext('2d');

    if (chartEl.chartInstance) {
        chartEl.chartInstance.destroy();
    }

    const getVar = (name, fallback) =>
        getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;

    const primary = `hsl(${getVar('--primary', '271 91% 65%')})`;
    const foreground = `hsl(${getVar('--foreground', '222 84% 5%')})`;
    const border = `hsl(${getVar('--border', '214 32% 91%')})`;
    const card = `hsl(${getVar('--card', '0 0% 100%')})`;
    const muted = `hsl(${getVar('--muted-foreground', '215 16% 47%')})`;

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visiteurs',
                data: data,
                fill: false,
                borderColor: primary,
                backgroundColor: primary,
                tension: 0.3,
                pointBackgroundColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: card,
                    titleColor: foreground,
                    bodyColor: foreground,
                    borderColor: border,
                    borderWidth: 1,
                    padding: 10
                }
            },
            scales: {
                x: {
                    ticks: { color: muted },
                    grid: { color: border }
                },
                y: {
                    ticks: { color: muted },
                    grid: { color: border }
                }
            }
        }
    });

    chartEl.chartInstance = chart;
}
