<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Models\Stack;
use Illuminate\Http\Request;

class StackController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pipe::stacks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pipe::stacks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Fikrimi\Pipe\Models\Stack $stack
     * @return void
     */
    public function store(Request $request, Stack $stack)
    {
        $stack->fill($request->toArray())
            ->fill([
                'commands' => explode(PHP_EOL, $request->get('commands')),
            ])
            ->save();

        return redirect()->route('pipe::stacks.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Fikrimi\Pipe\Models\Stack $stack
     * @return \Illuminate\Http\Response
     */
    public function edit(Stack $stack)
    {
        return view('pipe::stacks.edit')->with([
            'stack' => $stack
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Fikrimi\Pipe\Models\Stack $stack
     * @return void
     */
    public function update(Request $request, Stack $stack)
    {
        $stack->fill($request->toArray())
            ->fill([
                'commands' => explode(PHP_EOL, $request->get('commands')),
            ])
            ->save();

        return redirect()->route('pipe::stacks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Fikrimi\Pipe\Models\Stack $stack
     * @return void
     * @throws \Exception
     */
    public function destroy(Stack $stack)
    {
        $stack->delete();

        return redirect()->route('pipe::stacks.index');
    }
}
