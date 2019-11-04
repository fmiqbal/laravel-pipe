<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Exception;
use Fikrimi\Pipe\Exceptions\ApplicationException;
use Fikrimi\Pipe\Http\Controllers\Traits\HasPolicy;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BuildController extends BaseController
{
    use HasPolicy;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function build(Request $request, Project $project)
    {
        $this->authorize('build', $project);

        try {
            $project->release($request->wantsJson() ? 'webhook' : 'manual');
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApplicationException($e);
        }

        return redirect()->route('pipe::projects.show', $project);
    }

    public function show(Build $build)
    {
        $stepGroups = $build->steps->groupBy('group');
        $project = Project::make($build->meta_project);

        return view('pipe::builds.show')->with([
            'project'    => $project,
            'build'      => $build,
            'stepGroups' => $stepGroups,
        ]);
    }

    public function destroy(Project $project, Build $build)
    {
        if (in_array($build->status, Build::getFinishStatuses())) {
            return back();
        }

        Cache::put(
            $build->getCacheKey('status'),
            Build::S_PENDING_TERM,
            300
        );

        $build->update([
            'status' => Build::S_PENDING_TERM,
        ]);

        return back();
    }
}
