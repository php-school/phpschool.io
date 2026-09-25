<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud;

use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\PhpWorkshop\Solution\SolutionFile;
use PhpSchool\Website\Online\ComposerPackageAllowlist;
use PhpSchool\Website\Online\PathGenerator;
use PhpSchool\Website\Online\ProjectUploader;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\TestUtils\BaseFilesystemTest;
use PhpSchool\Website\User\StudentDTO;
use Ramsey\Uuid\Uuid;

class ProjectUploaderTest extends BaseFilesystemTest
{
    public function testExceptionIsThrownIfNoScriptsFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No files were uploaded');

        $uploader = new ProjectUploader(new PathGenerator());

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([])
        );

        $uploader->upload($request, $this->getStudent());
    }

    public function testExceptionIsThrownIfGeneratedBasePathExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Temporary directory already exists');

        $student = $this->getStudent();

        $path = $this->getTemporaryDirectory();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator);

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    'file1.php' => '<?php'
                ]
            ])
        );

        $uploader->upload($request, $student);
    }

    /**
     * @dataProvider invalidPathProvider
     */
    public function testExceptionIsThrownForInvalidPaths(string $path): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid filepath');

        $uploader = new ProjectUploader(new PathGenerator());

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    $path => '<?php'
                ]
            ])
        );

        $uploader->upload($request, $this->getStudent());
    }

    /**
     * @return array<array<string>>
     */
    public function invalidPathProvider(): array
    {
        return [
            ['/root/folder'],
            ['directory/../traversal'],
            ['directory/../traversal/one.php'],
            ['some-file*/solution.php'],
            ['some-file\'/solution.php'],
        ];
    }

    public function testBaseFolderIsRemovedIfInvalidPathFound(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator);

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    '/invalid/path' => '<?php'
                ]
            ])
        );

        try {
            $uploader->upload($request, $student);
        } catch (\RuntimeException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertEquals('Invalid filepath', $e->getMessage());
        $this->assertFileDoesNotExist($path);
    }

    public function testScriptsAreCorrectlyWrittenInGeneratedPath(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator);

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    'solution.php' => '<?php',
                    'folder/file2.php' => '<?php',
                    'folder/nested/file3.php' => '<?php'
                ]
            ])
        );

        $result = $uploader->upload($request, $student);

        $this->assertEquals($path, $result->getBaseDirectory());
        $this->assertEquals($path . '/solution.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [
                $path . '/folder/file2.php',
                $path . '/folder/nested/file3.php',
                $path . '/solution.php',
            ],
            array_map(fn(SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/solution.php');
        $this->assertFileExists($path . '/folder/file2.php');
        $this->assertFileExists($path . '/folder/nested/file3.php');

        $this->assertEquals('<?php', file_get_contents($path . '/solution.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/file2.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/nested/file3.php'));
    }

    public function testComposerDependenciesAreInstalledIfDependenciesAreSpecified(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator);

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    'solution.php' => '<?php',
                ],
                'composer_deps' => [
                    ['name' => 'symfony/console', 'version' => '6.2.0']
                ]
            ])
        );

        $uploader = new ProjectUploader($generator, $this->getAllowlist());
        $result = $uploader->upload($request, $student);

        $this->assertEquals($path, $result->getBaseDirectory());

        $this->assertEquals($path . '/solution.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [$path . '/composer.json', $path . '/solution.php'],
            array_map(fn(SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/solution.php');
        $this->assertEquals('<?php', file_get_contents($path . '/solution.php'));

        $this->assertFileExists($path . '/vendor');
        $this->assertFileExists($path . '/composer.lock');

        $this->assertFileExists($path . '/composer.json');
        $this->assertEquals(
            '{"require":{"symfony\/console":"6.2.0"},"config":{"allow-plugins":false}}',
            file_get_contents($path . '/composer.json')
        );
    }

    public function testWithAlternateEntryPoint(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator);

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => [
                    'program.php' => '<?php',
                    'folder/file2.php' => '<?php',
                    'folder/nested/file3.php' => '<?php'
                ],
                'entry_point' => 'program.php'
            ])
        );

        $result = $uploader->upload($request, $student);

        $this->assertEquals($path, $result->getBaseDirectory());
        $this->assertEquals($path . '/program.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [
                $path . '/folder/file2.php',
                $path . '/folder/nested/file3.php',
                $path . '/program.php',
            ],
            array_map(fn(SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/program.php');
        $this->assertFileExists($path . '/folder/file2.php');
        $this->assertFileExists($path . '/folder/nested/file3.php');

        $this->assertEquals('<?php', file_get_contents($path . '/program.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/file2.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/nested/file3.php'));
    }

    /**
     * @dataProvider rejectedComposerDependenciesProvider
     */
    public function testRejectedComposerDependenciesDoNotCreateAProject(mixed $dependencies, string $message): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->never())->method('random');

        $uploader = new ProjectUploader($generator, $this->getAllowlist());

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => ['solution.php' => '<?php'],
                'composer_deps' => $dependencies,
            ])
        );

        try {
            $uploader->upload($request, $student);
            $this->fail('Expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals($message, $e->getMessage());
        }

        $this->assertFileDoesNotExist($path);
    }

    /**
     * @return array<string, array{0: mixed, 1: string}>
     */
    public function rejectedComposerDependenciesProvider(): array
    {
        return [
            'package not in the allowlist' => [
                [['name' => 'evil/composer-plugin', 'version' => '1.0.0']],
                'Composer package "evil/composer-plugin" is not allowed',
            ],
            'invalid package name' => [
                [['name' => '../../etc/passwd', 'version' => '1.0.0']],
                'Composer package "../../etc/passwd" is not allowed',
            ],
            'vcs style version' => [
                [['name' => 'symfony/console', 'version' => 'dev-main#abc"; rm -rf /']],
                'Invalid version constraint for composer package "symfony/console"',
            ],
            'not a list' => ['symfony/console', 'Invalid composer dependencies'],
            'missing version' => [[['name' => 'symfony/console']], 'Invalid composer dependencies'],
            'too many packages' => [
                array_fill(0, 11, ['name' => 'psr/log', 'version' => '^3.0']),
                'A maximum of 10 composer dependencies are allowed',
            ],
        ];
    }

    public function testComposerDependenciesAreRejectedWhenThePackageListIsMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Composer package "symfony/console" is not allowed');

        $uploader = new ProjectUploader(
            new PathGenerator(),
            new ComposerPackageAllowlist(__DIR__ . '/_files/does-not-exist.json')
        );

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => ['solution.php' => '<?php'],
                'composer_deps' => [['name' => 'symfony/console', 'version' => '6.2.0']],
            ])
        );

        $uploader->upload($request, $this->getStudent());
    }

    public function testProjectIsRemovedWhenComposerInstallFails(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->with($student)
            ->willReturn($path);

        $uploader = new ProjectUploader($generator, $this->getAllowlist());

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode([
                'scripts' => ['solution.php' => '<?php'],
                'composer_deps' => [['name' => 'psr/log', 'version' => '999.0.0']],
            ])
        );

        try {
            $uploader->upload($request, $student);
            $this->fail('Expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Composer dependencies could not be installed', $e->getMessage());
        }

        $this->assertFileDoesNotExist($path);
    }

    public function testNonStringScriptContentsAreRejected(): void
    {
        $student = $this->getStudent();
        $path = $this->getTemporaryDirectoryWithoutCreating();
        $generator = $this->createMock(PathGenerator::class);
        $generator->method('random')->willReturn($path);

        $uploader = new ProjectUploader($generator, $this->getAllowlist());

        $request = new ServerRequest(
            'POST',
            '/verify',
            [],
            json_encode(['scripts' => ['solution.php' => ['not', 'a', 'string']]])
        );

        try {
            $uploader->upload($request, $student);
            $this->fail('Expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid file contents', $e->getMessage());
        }

        $this->assertFileDoesNotExist($path);
    }

    private function getAllowlist(): ComposerPackageAllowlist
    {
        return new ComposerPackageAllowlist(__DIR__ . '/_files/packages.json');
    }

    private function getStudent(): StudentDTO
    {
        return new StudentDTO(
            Uuid::uuid4(),
            'Student',
            'student@phpschool.io',
            'Student',
            null,
            null,
            new \DateTime(),
            false,
            new StudentCloudState([])
        );
    }
}
