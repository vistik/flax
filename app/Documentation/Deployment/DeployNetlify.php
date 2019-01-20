<?php

namespace App\Documentation\Deployment;

use App\Documentation;

class DeployNetlify
{

    /**
     * @var Documentation
     */
    private $documentation;

    public function __construct(Documentation $documentation)
    {
        $this->documentation = $documentation;
    }

    public function deploy(): array
    {
        exec('/usr/local/bin/netlify deploy --dir=storage/documentations/' . $this->documentation->uuid, $output, $returnVar);

        dd($returnVar);
        return $output;
    }
}