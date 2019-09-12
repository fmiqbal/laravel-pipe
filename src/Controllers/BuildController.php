<?php

namespace Fikrimi\Pipe\Controllers;

use App\Http\Controllers\Controller;
use Fikrimi\Pipe\Models\Project;

class BuildController extends Controller
{
    public function build(Project $project)
    {
        $cred = $project->credential;
        \Cache::delete('output');
        \SSH::connect([
            'host'     => $project->host,
            'username' => $cred->username,
            'keytext'  => \Crypt::decrypt($cred->auth),
        ])
            ->run([
                'cd ' . $project->dir_deploy,
                'echo $?',
                'git pull',
                'echo $?',
            ], function ($line) {
                $output = \Cache::get('output') ?: '';

                $output .= "\n" . $line;
                \Cache::put('output', $output);
            });
    }
}
