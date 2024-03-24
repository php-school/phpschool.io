<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class ProjectUploader
{
    public function __construct(private readonly PathGenerator $pathGenerator) {}

    public function upload(Request $request, StudentDTO $student): DirectorySolution
    {
        $data = json_decode($request->getBody()->__toString(), true);

        if (!is_array($data) || !isset($data['scripts']) || !is_array($data['scripts']) || count($data['scripts']) < 1) {
            throw new \RuntimeException('No files were uploaded');
        }

        $basePath = $this->pathGenerator->random($student);

        if (file_exists($basePath)) {
            throw new \RuntimeException('Temporary directory already exists');
        }
        try {
            $this->writeScripts($data['scripts'], $basePath);
        } catch (\RuntimeException $e) {
            (new Filesystem())->remove($basePath);
            throw $e;
        }

        if (isset($data['composer_deps']) && count($data['composer_deps']) > 0) {
            $composer = [
                'require' => array_combine(
                    array_column($data['composer_deps'], 'name'),
                    array_column($data['composer_deps'], 'version'),
                )
            ];

            file_put_contents(Path::join($basePath, 'composer.json'), json_encode($composer));

            $process = (new Process(['composer', 'update',  '--no-interaction'], $basePath, ['XDEBUG_MODE' => 'off']));
            $process->run();
        }

        $entryPoint = 'solution.php';
        if (isset($data['entry_point']) && isset($data['scripts'][$data['entry_point']])) {
            $entryPoint = $data['entry_point'];
        }

        return new DirectorySolution($basePath, $entryPoint, ['composer.lock', 'vendor']);
    }

    /**
     * @param array<string, string> $scripts
     */
    private function writeScripts(array $scripts, string $basePath): void
    {
        foreach ($scripts as $filePath => $content) {
            $this->validatePath($filePath);

            $fileName = basename($filePath);
            $path = dirname($filePath);

            if (!file_exists(Path::join($basePath, $path))) {
                mkdir(Path::join($basePath, $path), 0777, true);
            }

            file_put_contents(Path::join($basePath, $path, $fileName), $content);
        }
    }

    private function validatePath(string $filePath): void
    {
        if (1 !== preg_match('#^([a-zA-Z0-9_\- ]+/?)+([a-zA-Z0-9_\- ]+\.[a-zA-Z0-9]+)?$#', $filePath)) {
            throw new \RuntimeException('Invalid filepath');
        }
    }
}
