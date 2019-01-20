<?php

namespace App\Documentation\Generators;

use App\Documentation;
use GuzzleHttp\Client;
use ZipArchive;

class OnlineGenerator
{

    /**
     * @var Documentation
     */
    private $documentation;

    public function __construct(Documentation $documentation)
    {
        $this->documentation = $documentation;
    }

    public function generateHTML()
    {
        $client = new Client([
            'timeout' => 30,
        ]);

        $response = $client->post('https://generator.swagger.io/api/gen/clients/html2', [
            'json' => [
                'swaggerUrl' => 'http://92613589.ngrok.io/api/documentations/' . $this->documentation->uuid,
            ]
        ]);

        $content = json_decode($response->getBody()->getContents(), true);
        $link = $content['link'];

        $content = file_get_contents($link);

        file_put_contents(storage_path($this->documentation->uuid . '.zip'), $content);

        $zip = new ZipArchive;
        $zip->open(storage_path($this->documentation->uuid . '.zip'));
        $zip->extractTo(storage_path('zip/' . $this->documentation->uuid));
        $zip->close();

        unlink(storage_path($this->documentation->uuid . '.zip'));

//        $html = file_get_contents(storage_path('zip/' . $this->documentation->uuid . '/html2-client/index.html'));

        \File::makeDirectory(storage_path('documentations/' . $this->documentation->uuid));
        $filePath = storage_path('documentations/' . $this->documentation->uuid . '/index.html');
        \File::move(storage_path('zip/' . $this->documentation->uuid . '/html2-client/index.html'), $filePath);

        return $filePath;
    }

}