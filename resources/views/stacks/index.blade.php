@extends('pipe::app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            STACKS
        </h1>
        <a href="{{ route('pipe::stacks.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-fw fa-sm text-white-50"></i> Add new stacks
        </a>
    </div>
    <div class="row">
        <form method="POST" id="form-delete">
            @csrf
            @method('delete')
        </form>
        @foreach (\Fikrimi\Pipe\Models\Stack::all() as $stack)
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ $stack->name }}

                            <div class="float-right">
                                {{--                                <a href="{{ route('pipe::stacks.edit') }}"><i class="fas fa-fw fa-edit"></i></a>--}}
                                {{--<i class="fas text-danger fa-fw fa-trash"></i>--}}
                                <button style="padding: 0; margin: 0; background: none; border: none" type="submit" form="form-delete" formaction="{{ route('pipe::stacks.destroy', $stack) }}"><i class="fas text-danger fa-fw fa-trash"></i></button>
                            </div>
                        </h6>
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
