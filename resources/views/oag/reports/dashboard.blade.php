@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Council</h1>

    <form action="{{ route('crime.council.update', $council->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="council_name" class="text-white">Council Name</label>
            <input type="text" class="form-control @error('council_name') is-invalid @enderror" id="council_name" name="council_name" value="{{ old('council_name', $council->council_name) }}" required>
            @error('council_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        @extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #343a40;
            color: #fff;
            font-weight: 600;
        }

        .chart-container {
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                {{ $report->name }} Dashboard
            </div>
            <div class="card-body">
            @if ($report->id == 1)
    {{-- Render highchart for Project Expenditures by Activity, Year, and Budget --}}
    <div class="chart-container">
        <div id="projectExpendituresChart" style="height: 400px;"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var categories = [];
            var budgetData = [];
            var expenditureData = [];

            @foreach($results as $result)
                categories.push('{{ $result->project_title }} - {{ $result->year }}');
                budgetData.push({{ $result->budget }});
                expenditureData.push({{ $result->total_expenditure }});
            @endforeach

            Highcharts.chart('projectExpendituresChart', {
                chart: {
                    type: 'bar',
                    backgroundColor: '#f9f9f9',
                    borderRadius: 5
                },
                title: {
                    text: 'Project Expenditures by Activity, Year, and Budget',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold',
                        color: '#333'
                    }
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Projects - Year',
                        style: {
                            fontSize: '14px',
                            color: '#666'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    },
                    lineColor: '#ccc',
                    tickColor: '#ccc'
                },
                yAxis: {
                    title: {
                        text: 'Amount',
                        style: {
                            fontSize: '14px',
                            color: '#666'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    },
                    gridLineColor: '#f3f3f3'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        stacking: 'normal',
                        borderRadius: 5,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Budget',
                    data: budgetData,
                    color: '#5cb85c'
                }, {
                    name: 'Expenditure',
                    data: expenditureData,
                    color: '#f0ad4e'
                }],
                legend: {
                    itemStyle: {
                        fontSize: '12px',
                        color: '#666'
                    }
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                        }
                    }
                }
            });
        });
    </script>
@elseif ($report->id == 2)
    {{-- Render highchart for Total Expenditure by Project report --}}
    <div class="chart-container">
        <div id="totalExpenditureChart"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var data = [
                @foreach($results as $result)
                    { name: '{{ $result->project_code}}', y: {{ $result->total_expenditure }} },
                @endforeach
            ];

            Highcharts.chart('totalExpenditureChart', {
                chart: {
                    type: 'pie',
                    height: 400
                },
                title: {
                    text: 'Total Expenditure by Project'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    name: 'Total Expenditure',
                    colorByPoint: true,
                    data: data
                }],
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                        }
                    }
                }
            });
        });
    </script>
            @elseif ($report->id == 3)
    {{-- Render highchart for Financial Reporting --}}
    <div class="chart-container">
        <div id="financialReportingChart"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var categories = [];
            var budgetData = [];
            var actualExpenditureData = [];
            var budgetVarianceData = [];

            @foreach($results as $result)
                categories.push('{{ $result->{"Project Title"} }}');
                budgetData.push({{ $result->{"Initial Fund"} }});
                actualExpenditureData.push({{ $result->{"Actual Expenditure"} }});
                budgetVarianceData.push({{ $result->{"Budget Variance"} }});
            @endforeach

            Highcharts.chart('financialReportingChart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Project Financial Overview'
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Projects'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Amount'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: (Highcharts.defaultOptions.title.style && Highcharts.defaultOptions.title.style.color) || 'gray'
                        }
                    }
                },
                legend: {
                    align: 'right',
                    x: -30,
                    verticalAlign: 'top',
                    y: 25,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    borderColor: '#CCC',
                    borderWidth: 1,
                    shadow: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Budget',
                    data: budgetData
                }, {
                    name: 'Actual Expenditure',
                    data: actualExpenditureData
                }, {
                    name: 'Budget Variance',
                    data: budgetVarianceData
                }],
                credits: {
                    enabled: false // Disable the Highcharts logo
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                        }
                    }
                }
            });
        });
    </script>
            @elseif ($report->id == 4)
    {{-- Render highchart for Aggregate Spending by Islands per Project with Activity Descriptions --}}
    <div class="chart-container">
        <div id="aggregateSpendingChart"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var data = [];

            @foreach($results as $result)
                data.push({
                    name: '{{ $result->project_title }} - {{ $result->activity_description }}',
                    y: {{ $result->total_spending }}
                });
            @endforeach

            Highcharts.chart('aggregateSpendingChart', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Aggregate Spending by Islands per Project with Activity Descriptions'
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%', // This makes it a donut chart
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.y:.1f}'
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'Total Spending',
                    colorByPoint: true,
                    data: data
                }],
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                        }
                    }
                }
            });
        });
    </script>
  @elseif ($report->id == 5)
    {{-- Render highchart for Project Allocations with Balances --}}
    <div class="chart-container">
        <div id="projectAllocationsChart"></div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var categories = [];
            var warrantedAmountData = [];
            var allocationAmountData = [];
            var balanceAfterAllocationData = [];

            @foreach($results as $result)
                categories.push('{{ $result->project_title }} ({{ $result->year }}) - {{ $result->allocation_type }}');
                warrantedAmountData.push({{ $result->warranted_amount }});
                allocationAmountData.push({{ $result->allocation_amount }});
                balanceAfterAllocationData.push({{ $result->balance_after_allocation }});
            @endforeach

            var chart = Highcharts.chart('projectAllocationsChart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Project Allocations with Balances'
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Projects, Years, and Allocation Types'
                    }
                },
                yAxis: [{
                    title: {
                        text: 'Amount (in currency)'
                    },
                    min: 0
                }],
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    id: 'warrantedAmountSeries',
                    type: 'column',
                    name: 'Warranted Amount',
                    data: warrantedAmountData
                }, {
                    id: 'allocationAmountSeries',
                    type: 'column',
                    name: 'Allocation Amount',
                    data: allocationAmountData
                }, {
                    id: 'balanceAfterAllocationSeries',
                    type: 'spline',
                    name: 'Balance After Allocation',
                    data: balanceAfterAllocationData,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }],
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: [
                                'viewFullscreen', 'printChart', 'separator', 'downloadPNG', 
                                'downloadJPEG', 'downloadPDF', 'downloadSVG',
                                {
                                    text: 'Toggle Warranted Amount',
                                    onclick: function () {
                                        var series = chart.get('warrantedAmountSeries');
                                        series.setVisible(!series.visible);
                                    }
                                },
                                {
                                    text: 'Toggle Allocation Amount',
                                    onclick: function () {
                                        var series = chart.get('allocationAmountSeries');
                                        series.setVisible(!series.visible);
                                    }
                                },
                                {
                                    text: 'Toggle Balance After Allocation',
                                    onclick: function () {
                                        var series = chart.get('balanceAfterAllocationSeries');
                                        series.setVisible(!series.visible);
                                    }
                                }
                            ]
                        }
                    }
                }
            });
        });
    </script>



@elseif ($report->id == 6)
    {{-- Render Highchart for Island Involvement in Project Activities --}}
    <div class="chart-container">
        <div id="islandInvolvementChart"></div>
    </div>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var categories = [];
            var seriesData = {};

            @foreach($results as $result)
                var cat = '{{ $result->project_title }} - {{ $result->activity_description }}';
                if (!categories.includes(cat)) {
                    categories.push(cat);
                }

                if (!seriesData['{{ $result->island_name }}']) {
                    seriesData['{{ $result->island_name }}'] = new Array(categories.length).fill(0);
                }

                var index = categories.indexOf(cat);
                seriesData['{{ $result->island_name }}'][index] = {{ $result->spent }};
            @endforeach

            // Ensure all series arrays are the correct length
            for (var island in seriesData) {
                while (seriesData[island].length < categories.length) {
                    seriesData[island].push(0);
                }
            }

            var series = [];
            for (var island in seriesData) {
                series.push({
                    name: island,
                    data: seriesData[island]
                });
            }

            // Log categories and series data for debugging
            console.log('Categories:', categories);
            console.log('Series:', series);

            if (categories.length > 0 && series.length > 0) {
                Highcharts.chart('islandInvolvementChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Island Involvement in Project Activities'
                    },
                    xAxis: {
                        categories: categories,
                        title: {
                            text: 'Projects and Activities'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Spent Amount (in currency)'
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: series,
                    exporting: {
                        buttons: {
                            contextButton: {
                                menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                            }
                        }
                    }
                });
            } else {
                console.error('No data available to render the chart.');
            }
        });
    </script> 
                @else
                    <p class="text-center">No chart available for this report.</p>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
</body>
</html>
@endsection
        <!-- Hidden field for updated_by -->
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Update</button>
    </form>
</div>
@endsection
