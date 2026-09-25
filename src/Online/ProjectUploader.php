<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;
use PhpSchool\Website\Online\Sandbox\PhpSandbox;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ProjectUploader
{
    public function __construct(
        private readonly PathGenerator $pathGenerator,
        private readonly ComposerPackageAllowlist $composerPackageAllowlist = new ComposerPackageAllowlist(),
        private readonly PhpSandbox $sandbox = new PhpSandbox(),
    ) {}

    public function upload(Request $request, StudentDTO $student): DirectorySolution
    {
        $data = json_decode($request->getBody()->__toString(), true);

        if (!is_array($data) || !isset($data['scripts']) || !is_array($data['scripts']) || count($data['scripts']) < 1) {
            throw new \RuntimeException('No files were uploaded');
        }

        $require = [];
        if (!empty($data['composer_deps'])) {
            $require = $this->composerPackageAllowlist->validate($data['composer_deps']);
        }

        $basePath = $this->pathGenerator->random($student);

        if (file_exists($basePath)) {
            throw new \RuntimeException('Temporary directory already exists');
        }

        try {
            $this->writeScripts($data['scripts'], $basePath);

            if (count($require) > 0) {
                $this->installDependencies($require, $basePath);
            }
        } catch (\RuntimeException $e) {
            (new Filesystem())->remove($basePath);
            throw $e;
        }

        $entryPoint = 'solution.php';
        if (isset($data['entry_point']) && is_string($data['entry_point']) && isset($data['scripts'][$data['entry_point']])) {
            $entryPoint = $data['entry_point'];
        }

        return new DirectorySolution($basePath, $entryPoint, ['composer.lock', 'vendor']);
    }

    /**
     * @param array<string, string> $require
     */
    private function installDependencies(array $require, string $basePath): void
    {
        $composer = [
            'require' => $require,
            'config' => ['allow-plugins' => false],
        ];

        file_put_contents(Path::join($basePath, 'composer.json'), json_encode($composer));

        $env = ['XDEBUG_MODE' => 'off', 'COMPOSER_NO_INTERACTION' => '1'];
        foreach (['PATH', 'HOME', 'COMPOSER_HOME', 'COMPOSER_CACHE_DIR'] as $name) {
            $value = getenv($name);
            if (is_string($value) && $value !== '') {
                $env[$name] = $value;
            }
        }

        $process = new Process(
            ['composer', 'update', '--no-interaction', '--no-plugins', '--no-scripts', '--no-progress'],
            $basePath,
            $this->sandbox->environment($env),
            null,
            120
        );

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            throw new \RuntimeException('Composer dependencies could not be installed');
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Composer dependencies could not be installed');
        }
    }

    /**
     * @param array<mixed> $scripts
     */
    private function writeScripts(array $scripts, string $basePath): void
    {
        foreach ($scripts as $filePath => $content) {
            if (!is_string($content)) {
                throw new \RuntimeException('Invalid file contents');
            }

            $filePath = (string) $filePath;
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
