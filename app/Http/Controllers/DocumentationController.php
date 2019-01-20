<?php

namespace App\Http\Controllers;

use App\Documentation;
use App\Documentation\Deployment\DeployNetlify;
use App\Documentation\Generators\OnlineGenerator;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class DocumentationController extends Controller
{
    public function store(Request $request)
    {
        $content = $request->getContent();

        $docs = Documentation::create([
            'uuid'   => Uuid::uuid4()->toString(),
            'config' => trim($content),
        ]);

        $generator = new OnlineGenerator($docs);
        $html = $generator->generateHTML();

        $docs->html = $html;
        $docs->save();

        return response('OK', 201);
    }

    public function show(string $uuid)
    {
        $docs = Documentation::where('uuid', $uuid)->firstOrFail();

        return $docs->toArray()['config'];
    }

    public function deploy(string $uuid)
    {
        $docs = Documentation::where('uuid', $uuid)->firstOrFail();

        $deploy = new DeployNetlify($docs);
        $output = $deploy->deploy();

        return $output;
    }


}
