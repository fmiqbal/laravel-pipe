@extends('pipe::app')

@section('content')
    <input type="hidden" name="build-id" value="{{ $build->id }}">
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
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Terminal Output                    </h6>
                </div>
                <div class="card-body">
                    <span id="terminal-output">
                        @foreach ($build->meta['lines'] ?? [] as $line)
                            {{ $line }} <br>
                        @endforeach
                    </span>

                    @if (! in_array($build->status, \Fikrimi\Pipe\Models\Build::getFinishStatuses()))
                        <i id="output-spinner" class="fas fa-lg fa-circle-notch fa-spin m-t-20"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        var pusher = new Pusher('0d289eb62a8539cda514', {
            cluster: 'ap1',
            forceTLS: true
        });

        var buildId = $('input[name=build-id]').val();

        var channel = pusher.subscribe('terminal-' + buildId);

        channel.bind('output', function (data) {
            $('#terminal-output').append(data.line + "<br>");
        });
        channel.bind('finished', function (data) {
            $('#output-spinner').remove();
        });
    </script>
@endpush
