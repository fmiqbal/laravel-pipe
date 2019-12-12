<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Fikrimi\Pipe\Models\Credential;
use Illuminate\Http\Request;

class CredentialController extends BaseController
{
    use HasPolicy;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $credentials = Credential::query();

        $this->checkModelCreator('view_other', $credentials);

        return view('pipe::credentials.index')->with([
            'credentials' => $credentials->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pipe::credentials.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Fikrimi\Pipe\Models\Credential $credential
     * @return void
     */
    public function store(Request $request, Credential $credential)
    {
        $credential->fill($request->all())
            ->save();

        return redirect()->route('pipe::credentials.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Fikrimi\Pipe\Models\Credential $credential
     * @return void
     * @throws \Exception
     */
    public function destroy(Credential $credential)
    {
        $this->authorize('delete', $credential);

        $credential->delete();

        return redirect()->route('pipe::credentials.index');
    }
}
