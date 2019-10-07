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
        <div class="col-md-6">
            @foreach ($stepGroups as $group => $steps)
                @continue((explode('-', $group)[0] ?? '') === 'pipe')

                <div class="card shadow mb-4" style="border-right: 5px solid {{ $steps->every('exit_status', 0) ? '#1cc88a' : '#e74a3b' }}">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $group }}</h6>
                    </div>
                <div class="card-body">
                    <ul class="list-group">
                            @foreach ($steps as $step)
                                <li class="list-group-item">{{ $step->command }}
                                    @if ($step->exit_status === null)
                                        <i class="fas fa-spin fa-circle-notch float-right text-muted"></i>
                                    @elseif ($step->exit_status === 0)
                                        <i class="fas fa-check-circle float-right text-success"></i>
                                @else
                                        <i class="fas fa-times-circle float-right text-danger"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @endforeach
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Output </h6>
                </div>
                <div class="card-body">
                    <span id="terminal-output">
                        <div {{ $iterate = 1 }}></div>
                        @foreach ($build->steps as $step)
                            @continue(empty($step->output))
                            @continue((explode('-', $step->group)[0] ?? '') === 'pipe')

                            Executing : {{ $step->command }}
                            <br>
                            {{ $iterate++ }} | {{ $step->output }}
                            <br>
                            <br>
                        @endforeach
                    </span>

                    @if (! in_array($build->status, \Fikrimi\Pipe\Models\Build::getFinishStatuses()))
                        <i id="output-spinner" class="fas fa-lg fa-circle-notch fa-spin m-t-20"></i>
                    @else
                        Deploy has been completed with status: {{ $build->status_name }}
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
