<div class="earnings px-5">
    <div class="earnings-detail d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <!-- Original Boxes -->
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="dolors">{{ $totalEarnings }}</div>
            <span>Total earning</span>
        </div>
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="dolors">{{ $totalSales }}</div>
            <span>Current Balance</span>
        </div>
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="dolors">{{ $totalWithdrawals }}</div>
            <span>Total Withdraws</span>
        </div>
    </div>
    <div class="earnings-detail d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <!-- Additional Boxes -->
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-circle-dollar-to-slot"></i>
            </div>
            <div class="dolors">{{ $totalSales }}</div>
            <span>Total Sale</span>
        </div>
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-filter-circle-dollar"></i>
            </div>
            <div class="dolors">{{ $totalCommissions }}</div>
            <span>Deducted Commissions</span>
        </div>
        <div class="earning-box d-flex align-items-center justify-content-center flex-column">
            <div class="earning-box-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="dolors">{{ $totalFees }}</div>
            <span>Deducted Fees</span>
        </div>
    </div>
    <section>
        <div>
            <h3>Earnings Overview</h3>
            <div wire:ignore wire:change='updated' id="earnings-chart"></div>
        </div>
    </section>

</div>

@script
    <script type="text/javascript">
        const chartLabels = @json($chartLabels);
        const chartData = @json($chartData);

        console.log(chartLabels, chartData); // Debugging output (remove for production)

        // Initialize the chart if data exists
        if (chartLabels.length && chartData.length) {
            var options = {
                chart: {
                    type: "bar",
                    height: 350
                },
                series: [{
                    name: "Earnings",
                    data: chartData
                }],
                xaxis: {
                    categories: chartLabels,
                    title: {
                        text: "Months"
                    }
                },
                yaxis: {
                    title: {
                        text: "Earnings ($)"
                    }
                },
                title: {
                    text: "Monthly Earnings",
                    align: "center"
                },
                colors: ["#008FFB"]
            };

            // Create and render the chart
            var chart = new ApexCharts(document.querySelector("#earnings-chart"), options);
            chart.render();

            // Listen for a custom event to re-render the chart
            document.addEventListener('renderChart', function() {
                chart.destroy(); // Destroy the previous chart instance
                chart = new ApexCharts(document.querySelector("#earnings-chart"), options);
                chart.render(); // Reinitialize and render
            });
        } else {
            console.error("Chart data is empty.");
        }
    </script>
@endscript
