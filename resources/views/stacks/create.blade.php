@extends('pipe::app')

@push('css')
    <link href="{{ asset('pipe-assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">NEW STACKS</h1>
    </div>
    <div class="card shadow mb-4 col-md-6">
        <div class="card-body">
            <form action="{{ route('pipe::stacks.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input name="name" type="text" class="form-control" id="name" placeholder="Some catchy name">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input name="description" type="text" class="form-control" id="description" placeholder="And its description">
                </div>
                <div class="form-group">
                    <label for="commands">Commands</label>
                    <textarea name="commands" id="commands" cols="30" rows="10" class="form-control" placeholder="./vendor/bin/phpunit"></textarea>
                    <small id="command-help" class="form-text text-muted">Use newline to add new commands. As long as this raw command can be executed on your server, its safe to add it.</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
