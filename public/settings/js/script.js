const barGraphic = document.getElementById("graficoBarras").getContext("2d");

new Chart(barGraphic, {
  type: "bar",
  data: {
    labels: [
      "Colares",
      "Brincos",
      "Anéis",
      "Pulseiras",
      "Conjuntos",
      "Argolas",
    ],
    datasets: [
      {
        label: "Valores",
        data: [60, 50, 80, 40, 70, 30],
        backgroundColor: [
          "rgba(99, 102, 241, 0.8)",
          "rgba(16, 185, 129, 0.8)",
          "rgba(245, 158, 11, 0.8)",
          "rgba(239, 68, 68, 0.8)",
          "rgba(139, 92, 246, 0.8)",
          "rgba(6, 182, 212, 0.8)",
        ],
        hoverBackgroundColor: [
          "rgba(99, 102, 241, 1)",
          "rgba(16, 185, 129, 1)",
          "rgba(245, 158, 11, 1)",
          "rgba(239, 68, 68, 1)",
          "rgba(139, 92, 246, 1)",
          "rgba(6, 182, 212, 1)",
        ],
        borderRadius: 8,
        borderSkipped: false,
        barThickness: 40,
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
        right: 10,
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
        border: {
          display: false,
        },
      },
      y: {
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
          stepSize: 20,
        },
        border: {
          display: false,
        },
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

const circleGraphic = document.getElementById("graficoPizza").getContext("2d");

new Chart(circleGraphic, {
  type: "doughnut",
  data: {
    labels: [
      "Colares",
      "Brincos",
      "Anéis",
      "Pulseiras",
      "Conjuntos",
      "Argolas",
    ],
    datasets: [
      {
        data: [60, 50, 80, 40, 70, 30],
        backgroundColor: [
          "rgba(99, 102, 241, 0.9)",
          "rgba(16, 185, 129, 0.9)",
          "rgba(245, 158, 11, 0.9)",
          "rgba(239, 68, 68, 0.9)",
          "rgba(139, 92, 246, 0.9)",
          "rgba(6, 182, 212, 0.9)",
        ],
        hoverBackgroundColor: [
          "rgba(99, 102, 241, 1)",
          "rgba(16, 185, 129, 1)",
          "rgba(245, 158, 11, 1)",
          "rgba(239, 68, 68, 1)",
          "rgba(139, 92, 246, 1)",
          "rgba(6, 182, 212, 1)",
        ],
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
