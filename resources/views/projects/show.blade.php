@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <form method="post" action="{{ route('pipe::build', $project) }}" id="form-build">
        @csrf
    </form>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <small>project</small> <b>{{ strtoupper($project->name) }}</b>
        </h1>
        <button form="form-build" type="submit" class="btn btn-primary btn-sm float-right">Build and Deploy</button>
    </div>
    <div class="row">
        <div class="col-sm-12 m-b-30">
            <div class="card shadow">
                <a href="#project-header" class="card-header py-3" data-toggle="collapse">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Project Details
                    </h6>
                </a>
                <div class="collapse" id="project-header">
                    @include('pipe::partials.project_header')
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Build History</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Invoker</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($project->builds()->latest()->get() as $build)
                            @php
                                $build->checkTimeOut();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $build->created_at  }}</td>
                                <td>
                                    {{ $build->duration->format('%H:%I:%S') }}
                                </td>
                                <td>{{ $build->invoker }}</td>
                                <td>{{ $build->branch }}</td>
                                <td>
                                    <span class="badge
                                        @if ($build->status === \Fikrimi\Pipe\Models\Build::S_SUCCESS)
                                            badge-success
                                        @elseif ($build->status === \Fikrimi\Pipe\Models\Build::S_FAILED || $build->status === \Fikrimi\Pipe\Models\Build::S_TERMINATED)
                                            badge-danger
                                        @else
                                            badge-light
                                        @endif
                                            ">
                                        {{ ucwords($build->status_name) }}
                                    </span>
                                    @if ($build->id === $project->current_build)
                                        <span class="badge badge-primary">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pipe::builds.show', $build) }}" class="btn btn-primary btn-sm">
                                        Details
                                    </a>

                                    <button data-toggle="tooltip" title="Switch to this version" type="submit" form="form-switch" class="btn btn-primary btn-sm" formaction="{{ route('pipe::builds.switch', $build) }}">
                                        <i class="fas fa-sync"></i>
                                    </button>

                                    <button type="submit" form="form-destroy" class="btn btn-danger btn-sm" formaction="{{ route('pipe::builds.destroy', $build) }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <form id="form-destroy" method="post">
        @method('delete')
        @csrf
    </form>
    <form id="form-switch" method="post">
        @csrf
    </form>
@endsection

@push('js')
    <!-- Page level plugins -->
    <script src="{{ asset('pipe-assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endpush
