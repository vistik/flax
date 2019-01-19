<?php

namespace App\Http\Controllers;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateDocsController extends Controller
{
    public function generate(Request $request)
    {
        $content = $request->getContent();
        $fileName = '../tmp/' . Str::random() . '.json';
        file_put_contents($fileName, $content);
        exec("swagger-codegen generate -i " . $fileName . " -l html2 -o ../build");

        $response = GitHub::gist()->create([
            'description' => 'docs ' . $fileName,
            'public' => true,
            'files' => [
                'index.html' => [
                    'content' => file_get_contents('../build/index.html')
                ]
            ]
        ]);

//        return $response['html_url'];
        dd($response);
//        Storage::disk('s3')->put('avatars/1', $fileContents);
    }
}
