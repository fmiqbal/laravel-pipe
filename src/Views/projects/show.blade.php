@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <form method="post" action="{{ route('pipe.projects.builds.build', $project) }}" id="form-build">
        @csrf
    </form>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            PROJECT {{ strtoupper($project->name) }}
        </h1>
        <button form="form-build" type="submit" class="btn btn-primary btn-sm float-right">Build and Deploy</button>
    </div>
    <div class="row">
        <div class="col-sm-12 m-b-30">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row m-b-10">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6"><b>Provider</b></div>
                                <div class="col-md-6">{{ \Fikrimi\Pipe\Enum\Provider::$names[$project->provider] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6"><b>Namespace</b></div>
                                <div class="col-md-6">{{ $project->namespace }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6"><b>Host</b></div>
                                <div class="col-md-6">{{ $project->host }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6"><b>Deploy Dir</b></div>
                                <div class="col-md-6">{{ $project->dir_deploy }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6"><b>Workspace Dir</b></div>
                                <div class="col-md-6">{{ $project->dir_workspace }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3"><b>WebHook URL</b></div>
                                <div class="col-md-9">{{ url('pipe/webhook/' . $project->id) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">History</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Invoker</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($project->builds as $build)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $build->created_at  }}</td>
                                <td>{{ $build->invoker }}</td>
                                <td>{{ $build->status }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
