<?php

namespace Fikrimi\Pipe\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Fikrimi\Pipe\Exceptions\ApplicationException;
use Fikrimi\Pipe\Jobs\ExecutePipeline;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Project;
use Illuminate\Http\Request;
use Str;

class BuildController extends Controller
{
    /**
     * @param \Fikrimi\Pipe\Models\Project $project
     * @param \Fikrimi\Pipe\Models\Build $build
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     */
    public function build(Request $request, Project $project, Build $build)
    {
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

            ExecutePipeline::dispatch($build);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            throw new ApplicationException($e);
        }

        return back();
    }

    public function show(Build $build)
    {
        $stepGroups = $build->steps->groupBy('group');
        $project = Project::make($build->meta_project);

        return view('pipe::builds.show')->with([
            'project' => $project,
            'build'   => $build,
            'stepGroups'   => $stepGroups,
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
