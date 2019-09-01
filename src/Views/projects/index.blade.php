@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PROJECTS</h1>
        <a href="{{ route('pipe.projects.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-fw fa-sm text-white-50"></i> Add new projects</a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Provider</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach (\Fikrimi\Pipe\Models\Project::all() as $project)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Fikrimi\Pipe\Enum\Provider::$names[$project->provider] }}</td>
                            <td>{{ $project->name }}</td>
                            <td>
                                <button type="submit" form="form-delete" formaction="{{ route('pipe.projects.destroy', $project) }}" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <form method="post" id="form-delete">
        @csrf
        @method('delete')
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
