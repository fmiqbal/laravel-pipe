<?php

namespace Fikrimi\Pipe\Http\Controllers;

use App\Http\Controllers\Controller;
use Fikrimi\Pipe\Models\Project;
use Illuminate\Http\Request;
use Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pipe::projects.index');
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
            ->fill([
                'provider'      => $request->get('provider'),
                'name'          => $request->get('name'),
                'namespace'     => $request->get('namespace'),
                'dir_deploy'    => $request->get('dir_deploy'),
                'dir_workspace' => $request->get('dir_workspace'),
                'host'          => $request->get('host'),
                'credential_id' => $request->get('credential_id'),
                'id'            => Str::orderedUuid(),
            ])
            ->save();

        return redirect()->route('pipe.projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
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
        $project->delete();

        return redirect()->back();
    }
}
