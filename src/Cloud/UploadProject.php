<?php

namespace PhpSchool\Website\Cloud;

use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\PhpWorkshop\Utils\System;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Process\Process;

class UploadProject
{
    private ?string $entryPoint = 'solution.php';

    public function upload(Request $request): string
    {
        $data = json_decode($request->getBody()->__toString(), true);

        $basePath = System::tempDir(); //TODO: unique per user
        foreach ($data['scripts'] ?? [] as $filePath => $content) {
            $fileName = basename($filePath);
            $path = dirname($filePath);

            //TODO: directory reversal hacks
            if (!file_exists(Path::join($basePath, $path))) {
                mkdir(Path::join($basePath, $path), 0777, true); //TODO: must not do 0777
            }

            file_put_contents(Path::join($basePath, $path, $fileName), $content);
        }

        if (isset($data['composerDeps']) && count($data['composerDeps']) > 0) {
            $composer = [
                'require' => array_combine(
                    array_column($data['composerDeps'], 'name'),
                    array_column($data['composerDeps'], 'version'),
                )
            ];

            file_put_contents(Path::join($basePath, 'composer.json'), json_encode($composer));

            $process = (new Process(['composer', 'update',  '--no-interaction'], $basePath, ['XDEBUG_MODE' => 'off']));
            $process->run();
        }

        if (isset($data['entry_point']) && isset($data['scripts'][$data['entry_point']])) {
            $this->entryPoint = $data['entry_point'];
        }

        return $basePath;
    }

    public function getEntryPoint(): string
    {
        return $this->entryPoint;
    }
}
