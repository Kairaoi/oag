@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Court of Appeal</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Court of Appeal Cases
    </h2>

    <!-- DataTable -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="court-appeal-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Appeal Case No.</th>
                    <th>Filing Date</th>
                    <th>Filing Source</th>
                    <th>Judgment Date</th>
                    <th>Outcome</th>
                    <th>Principle Established</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appeals as $appeal)
                    <tr>
                        <td>{{ $appeal->id }}</td>
                        <td>{{ $appeal->appeal_case_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('d M Y') }}</td>
                        <td>{{ ucfirst($appeal->filing_date_source) }}</td>
                        <td>{{ \Carbon\Carbon::parse($appeal->judgment_delivered_date)->format('d M Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($appeal->court_outcome) }}">
                                {{ ucfirst($appeal->court_outcome) }}
                            </span>
                        </td>
                        <td>{{ $appeal->decision_principle_established }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $appeal->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $appeal->id }}">
                                    <li><a class="dropdown-item" href="{{ route('crime.courtOfAppeal.edit', $appeal->id) }}">Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route('crime.courtOfAppeal.show', $appeal->id) }}">Show</a></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Delete this record?')) document.getElementById('delete-form-{{ $appeal->id }}').submit();">Delete</a>
                                        <form id="delete-form-{{ $appeal->id }}" action="{{ route('crime.courtOfAppeal.destroy', $appeal->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#court-appeal-table').DataTable();
    });
</script>
@endpush
