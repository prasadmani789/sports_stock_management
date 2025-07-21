// script.js

$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2();

    // Initialize DataTable
    $('#example').DataTable();

    // Example: Toggle sidebar (if applicable)
    $('#sidebarToggle').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    // Example: Chart.js demo
    if (document.getElementById("myChart")) {
        const ctx = document.getElementById("myChart").getContext("2d");
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow'],
                datasets: [{
                    label: 'Sample Data',
                    data: [12, 19, 3],
                    backgroundColor: ['#dc3545', '#007bff', '#ffc107']
                }]
            }
        });
    }
});