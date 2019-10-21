<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Fikrimi\Pipe\Http\Controllers\Traits\HasPolicy;
use Fikrimi\Pipe\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends BaseController
{
    use HasPolicy;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::query();

        $this->checkModelCreator('view_other', $projects);

        return view('pipe::projects.index')->with([
            'projects' => $projects->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pipe::projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return void
     */
    public function store(Request $request, Project $project)
    {
        $project
            ->fill($request->toArray())
            ->save();

        return redirect()->route('pipe.projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return view('pipe::projects.show')->with([
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('pipe.projects.index');
    }
}
