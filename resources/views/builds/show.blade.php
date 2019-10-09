@extends('pipe::app')

@section('content')
    <input type="hidden" name="build-id" value="{{ $build->id }}">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PROJECT {{ strtoupper($project->name) }}</h1>
    </div>
    <div class="row">
        <div class="col-sm-12 m-b-30">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Build Details
                    </h6>
                </div>
                @include('pipe::partials.project_header')
            </div>
        </div>
        <div class="col-md-6">
            @if ($build->status === \Fikrimi\Pipe\Models\Build::S_FAILED)
                <div class="card shadow mb-4" style="border-right: 5px solid #e74a3b">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Errors</h6>
                    </div>
                    <div class="card-body">
                        {{--<ul class="list-group">--}}
                            @foreach ($build->steps()->where('exit_status', '<>', 0)->get() as $step)
                                @continue($step->output === null)
                                {{--<li class="list-group-item">--}}
                                    {{ $step->command }}
                                    <br>
                                    {{ $step->output }}
                                    <hr>
                                {{--</li>--}}
                            @endforeach
                        {{--</ul>--}}
                    </div>
                </div>
            @endif
            @foreach ($stepGroups as $group => $steps)
                @continue((explode('-', $group)[0] ?? '') === 'pipe')

                <div class="card shadow mb-4" style="border-right: 5px solid {{ $steps->every('exit_status', 0) ? '#1cc88a' : '#e74a3b' }}">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $group }}</h6>
                    </div>
                    <div class="card-body">
                        {{--<ul class="list-group">--}}
                            @foreach ($steps as $step)
                                {{--<li class="list-group-item">--}}
                                    <div class="row">
                                        <div class="col-sm-10">
                                            {{ $step->command }}
                                        </div>
                                        <div class="col-sm-2 text-right">
                                            @if ($step->exit_status === null)
                                                <i class="fas fa-spin fa-circle-notch text-muted"></i>
                                            @elseif ($step->exit_status === 0)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                {{--</li>--}}
                            <hr>
                            @endforeach
                        {{--</ul>--}}
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
