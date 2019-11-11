<div class="card-body card-commands">
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
                <code style="white-space: nowrap;">
                    @foreach ($stack->commands as $command)
                        {{ $command }}
                        <br>
                    @endforeach
                </code>
            </td>
        </tr>
    </table>
</div>
