@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-light" style="border-radius: 20px;">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00c6ff); border-radius: 20px 20px 0 0;">
                    <h3 class="mb-0">{{ __('Office of the Attorney General Dashboard') }}</h3>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="card-text">
                        Welcome to the Office of the Attorney General Dashboard. This platform enables efficient management of legal and administrative functions. Use the tools provided to access case management, generate reports, and utilize administrative resources.
                    </p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100" style="border: 2px solid #007bff; border-radius: 15px;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Upcoming Legal Matters</h5>
                                        <p class="card-text">Keep track of upcoming legal matters and deadlines. Stay informed about ongoing proceedings and manage your case schedules effectively.</p>
                                    </div>
                                    <a href="" class="btn btn-primary" style="border-radius: 30px;">View Legal Matters</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100" style="border: 2px solid #007bff; border-radius: 15px;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Legal Reports</h5>
                                        <p class="card-text">Generate and review detailed reports on legal cases, personnel, and other critical data.</p>
                                    </div>
                                    <a href="" class="btn btn-primary" style="border-radius: 30px;">Generate Reports</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100" style="border: 2px solid #007bff; border-radius: 15px;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Administrative Management</h5>
                                        <p class="card-text">Access tools for managing user accounts, system settings, and configurations to ensure optimal functionality.</p>
                                    </div>
                                    <a href="" class="btn btn-primary" style="border-radius: 30px;">Access Admin Tools</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection