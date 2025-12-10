let graficoBarras = null;
let graficoPizza = null;

window.carregarGraficoBarras = async function () {
  try {
    const response = await fetch("/dashboard/mais-vendidos");
    const dados = await response.json();
    if (!Array.isArray(dados) || dados.length === 0) return;
    const labels = dados.map((item) => item.nome || "Produto");
    const valores = dados.map((item) => parseInt(item.total_vendido || 0));

    // Gerar todas as cores automaticamente via HSL
    function gerarCoresHSL(qtd, alpha) {
      const cores = [];
      for (let i = 0; i < qtd; i++) {
        const hue = Math.round((360 / qtd) * i);
        cores.push(`hsl(${hue}, 50%, 50%, ${alpha})`);
      }
      return cores;
    }
    const cores = gerarCoresHSL(labels.length, 0.8);
    const coresHover = gerarCoresHSL(labels.length, 1);

    // Ajustar largura das barras conforme tamanho da tela
    const isMobile = window.innerWidth < 768;
    const barPercentage = isMobile ? 0.7 : 0.6;
    const categoryPercentage = isMobile ? 0.8 : 0.5;

    const ctx = document.getElementById("graficoBarras");
    if (!ctx) return;
    if (graficoBarras) graficoBarras.destroy();
    graficoBarras = new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Valores",
            data: valores,
            backgroundColor: cores.slice(0, valores.length),
            hoverBackgroundColor: coresHover.slice(0, valores.length),
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: barPercentage,
            categoryPercentage: categoryPercentage,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 20,
            bottom: 10,
            left: 10,
            right: 20,
          },
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.95)",
            titleColor: "#212529",
            bodyColor: "#6B7280",
            borderColor: "rgba(0, 0, 0, 0.1)",
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            titleFont: {
              family: "Poppins",
              size: 14,
              weight: 600,
            },
            bodyFont: {
              family: "Poppins",
              size: 13,
              weight: 500,
            },
            callbacks: {
              label: function (context) {
                return `${context.raw} vendidos`;
              },
            },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              display: true,
              font: {
                family: "Poppins",
                size: 14,
                weight: 500,
              },
              color: "#6B7280",
            },
            border: { display: false },
          },
          y: {
            beginAtZero: true,
            grid: {
              display: true,
              color: "rgba(0, 0, 0, 0.03)",
              lineWidth: 1,
            },
            ticks: {
              display: true,
              font: {
                family: "Poppins",
                size: 14,
                weight: 500,
              },
              color: "#6B7280",
              padding: 10,
              maxTicksLimit: 8,
              precision: 0,
            },
            border: { display: false },
          },
        },
        interaction: {
          intersect: false,
          mode: "index",
        },
        animation: {
          duration: 1000,
          easing: "easeInOutQuart",
        },
      },
    });
  } catch (error) {
    // ...
  }
};

window.carregarGraficoPizza = async function () {
  try {
    const response = await fetch("/dashboard/estoque");
    const dados = await response.json();
    // ...
    if (!Array.isArray(dados) || dados.length === 0) {
      // ...
      return;
    }
    const labels = dados.map((item) => item.categoria || "Sem categoria");
    const valores = dados.map((item) => parseInt(item.total || 0));
    // ...

    // Cores modernas para o gráfico de pizza
    // Gerar todas as cores automaticamente via HSL
    function gerarCoresHSL(qtd, alpha) {
      const cores = [];
      for (let i = 0; i < qtd; i++) {
        const hue = Math.round((360 / qtd) * i);
        cores.push(`hsl(${hue}, 50%, 50%, ${alpha})`);
      }
      return cores;
    }
    const cores = gerarCoresHSL(labels.length, 0.9);
    const coresHover = gerarCoresHSL(labels.length, 1);
    const ctx = document.getElementById("graficoPizza");
    if (!ctx) {
      // ...
      return;
    }
    if (graficoPizza) graficoPizza.destroy();
    graficoPizza = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [
          {
            data: valores,
            backgroundColor: cores.slice(0, valores.length),
            hoverBackgroundColor: coresHover.slice(0, valores.length),
            borderWidth: 0,
            spacing: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "55%",
        radius: "75%",
        layout: {
          padding: 10,
        },
        plugins: {
          legend: {
            display: true,
            position: "right",
            align: "center",
            labels: {
              font: {
                family: "Poppins",
                size: 14,
                weight: 500,
              },
              color: "#6B7280",
              usePointStyle: true,
              pointStyle: "circle",
              padding: 20,
              boxWidth: 8,
              boxHeight: 8,
            },
          },
          tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.95)",
            titleColor: "#212529",
            bodyColor: "#6B7280",
            borderColor: "rgba(0, 0, 0, 0.1)",
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
            titleFont: {
              family: "Poppins",
              size: 14,
              weight: 600,
            },
            bodyFont: {
              family: "Poppins",
              size: 13,
              weight: 500,
            },
            callbacks: {
              label: function (context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.raw / total) * 100).toFixed(1);
                return `${context.label}: ${context.raw} (${percentage}%)`;
              },
            },
          },
        },
        elements: {
          arc: {
            borderWidth: 0,
          },
        },
        interaction: {
          intersect: false,
        },
        animation: {
          animateRotate: true,
          animateScale: true,
          duration: 1200,
          easing: "easeInOutQuart",
        },
      },
    });
  } catch (error) {
    // ...
  }
};

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("graficoBarras")) carregarGraficoBarras();
  if (document.getElementById("graficoPizza")) carregarGraficoPizza();
});
