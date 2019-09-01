@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">NEW PROJECT</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('pipe.projects.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="provider">Provider</label>
                    <select name="provider" id="provider" class="form-control">
                        @foreach (\Fikrimi\Pipe\Enum\Provider::$names as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input name="name" type="text" class="form-control" id="name" placeholder="My Awesome Project">
                </div>
                <div class="form-group">
                    <label for="namespace">Project Namespace</label>
                    <input name="namespace" type="text" class="form-control" id="namespace" placeholder="myname/my-awesome-project">
                </div>
                <div class="form-group">
                    <label for="credential_id">Credential Used</label>
                    <select name="credential_id" id="credential_id" class="form-control">
                        @foreach (\Fikrimi\Pipe\Models\Credential::all() as $credential)
                            <option value="{{ $credential->id }}">{{ $credential->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
