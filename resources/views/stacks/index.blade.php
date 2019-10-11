@extends('pipe::app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">STACKS</h1>
    </div>
    <div class="row">
        @foreach (\Fikrimi\Pipe\Models\Stack::all() as $stack)
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $stack->name }}</h6>
                    </div>
                    <div class="card-body" style="overflow-x: scroll">
                        <small>{{ $stack->description }}</small>
                        <hr>
                        <table>
                            <tr>
                                <td style="border-right: 1px solid black; padding-right: 10px">
                                    <code>
                                        @foreach ($stack->commands as $command)
                                            {{ $loop->iteration }}
                                            <br>
                                        @endforeach
                                    </code>
                                </td>
                                <td style="padding-left: 10px">
                                    <code style="overflow-x: scroll; white-space: nowrap;">
                                        @foreach ($stack->commands as $command)
                                            {{ $command }}
                                            <br>
                                        @endforeach
                                    </code>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
