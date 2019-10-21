<div class="card-body">
    <div class="row m-b-10">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6"><b>Provider</b></div>
                <div class="col-md-6">{{ \Fikrimi\Pipe\Enum\Repository::$names[$project->repository] }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6"><b>Namespace</b></div>
                <div class="col-md-6">{{ $project->namespace }}</div>
            </div>
        </div>
    </div>
    <div class="row m-b-10">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6"><b>Host</b></div>
                <div class="col-md-6">{{ $project->host }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6"><b>Deploy Dir</b></div>
                <div class="col-md-6">{{ $project->dir_deploy }}</div>
            </div>
        </div>
    </div>
    <div class="row m-b-10">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6"><b>Workspace Dir</b></div>
                <div class="col-md-6">{{ $project->dir_workspace }}</div>
            </div>
        </div>
    </div>
    <div class="row m-b-10">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3"><b>WebHook URL</b></div>
                <div class="col-md-9">{{ url('pipe/webhook/' . $project->id) }}</div>
            </div>
        </div>
    </div>
</div>
