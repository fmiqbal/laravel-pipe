<?php

namespace Fikrimi\Pipe\Controllers;

use App\Http\Controllers\Controller;
use Crypt;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Project;
use Illuminate\Http\Request;
use phpseclib\Crypt\RSA;

class CredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pipe::credentials.index');
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
        $key = new RSA();
        $key->loadKey($request->auth);

        $credential
            ->fill([
                'username'    => $request->get('username'),
                'type'        => $request->get('type'),
                'auth'        => Crypt::encrypt($request->get('auth')),
                'fingerprint' => $key->getPublicKeyFingerprint('sha256'),
            ])
            ->save();

        return redirect()->route('pipe.credentials.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
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
     * @param \Fikrimi\Pipe\Models\Credential $credential
     * @return void
     * @throws \Exception
     */
    public function destroy(Credential $credential)
    {
        $credential->delete();

        return redirect()->back();
    }
}
