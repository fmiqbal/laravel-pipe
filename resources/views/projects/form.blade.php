<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Repository
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="repository">Provider</label>
                    <select required form="form-project" name="repository" id="repository" class="form-control">
                        <option value="" disabled {{ $project->id ? '' : 'selected' }}>Select provider</option>
                        @foreach (\Fikrimi\Pipe\Enum\Repository::$names as $id => $name)
                            <option {{ fill_select('repository', $id, $project) }} value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input required form="form-project" name="name" type="text" class="form-control" id="name" placeholder="My Awesome Project" value="{{ fill('name', $project) }}">
                </div>
                <div class="form-group">
                    <label for="namespace">Repository Namespace</label>
                    <input required form="form-project" name="namespace" type="text" class="form-control" id="namespace" placeholder="myname/my-awesome-project" value="{{ fill('namespace', $project) }}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Deploy Server
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="host">Host</label>
                    <input required form="form-project" name="host" type="text" class="form-control" id="host" placeholder="172.X.X.X" value="{{ fill('host', $project) }}">
                </div>
                <div class="form-group">
                    <label for="dir_deploy">Deploy Directory</label>
                    <input required form="form-project" name="dir_deploy" type="text" class="form-control" id="dir_deploy" placeholder="/var/www/html" value="{{ fill('dir_deploy', $project) }}">
                </div>
                <div class="form-group">
                    <label for="dir_workspace">Workspace Directory</label>
                    <input required form="form-project" name="dir_workspace" type="text" class="form-control" id="dir_workspace" placeholder="/var/www" value="{{ fill('dir_workspace', $project) }}">
                </div>
                <div class="form-group">
                    <label for="credential_id">Credential Used</label>
                    <select  required form="form-project" name="credential_id" id="credential_id" class="form-control">
                        @foreach (\Fikrimi\Pipe\Models\Credential::all() as $credential)
                            <option {{ fill_select('credential_id', $credential->id, $project) }} value="{{ $credential->id }}">{{ $credential->username }} - {{ strtoupper($credential->fingerprint) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Commands
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="stack">Stacks</label>
                    <select  required name="stack" id="stack" class="form-control" data-api-url="{{ route('pipe::stacks.show', ':id') }}">
                        <option value="" disabled selected>Select stack</option>
                        @foreach (\Fikrimi\Pipe\Models\Stack::all() as $stack)
                            <option value="{{ $stack->id }}">{{ $stack->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="namespace">Commands</label>
                    <textarea form="form-project" class="form-control" name="commands" id="stack-commands" cols="30" rows="10">{{ implode(PHP_EOL, optional($project ?? [])->commands ?: []) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('select#stack').on('change', function () {
                let url = $(this).data('api-url').replace(':id', $(this).val());

                $.get({
                    'headers': {
                        'accept': 'application/json'
                    },
                    'url': url,
                })
                    .done(function (data) {
                        $('#stack-commands')
                            .val('')
                            .val(data.commands.join("\n"));
                    })
            })
        })
    </script>
@endpush
