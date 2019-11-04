@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">NEW CREDENTIAL</h1>
    </div>
    <div class="card shadow mb-4 col-md-6">
        <div class="card-body">
            <form action="{{ route('pipe::credentials.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input name="username" type="text" class="form-control" id="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="form-control">
                        @foreach (\Fikrimi\Pipe\Models\Credential::$typeNames as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="auth">Auth</label>
                    <textarea name="auth" id="auth" cols="30" rows="10" class="form-control" placeholder="Public Key / Password"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
