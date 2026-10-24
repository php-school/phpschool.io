<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Process\ProcessFactory;
use PhpSchool\PhpWorkshop\Process\ProcessInput;
use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class ProjectUploader
{
    public function __construct(
        private readonly PathGenerator $pathGenerator,
        private readonly ComposerPackageAllowlist $composerPackageAllowlist = new ComposerPackageAllowlist(),
    ) {}

    public function upload(Request $request, StudentDTO $student, ProcessFactory $processFactory): DirectorySolution
    {
        /** @var array{scripts?: mixed, composer_deps?: mixed, entry_point?: mixed}|null $data */
        $data = json_decode($request->getBody()->__toString(), true);

        if (!is_array($data) || !isset($data['scripts']) || !is_array($data['scripts']) || count($data['scripts']) < 1) {
            throw new \RuntimeException('No files were uploaded');
        }

        /** @var array<mixed> $scripts */
        $scripts = $data['scripts'];

        $basePath = $this->pathGenerator->random($student);

        if (file_exists($basePath)) {
            throw new \RuntimeException('Temporary directory already exists');
        }
        try {
            $this->writeScripts($scripts, $basePath);
        } catch (\RuntimeException $e) {
            (new Filesystem())->remove($basePath);
            throw $e;
        }

        if (isset($data['composer_deps']) && is_array($data['composer_deps']) && count($data['composer_deps']) > 0) {
            try {
                $this->installComposerDeps($data['composer_deps'], $basePath, $processFactory);
            } catch (\RuntimeException $e) {
                (new Filesystem())->remove($basePath);
                throw $e;
            }
        }

        $entryPoint = 'solution.php';
        if (isset($data['entry_point']) && is_string($data['entry_point']) && isset($data['scripts'][$data['entry_point']])) {
            $entryPoint = $data['entry_point'];
        }

        return new DirectorySolution($basePath, $entryPoint, ['composer.lock', 'vendor']);
    }

    /**
     * @param array<mixed> $deps
     */
    private function installComposerDeps(array $deps, string $basePath, ProcessFactory $processFactory): void
    {
        $require = $this->composerPackageAllowlist->validate($deps);

        file_put_contents(Path::join($basePath, 'composer.json'), json_encode(['require' => $require]));

        //runs in the workshop sandbox, never on the host
        $process = $processFactory->create(
            new ProcessInput('composer', ['update', '--no-interaction'], $basePath, ['XDEBUG_MODE' => 'off'], []),
        );
        $process->run();

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
            //eg ["solution.php" => "<?php ..."], anything else used to throw a TypeError and leave the folder behind
            if (!is_string($filePath) || !is_string($content)) {
                throw new \RuntimeException('Invalid file');
            }

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
