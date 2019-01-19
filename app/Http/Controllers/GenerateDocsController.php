<?php

namespace App\Http\Controllers;

use GrahamCampbell\GitHub\Facades\GitHub;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use ZipArchive;

class GenerateDocsController extends Controller
{
    public function generate(Request $request)
    {
        $content = $request->getContent();
        $fileName = '../tmp/' . Str::random() . '.json';

        $response = GitHub::gist()->create([
            'description' => 'tmp ' . $fileName,
            'public' => true,
            'files' => [
                'swagger.json' => [
                    'content' => trim($content)
                ]
            ]
        ]);

        $client = new Client([
            'timeout'  => 2.0,
        ]);

//        dd($response['files']['swagger.json']['raw_url']);

        $response = $client->post('https://generator.swagger.io/api/gen/clients/html2', [
            'json' => [
                'swaggerUrl' => $response['files']['swagger.json']['raw_url']
            ]
        ]);

//        dd(print_r($response->getBody()->getContents()));

        $content = json_decode($response->getBody()->getContents(), true);
        $link = $content['link'];

        $content = file_get_contents($link);

        file_put_contents('download.zip', $content);

        $zip = new ZipArchive;
        $res = $zip->open('download.zip');
        if ($res === TRUE) {
            $zip->extractTo(storage_path('zip'));
            $zip->close();
            echo 'woot!';
        } else {
            echo 'doh!';
        }


        $response = GitHub::gist()->create([
            'description' => 'docs ' . $fileName,
            'public' => true,
            'files' => [
                'index.html' => [
                    'content' => file_get_contents(storage_path('zip/html2-client/index.html'))
                ]
            ]
        ]);

//        return $response['html_url'];
        dd($response);
//        Storage::disk('s3')->put('avatars/1', $fileContents);
    }
}
