<?php

namespace Fikrimi\Pipe\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Fikrimi\Pipe\Exceptions\ApplicationException;
use Fikrimi\Pipe\Jobs\Deploy;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Project;
use Str;

class BuildController extends Controller
{
    /**
     * @param \Fikrimi\Pipe\Models\Project $project
     * @param \Fikrimi\Pipe\Models\Build $build
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     */
    public function build(Project $project, Build $build)
    {
        if ($project->builds()->whereNotIn('status', Build::getFinishStatuses())->exists()) {
            return back()->withErrors('Masih terdapat build yang belum selesai');
        }

        try {
            \DB::beginTransaction();
            $project->load('credential');
            $build->fill([
                'id'           => Str::orderedUuid(),
                'status'       => Build::S_PROVISIONING,
                'meta'         => json_decode('{}'),
                'meta_steps'   => json_decode('{}'),
                'meta_project' => $project->toArray(),
                'invoker'      => 'manual',
            ]);

            $project->builds()->save($build);

            Deploy::dispatch($build);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            throw new ApplicationException($e);
        }

        return back();
    }

    public function show(Project $project, Build $build)
    {
        return view('pipe::builds.show')->with([
            'project' => $project,
            'build'   => $build,
        ]);
    }

    public function destroy(Project $project, Build $build)
    {
        if (in_array($build->status, Build::getFinishStatuses())) {
            return back();
        }

        $build->update([
            'status' => Build::S_TERMINATED,
        ]);

        return back();
    }
}
