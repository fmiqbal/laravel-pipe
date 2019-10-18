@if (config('pipe.modules.auth'))
    <td>
        @if ($model->creator !== null)
            {{ $model->creator->{config('pipe.auth.human_identifier')} }}
        @else
            -
        @endif
    </td>
@endif
