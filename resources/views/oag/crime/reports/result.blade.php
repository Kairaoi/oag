@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-5 custom-title">Executive Report</h1>

    @if(!empty($results) && (is_array($results) ? count($results) > 0 : $results->count() > 0))
        <div id="reportResults" class="report-container mb-5">

            <!-- Chart Container -->
            <div id="chartContainer" class="mb-5" style="min-height: 400px;"></div>

            <!-- Table Export Area -->
            <div id="reportCaptureArea" class="table-responsive table-container">
                <table id="reportTable" class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            @php
                                $firstRow = is_array($results) ? (object) reset($results) : $results->first();
                                $headers = array_keys((array) $firstRow);
                            @endphp
                            @foreach($headers as $header)
                                <th class="custom-header">
                                    {{ ucfirst(str_replace('_', ' ', $header)) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            @php $row = (array) (is_object($result) ? $result : (object) $result); @endphp
                            <tr>
                                @foreach($row as $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Export Buttons -->
            <div class="text-center mt-4">
                <button class="btn btn-custom" onclick="downloadAsPNG()">Download PNG</button>
                <button class="btn btn-custom" onclick="downloadAsJPEG()">Download JPEG</button>
                <button class="btn btn-custom" onclick="downloadAsSVG()">Download SVG</button>
            </div>

            @if(!is_array($results))
                <div class="pagination-container mt-4">
                    {{ $results->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @else
        <p class="text-center text-muted">No results found.</p>
    @endif
</div>
@endsection

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Your existing styles unchanged */
    .custom-title {
        font-size: 3rem;
        font-weight: 700;
        color: #003366;
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
        border-bottom: 4px solid #004080;
        padding-bottom: 15px;
    }

    .table-container {
        padding: 30px;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border: 2px solid #004080;
    }

    .table {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        animation: fadeIn 1s ease;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        padding: 14px;
    }

    .thead-dark th {
        background-color: #004080;
        color: #fff;
        border-bottom: 2px solid #002b5c;
    }

    .custom-header {
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-custom {
        background-color: #004080;
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 8px 14px;
        margin: 5px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #002b5c;
    }

    .pagination-container .pagination {
        justify-content: center;
    }

    @keyframes fadeIn {
        0%   { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
        .custom-title {
            font-size: 2.2rem;
        }

        .table th, .table td {
            font-size: 0.9rem;
            padding: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Highcharts and modules -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/offline-exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<!-- html2canvas and dom-to-image-more -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.9.0/dist/dom-to-image-more.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#reportTable').DataTable({
            paging: true,
            searching: true,
            pagingType: "full_numbers",
            responsive: true
        });

        $('#reportResults').hide().slideDown(800);

        // Prepare data for chart from PHP $results
        // Assume $results have 'category' and 'value' columns
        const categories = @json(collect($results)->pluck('category'));
        const dataValues = @json(collect($results)->pluck('value').map(v => Number(v)));

        Highcharts.chart('chartContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Summary Chart'
            },
            accessibility: {
                enabled: true
            },
            exporting: {
                fallbackToExportServer: false,
                enabled: true,
                buttons: {
                    contextButton: {
                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadSVG', 'downloadPDF']
                    }
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Category'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Value'
                }
            },
            series: [{
                name: 'Values',
                data: dataValues
            }]
        });
    });

    function downloadAsPNG() {
        const node = document.getElementById('reportCaptureArea');
        html2canvas(node, {
            scale: 2,
            useCORS: true,
            backgroundColor: "#fff"
        }).then(canvas => {
            canvas.toBlob(blob => {
                if (blob) {
                    const link = document.createElement('a');
                    link.download = 'executive_report.png';
                    link.href = URL.createObjectURL(blob);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    URL.revokeObjectURL(link.href);
                } else {
                    alert('Failed to generate PNG file.');
                }
            });
        }).catch(err => {
            console.error('PNG export error:', err);
            alert('Error generating PNG export.');
        });
    }

    function downloadAsJPEG() {
        const node = document.getElementById('reportCaptureArea');
        html2canvas(node, {
            scale: 2,
            useCORS: true,
            backgroundColor: "#fff"
        }).then(canvas => {
            canvas.toBlob(blob => {
                if (blob) {
                    const link = document.createElement('a');
                    link.download = 'executive_report.jpeg';
                    link.href = URL.createObjectURL(blob);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    URL.revokeObjectURL(link.href);
                } else {
                    alert('Failed to generate JPEG file.');
                }
            }, 'image/jpeg', 1.0);
        }).catch(err => {
            console.error('JPEG export error:', err);
            alert('Error generating JPEG export.');
        });
    }

    function downloadAsSVG() {
        const node = document.getElementById('reportCaptureArea');
        domtoimage.toSvg(node)
            .then(dataUrl => {
                const link = document.createElement('a');
                link.download = 'executive_report.svg';
                link.href = dataUrl;
                document.body.appendChild(link);
                link.click();
                link.remove();
            })
            .catch(error => {
                console.error('SVG export failed:', error);
                alert('Error generating SVG export.');
            });
    }
</script>
@endpush
