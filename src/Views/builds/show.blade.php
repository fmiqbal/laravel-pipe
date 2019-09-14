@extends('pipe::app')

@section('content')
    @if ($build->status === \Fikrimi\Pipe\Models\Build::S_FAILED)
        <div class="alert alert-danger">Last Line : {{ $build->meta['last_line'] ?? '' }}</div>
    @endif
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PROJECT {{ strtoupper($project->name) }}</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4" style="border-right: 5px solid #1cc88a">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Executing Code
                        <i class="fas fa-check-circle float-right text-success"></i>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($build->meta_steps as $step)
                            <li class="list-group-item">{{ $step[0] }}
                                @if ($step[1] == 0)
                                    <i class="fa fa-check-circle float-right text-success"></i>
                                @else
                                    <i class="fa fa-times-circle float-right text-danger"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
