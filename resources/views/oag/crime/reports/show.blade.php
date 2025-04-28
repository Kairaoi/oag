@extends('layouts.app')

@section('styles')
<style>
    /* General container */
    .report-container {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin: 2rem auto;
        max-width: 1200px;
    }

    /* Report Header */
    .report-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .report-title {
        font-weight: 700;
        font-size: 2rem;
        color: #212529;
    }
    .report-description {
        color: #6c757d;
        margin-top: 0.5rem;
        font-size: 1.1rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Controls (Dropdowns and Buttons) */
    .controls-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.75rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .control-group {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .control-label {
        font-weight: 600;
        color: #495057;
    }
    .chart-selector {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        font-size: 1rem;
    }
    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        background: #ffffff;
        border: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        transition: 0.2s;
    }
    .action-btn:hover {
        background: #e9ecef;
    }
    .primary-btn {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .primary-btn:hover {
        background-color: #0b5ed7;
    }

    /* Chart container */
    .chart-container {
        background: #fff;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        height: 450px;
    }

    /* Enhanced Table Styles */
    .data-table-wrapper {
        background: #ffffff;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
        margin-bottom: 2rem;
    }
    
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.95rem;
        color: #495057;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
    }
    
    .data-table thead th {
        background: linear-gradient(145deg, #0d6efd, #4d91ff);
        color: #ffffff;
        font-weight: 600;
        padding: 1.2rem 1rem;
        text-align: left;
        position: sticky;
        top: 0;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }
    
    .data-table thead th:first-child {
        border-top-left-radius: 0.75rem;
    }
    
    .data-table thead th:last-child {
        border-top-right-radius: 0.75rem;
    }
    
    .data-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 0.75rem;
    }
    
    .data-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 0.75rem;
    }
    
    .data-table tbody td {
        padding: 1rem;
        background-color: #fff;
        border-bottom: 1px solid #f1f3f5;
        transition: all 0.2s ease;
        vertical-align: middle;
    }
    
    .data-table tbody tr:hover td {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03);
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .data-table tbody tr:nth-child(even) td {
        background-color: #f9fafb;
    }
    
    .data-table tbody tr:nth-child(even):hover td {
        background-color: #f3f4f6;
    }
    
    /* Cell types - customize based on your data */
    .data-table .numeric-cell {
        text-align: right;
        font-family: 'Consolas', 'Monaco', monospace;
        font-weight: 500;
    }
    
    .data-table .date-cell {
        font-family: 'Segoe UI', 'Roboto', sans-serif;
        white-space: nowrap;
    }
    
    .data-table .status-cell {
        text-align: center;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    
    /* Status indicators */
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    .status-active .status-indicator {
        background-color: #10b981;
    }
    
    .status-pending .status-indicator {
        background-color: #f59e0b;
    }
    
    .status-inactive .status-indicator {
        background-color: #ef4444;
    }
    
    /* Table pagination */
    .table-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    
    .pagination-info {
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .pagination-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pagination-button {
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    
    .pagination-button:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
    
    .pagination-button.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    
    .pagination-button:disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    
    /* Table search and filters */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .table-search {
        position: relative;
        width: 100%;
        max-width: 300px;
    }
    
    .search-input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #4b5563;
        transition: all 0.15s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
    }
    
    .search-icon {
        position: absolute;
        top: 50%;
        left: 0.75rem;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    
    .table-filters {
        display: flex;
        gap: 0.5rem;
    }
    
    .filter-button {
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    
    .filter-button:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
    
    .filter-active {
        background-color: #eff6ff;
        border-color: #0d6efd;
        color: #0d6efd;
    }
    
    /* Empty state */
    .no-data {
        text-align: center;
        padding: 3rem;
        background: #f8f9fa;
        border-radius: 0.75rem;
        color: #6c757d;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .no-data i {
        font-size: 2.5rem;
        color: #9ca3af;
    }
    
    .no-data p {
        font-size: 1.1rem;
        font-weight: 500;
        margin: 0;
    }
    
    .no-data .action-hint {
        font-size: 0.95rem;
        color: #6b7280;
        max-width: 400px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .controls-card {
            flex-direction: column;
            align-items: stretch;
        }
        .control-group {
            width: 100%;
            justify-content: space-between;
        }
        .data-table {
            font-size: 0.85rem;
        }
        .data-table thead th,
        .data-table tbody td {
            padding: 0.75rem 0.5rem;
        }
        .table-controls {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        .table-search {
            max-width: 100%;
        }
    }
    
</style>
@endsection

@section('content')
<div class="container report-container">

    <!-- Report Header -->
    <div class="report-header">
        <h2 class="report-title">{{ $report->name }}</h2>
        <p class="report-description">{{ $report->description }}</p>
    </div>

    <!-- Controls -->
    <div class="controls-card">
        <div class="control-group">
            <label for="chartType" class="control-label">Chart Type:</label>
            <select id="chartType" class="chart-selector">
                <option value="column">Column</option>
                <option value="line">Line</option>
                <option value="area">Area</option>
                <option value="pie">Pie</option>
                <option value="bar">Bar</option>
                <option value="spline">Spline</option>
            </select>
        </div>

        <div class="control-group">
            <button id="downloadBtn" class="action-btn primary-btn">
                <i class="fas fa-download"></i> Download
            </button>
            <button id="fullscreenBtn" class="action-btn">
                <i class="fas fa-expand"></i> Fullscreen
            </button>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-container">
        <div id="chartContainer"></div>
    </div>

    <!-- Enhanced Data Table -->
    @if (count($results))
        <div class="data-table-wrapper">
            <!-- Table Controls -->
            <div class="table-controls">
                <div class="table-search">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search data..." id="tableSearch">
                </div>
                <div class="table-filters">
                    <button class="filter-button filter-active">
                        <i class="fas fa-filter"></i> All
                    </button>
                    <button class="filter-button">
                        <i class="fas fa-sort-amount-up"></i> Sort
                    </button>
                </div>
            </div>
            
            <!-- Data Table -->
            <table class="data-table">
    <thead>
        <tr>
            @foreach($columns as $column)
                <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($results as $row)
            <tr>
                @foreach((array) $row as $key => $value)
                    @if (is_numeric($value))
                        <td class="numeric-cell">{{ $value }}</td>
                    @elseif (strtotime($value) !== false)
                        <td class="date-cell">{{ $value }}</td>
                    @elseif (in_array(strtolower($value), ['active', 'pending', 'inactive']))
                        <td class="status-cell status-{{ strtolower($value) }}">
                            <span class="status-indicator"></span>{{ $value }}
                        </td>
                    @else
                        <td>{{ $value }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<style>
    /* Table Container Style */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        background: linear-gradient(to right, #ff7e5f, #feb47b); /* Gradient background */
    }

    /* Table Header Style */
    .data-table th {
        padding: 16px 20px;
        background: #333;
        color: white;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }

    /* Table Body Style */
    .data-table td {
        padding: 12px 18px;
        text-align: left;
        font-size: 14px;
        border-bottom: 2px solid #f4f4f4;
        transition: all 0.3s ease;
    }

    /* Row Hover Effect */
    .data-table tr:hover td {
        background-color: #f1f1f1;
        transform: scale(1.02);
    }

    /* Row Colors - Alternating */
    .data-table tr:nth-child(odd) {
        background-color: #fff;
    }

    .data-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Numeric Cell Alignment */
    .numeric-cell {
        text-align: right;
        font-weight: bold;
        color: #ff7e5f; /* Accent color */
    }

    /* Date Cell Styling */
    .date-cell {
        text-align: center;
        font-style: italic;
        color: #2d2d2d;
    }

    /* Status Cell Styling */
    .status-cell {
        text-align: center;
        font-weight: bold;
        text-transform: capitalize;
    }

    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .status-active .status-indicator {
        background-color: #28a745; /* Green */
    }

    .status-pending .status-indicator {
        background-color: #ffc107; /* Yellow */
    }

    .status-inactive .status-indicator {
        background-color: #dc3545; /* Red */
    }

    /* Adding shadows to rows on hover */
    .data-table tr:hover {
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .data-table {
            font-size: 12px;
        }

        .data-table th, .data-table td {
            padding: 10px;
        }
    }
</style>

            
            <!-- Table Pagination -->
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <strong>1-{{ min(10, count($results)) }}</strong> of <strong>{{ count($results) }}</strong> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-button" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="pagination-button active">1</button>
                    @if (count($results) > 10)
                        <button class="pagination-button">2</button>
                    @endif
                    @if (count($results) > 20)
                        <button class="pagination-button">3</button>
                    @endif
                    @if (count($results) > 30)
                        <button class="pagination-button">...</button>
                    @endif
                    <button class="pagination-button">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-database"></i>
            <p>No data available for this report</p>
            <div class="action-hint">Try adjusting your filters or selection criteria to see results.</div>
        </div>
    @endif

</div>

<!-- Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    Highcharts.setOptions({
        colors: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1', '#dc3545', '#0dcaf0'],
        chart: { style: { fontFamily: 'Segoe UI, Roboto, Arial, sans-serif' }, animation: { duration: 500 } },
        tooltip: { backgroundColor: '#fff', borderRadius: 8, shadow: true, style: { color: '#212529' }},
        credits: { enabled: false },
    });

    const chart = Highcharts.chart('chartContainer', {
        chart: { type: 'column' },
        title: { text: '{{ $report->name }}' },
        subtitle: { text: 'Data Visualization', style: { color: '#6c757d' }},
        xAxis: { categories: {!! json_encode($chartData['categories']) !!} },
        yAxis: { title: { text: 'Values' }},
        series: [{
            name: '{{ $report->name }}',
            data: {!! json_encode($chartData['data']) !!}
        }]
    });

    document.getElementById('chartType').addEventListener('change', function () {
        const selectedType = this.value;
        if (selectedType === 'pie') {
            const pieData = {!! json_encode($chartData['categories']) !!}.map((cat, idx) => ({
                name: cat,
                y: {!! json_encode($chartData['data']) !!}[idx]
            }));
            chart.update({
                chart: { type: 'pie' },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        showInLegend: true
                    }
                },
                series: [{ type: 'pie', name: '{{ $report->name }}', data: pieData }]
            });
        } else {
            chart.update({
                chart: { type: selectedType },
                series: [{
                    type: selectedType,
                    name: '{{ $report->name }}',
                    data: {!! json_encode($chartData['data']) !!}
                }]
            });
        }
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        const format = confirm('Download as SVG? Click Cancel for PNG.') ? 'image/svg+xml' : 'image/png';
        chart.exportChart({ type: format, filename: '{{ $report->name }}' });
    });

    document.getElementById('fullscreenBtn').addEventListener('click', function () {
        const container = document.querySelector('.chart-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });

    document.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement) {
            chart.setSize(window.innerWidth - 100, window.innerHeight - 100, false);
        } else {
            chart.setSize(null, 450, false);
        }
    });
    
    // Table search functionality
    document.getElementById('tableSearch').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.data-table tbody tr');
        
        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Make table rows interactive
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            // Remove selected class from all rows
            tableRows.forEach(r => r.classList.remove('selected'));
            // Add selected class to clicked row
            this.classList.add('selected');
            
            // You could also highlight corresponding chart data if needed
            // const index = Array.from(tableRows).indexOf(this);
            // chart.series[0].data[index].select(true, true);
        });
    });
});
</script>
@endsection