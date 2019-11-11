@extends('pipe::app')

@push('css')
    <style>
        .card-commands {
            overflow-x: hidden;
        }

        .card-commands:hover {
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            STACKS
        </h1>
        <a href="{{ route('pipe::stacks.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-fw fa-sm text-white-50"></i> Add new stacks
        </a>
    </div>
    <p>
        <a class="btn btn-light text-gray-800" data-toggle="collapse" href="#collapse-default" role="button" aria-expanded="false" aria-controls="collapse-default"><i class="fas fa-fw fa-caret-down"></i> Show default</a>
    </p>
    <div class="collapse multi-collapse" id="collapse-default">
        <div class="row">
            @foreach (\Fikrimi\Pipe\Stack::all() as $stack)
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-danger">
                                {{ $stack->name }}

                                <div class="float-right">
                                    <form action="{{ route('pipe::stacks.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $stack->name }}">
                                        <input type="hidden" name="description" value="{{ $stack->description }}">
                                        <input type="hidden" name="commands" value="{{ implode(PHP_EOL, $stack->commands) }}">
                                        <button style="padding: 0; margin: 0; background: none; border: none" type="submit">
                                            <i class="far text-{{ $color ?? 'primary' }} fa-fw fa-clone"></i></button>
                                    </form>
                                </div>
                            </h6>
                        </div>
                        @include('pipe::stacks.commands')
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <hr style="border: 1px dashed grey">
    <div class="row">
        <form method="POST" id="form-delete">
            @csrf
            @method('delete')
        </form>
        <form method="POST" id="form-duplicate">
            @csrf
        </form>
        @foreach (\Fikrimi\Pipe\Models\Stack::all() as $stack)
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-{{ $bgColor ?? '' }}">
                        <h6 class="m-0 font-weight-bold text-{{ $color ?? 'primary' }}">
                            {{ $stack->name }}

                            <div class="float-right">
                                <button style="padding: 0; margin: 0; background: none; border: none" type="submit" form="form-duplicate" formaction="{{ route('pipe::stacks.duplicate', $stack) }}">
                                    <i class="far text-{{ $color ?? 'primary' }} fa-fw fa-clone"></i></button>
                                <a href="{{ route('pipe::stacks.edit', $stack) }}"><i class="fas text-{{ $color ?? 'primary' }} fa-fw fa-edit"></i></a>
                                <button style="padding: 0; margin: 0; background: none; border: none" type="submit" form="form-delete" formaction="{{ route('pipe::stacks.destroy', $stack) }}">
                                    <i class="fas text-danger fa-fw fa-trash"></i></button>
                            </div>
                        </h6>
                    </div>
                    @include('pipe::stacks.commands')
                </div>
            </div>
        @endforeach
    </div>
@endsection
