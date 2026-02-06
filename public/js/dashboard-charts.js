document.addEventListener('DOMContentLoaded', function() {
    // Top Searched Keywords Chart
    var ctx = document.getElementById('topKeywordsChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.dashboardData.topKeywordsLabels,
            datasets: [{
                label: 'Search Count',
                data: window.dashboardData.topKeywordsData,
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { title: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Top Viewed Products Chart
    var ctx2 = document.getElementById('topProductsChart').getContext('2d');
    var chart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: window.dashboardData.topProductsLabels,
            datasets: [{
                label: 'View Count',
                data: window.dashboardData.topProductsData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { title: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Format date labels for charts as "Mon D" (e.g., Jan 1) and sort by date
    function formatDateLabelsAndSort(labels, ...dataArrays) {
        let pairs = labels.map(function(dateStr, idx) {
            let year, month, day;
            if (/^\d{8}$/.test(dateStr)) {
                year = parseInt(dateStr.substr(0,4), 10);
                month = parseInt(dateStr.substr(4,2), 10) - 1;
                day = parseInt(dateStr.substr(6,2), 10);
            } else if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                let parts = dateStr.split('-');
                year = parseInt(parts[0], 10);
                month = parseInt(parts[1], 10) - 1;
                day = parseInt(parts[2], 10);
            } else {
                return { label: dateStr, data: dataArrays.map(arr => arr[idx]), sort: dateStr };
            }
            let jsDate = new Date(year, month, day);
            return { label: jsDate, data: dataArrays.map(arr => arr[idx]), sort: jsDate.getTime() };
        });

        pairs.sort(function(a, b) { return a.sort - b.sort; });

        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        let formattedLabels = pairs.map(function(pair) {
            if (pair.label instanceof Date && !isNaN(pair.label)) {
                return months[pair.label.getMonth()] + ' ' + pair.label.getDate();
            }
            return pair.label;
        });
        let sortedDataArrays = dataArrays.map((_, i) => pairs.map(pair => pair.data[i]));

        return { labels: formattedLabels, data: sortedDataArrays };
    }

    // --- Chart Data Preparation ---
    var all_dates = Array.from(new Set([
        ...window.dashboardData.gaViewsLabels,
        ...window.dashboardData.userTrendLabels
    ]));

    var gaViewsMap = {};
    window.dashboardData.gaViewsLabels.forEach(function(label, idx) {
        gaViewsMap[label] = window.dashboardData.gaViewsData[idx];
    });
    var userTrendMap = {};
    window.dashboardData.userTrendLabels.forEach(function(label, idx) {
        userTrendMap[label] = window.dashboardData.userTrendData[idx];
    });

    var visitsData = all_dates.map(function(date) {
        return gaViewsMap[date] !== undefined ? gaViewsMap[date] : 0;
    });
    var usersData = all_dates.map(function(date) {
        return userTrendMap[date] !== undefined ? userTrendMap[date] : 0;
    });

    var formatted = formatDateLabelsAndSort(all_dates, visitsData, usersData);

    // --- Dual Axis Chart ---
    var dualAxisCtx = document.getElementById('visitsActiveUsersChart').getContext('2d');
    var visitsActiveUsersChart = new Chart(dualAxisCtx, {
        type: 'line',
        data: {
            labels: formatted.labels,
            datasets: [
                {
                    label: 'Website Visits',
                    data: formatted.data[0],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    yAxisID: 'y',
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    fill: false
                },
                {
                    label: 'Active Users',
                    data: formatted.data[1],
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            stacked: false,
            plugins: {
                legend: { display: true },
                title: { display: false }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Website Visits' },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Active Users' },
                    grid: { drawOnChartArea: false },
                    beginAtZero: true
                }
            }
        }
    });

    // --- Combo Chart (Bar + Line) ---
    var comboCtx = document.getElementById('visitsActiveUsersComboChart').getContext('2d');
    var visitsActiveUsersComboChart = new Chart(comboCtx, {
        data: {
            labels: formatted.labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Website Visits',
                    data: formatted.data[0],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    yAxisID: 'y',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Active Users',
                    data: formatted.data[1],
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            stacked: false,
            plugins: {
                legend: { display: true },
                title: { display: false }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Website Visits' },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Active Users' },
                    grid: { drawOnChartArea: false },
                    beginAtZero: true
                }
            }
        }
    });

    // --- Chart Toggle Buttons ---
    var dualBtn = document.getElementById('showDualAxisBtn');
    var comboBtn = document.getElementById('showComboBtn');
    var dualContainer = document.getElementById('dualAxisChartContainer');
    var comboContainer = document.getElementById('comboChartContainer');

    dualBtn.addEventListener('click', function() {
        dualBtn.classList.add('active');
        comboBtn.classList.remove('active');
        dualContainer.style.display = '';
        comboContainer.style.display = 'none';
    });
    comboBtn.addEventListener('click', function() {
        comboBtn.classList.add('active');
        dualBtn.classList.remove('active');
        dualContainer.style.display = 'none';
        comboContainer.style.display = '';
    });   
});
