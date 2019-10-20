<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pipe::index');
    }
}
