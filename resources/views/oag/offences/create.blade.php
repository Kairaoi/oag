@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.offence.index') }}">Offences</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Offence</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create New Offence</h1>

    <!-- Form -->
    <form action="{{ route('crime.offence.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        <div class="form-group">
            <label for="offence_name" class="text-white">Offence Name</label>
            <input type="text" class="form-control @error('offence_name') is-invalid @enderror" id="offence_name" name="offence_name" value="{{ old('offence_name') }}" required>
            @error('offence_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="offence_category_id" class="text-white">Offence Category</label>
            <select class="form-control @error('offence_category_id') is-invalid @enderror" id="offence_category_id" name="offence_category_id" required>
                <option value="">Select a category</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('offence_category_id') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('offence_category_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="button" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold; background-color: #ffffff; color: #ff416c;" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add New Category</button>
        <button type="submit" class="btn btn-light btn-lg btn-block mt-2" style="border-radius: 30px; font-weight: bold;">Create</button>
    </form>
</div>

<!-- Modal for adding a new category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5);">
            <div class="modal-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Offence Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: #f8f9fa;">
                <form id="addCategoryForm">
                    @csrf
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2" style="border-radius: 20px; background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border: none;">Add Category</button>
                    <div id="categoryFeedback" class="mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#addCategoryForm').on('submit', function(event) {
        event.preventDefault();

        var categoryName = $('#category_name').val();
        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: "{{ route('crime.category.store') }}",
            method: 'POST',
            data: {
                _token: token,
                category_name: categoryName
            },
            success: function(response) {
                if (response.success) {
                    $('#offence_category_id').append(
                        $('<option>', {
                            value: response.id,
                            text: categoryName,
                            selected: true
                        })
                    );
                    $('#addCategoryModal').modal('hide');
                    $('#category_name').val('');
                    $('#categoryFeedback').text('Category added successfully!');
                } else {
                    $('#categoryFeedback').text(response.message);
                }
            },
            error: function(xhr) {
                $('#categoryFeedback').text('An error occurred. Please try again.');
            }
        });
    });
});
</script>
@endsection
