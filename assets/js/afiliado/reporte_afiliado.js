// ===============================
// COLORES DEL SISTEMA
// ===============================
const coloresEstado = {
    "Pendiente":   "#ffd000",
    "En revisión": "#2563eb",
    "Aprobado":    "#16a34a",
    "Rechazado":   "#dc2626"
};

// ===============================
// GRÁFICA 1: DONA - POR ESTADO
// ===============================
new Chart(document.getElementById("graficaEstado"), {
    type: "doughnut",
    data: {
        labels: estados,
        datasets: [{
            data: totalesEstado,
            backgroundColor: estados.map(e => coloresEstado[e] ?? "#94a3b8"),
            borderWidth: 2,
            borderColor: "#fff"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: { font: { size: 13 }, padding: 15 }
            }
        }
    }
});

// ===============================
// GRÁFICA 2: BARRAS HORIZONTALES - POR TIPO
// ===============================
new Chart(document.getElementById("graficaTipos"), {
    type: "bar",
    data: {
        labels: tipos,
        datasets: [{
            label: "Cantidad",
            data: totalesTipo,
            backgroundColor: "rgba(0, 40, 85, 0.8)",
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 } },
            y: { ticks: { font: { size: 12 } } }
        }
    }
});