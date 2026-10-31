<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Process\Sandbox;
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

    /**
     * The volume a student's packages for one exercise are kept in between verifies, see DockerSandbox
     *
     * @return array<string, string> eg. ['/tmp/php-school/<student id>' => 'php-school-vendor-<student id>-learnyouphp-dependency-heaven']
     */
    public function vendorVolumes(StudentDTO $student, string $workshop, string $exercise): array
    {
        return [
            $this->pathGenerator->student($student) => sprintf('php-school-vendor-%s-%s-%s', $student->id->toString(), $workshop, $exercise),
        ];
    }

    public function upload(Request $request, StudentDTO $student, Sandbox $sandbox, string $workshop, string $exercise): DirectorySolution
    {
        /** @var array{scripts?: mixed, composer_deps?: mixed, entry_point?: string}|null $data */
        $data = json_decode($request->getBody()->__toString(), true);

        if (!is_array($data) || !isset($data['scripts']) || !is_array($data['scripts']) || count($data['scripts']) < 1) {
            throw new \RuntimeException('No files were uploaded');
        }

        /** @var array<string, string> $scripts */
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
                $this->installComposerDeps(
                    $data['composer_deps'],
                    $basePath,
                    $sandbox,
                    //the last composer.json and lock for this exercise, eg. /tmp/php-school/<student id>/learnyouphp-dependency-heaven.composer.json
                    Path::join($this->pathGenerator->student($student), sprintf('%s-%s', $workshop, $exercise)),
                );
            } catch (\RuntimeException $e) {
                (new Filesystem())->remove($basePath);
                throw $e;
            }
        }

        $entryPoint = 'solution.php';
        if (isset($data['entry_point']) && isset($data['scripts'][$data['entry_point']])) {
            $entryPoint = $data['entry_point'];
        }

        return new DirectorySolution($basePath, $entryPoint, ['composer.lock', 'vendor']);
    }

    /**
     * @param array<mixed> $deps
     */
    private function installComposerDeps(array $deps, string $basePath, Sandbox $sandbox, string $last): void
    {
        $require = $this->composerPackageAllowlist->validate($deps);
        $composerJson = (string) json_encode(['require' => $require]);

        file_put_contents(Path::join($basePath, 'composer.json'), $composerJson);

        //same packages as last time: install from the last lock, which does nothing while the vendor volume is still there
        $unchanged = file_exists($last . '.composer.lock') && @file_get_contents($last . '.composer.json') === $composerJson;
        if ($unchanged) {
            copy($last . '.composer.lock', Path::join($basePath, 'composer.lock'));
        }

        //runs in the workshop sandbox, never on the host
        $execution = $sandbox->create(
            new ProcessInput('composer', [$unchanged ? 'install' : 'update', '--no-interaction'], $basePath, ['XDEBUG_MODE' => 'off'], []),
        );
        $execution->start();
        $execution->wait();

        if (!$execution->isSuccessful()) {
            throw new \RuntimeException('Composer dependencies could not be installed');
        }

        file_put_contents($last . '.composer.json', $composerJson);
        copy(Path::join($basePath, 'composer.lock'), $last . '.composer.lock');
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
