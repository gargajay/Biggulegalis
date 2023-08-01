

function createBarChart(data, id) {
    const labels = data.map(d => d.month);
    const values = data.map(d => d.total);

    const chartConfig = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#FF9999', '#FFCC99', '#FFFF99', '#CCFF99', '#99FF99', '#99FFCC', '#99CCFF', '#9999FF', '#FF99FF', '#FF99CC'],
                borderColor: 'black',
                borderWidth: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    type: 'logarithmic',
                    ticks: {
                        precision: 0,
                        stepSize: 1
                    }
                }                
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };

    const chartElement = document.getElementById(id);
    const myChart = new Chart(chartElement, chartConfig);
}