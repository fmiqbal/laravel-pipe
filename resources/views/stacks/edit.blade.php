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
            <form action="{{ route('pipe::stacks.update', $stack) }}" method="post">
                @csrf
                @method('patch')
                @include('pipe::stacks.form', [
                    'stack' => $stack
                ])
            </form>
        </div>
    </div>
@endsection
