@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">NEW PROJECT</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Repository
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="provider">Provider</label>
                        <select form="form-project" name="provider" id="provider" class="form-control">
                            @foreach (\Fikrimi\Pipe\Enum\Repository::all() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input form="form-project" name="name" type="text" class="form-control" id="name" placeholder="My Awesome Project">
                    </div>
                    <div class="form-group">
                        <label for="namespace">Repository Namespace</label>
                        <input form="form-project" name="namespace" type="text" class="form-control" id="namespace" placeholder="myname/my-awesome-project">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Deploy Server
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="host">Host</label>
                        <input form="form-project" name="host" type="text" class="form-control" id="host" placeholder="172.X.X.X">
                    </div>
                    <div class="form-group">
                        <label for="dir_deploy">Deploy Directory</label>
                        <input form="form-project" name="dir_deploy" type="text" class="form-control" id="dir_deploy" placeholder="/var/www/html">
                    </div>
                    <div class="form-group">
                        <label for="dir_workspace">Workspace Directory</label>
                        <input form="form-project" name="dir_workspace" type="text" class="form-control" id="dir_workspace" placeholder="/var/www">
                    </div>
                    <div class="form-group">
                        <label for="credential_id">Credential Used</label>
                        <select form="form-project" name="credential_id" id="credential_id" class="form-control">
                            @foreach (\Fikrimi\Pipe\Models\Credential::all() as $credential)
                                <option value="{{ $credential->id }}">{{ $credential->username }} - {{ strtoupper($credential->fingerprint) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('pipe::projects.store') }}" method="post" id="form-project">
        @csrf
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
