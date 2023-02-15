<?php

namespace PhpSchool\WebsiteTest\Cloud;

use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\PhpWorkshop\Solution\SolutionFile;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\Cloud\PathGenerator;
use PhpSchool\Website\Cloud\ProjectUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ProjectUploaderTest extends TestCase
{
    private Filesystem $filesystem;
    public function setUp(): void
    {
        $this->filesystem = new Filesystem();
    }

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

        $uploader->upload($request);
    }

    public function testExceptionIsThrownIfGeneratedBasePathExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Temporary directory already exists');

        $path = System::tempDir($this->getName());
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
            ->willReturn($path);

        $this->filesystem->mkdir($path);

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

        $uploader->upload($request);
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

        $uploader->upload($request);
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
        $path = System::tempDir($this->getName());
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
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
            $uploader->upload($request);
        } catch (\RuntimeException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertEquals('Invalid filepath', $e->getMessage());
        $this->assertFileDoesNotExist($path);
    }

    public function testScriptsAreCorrectlyWrittenInGeneratedPath(): void
    {
        $path = System::tempDir($this->getName());
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
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

        $result = $uploader->upload($request);

        $this->assertEquals($path, $result->getBaseDirectory());
        $this->assertEquals($path . '/solution.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [
                $path . '/folder/file2.php',
                $path . '/folder/nested/file3.php',
                $path . '/solution.php',
            ],
            array_map(fn (SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/solution.php');
        $this->assertFileExists($path . '/folder/file2.php');
        $this->assertFileExists($path . '/folder/nested/file3.php');

        $this->assertEquals('<?php', file_get_contents($path . '/solution.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/file2.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/nested/file3.php'));

        $this->filesystem->remove($path);
    }

    public function testComposerDependenciesAreInstalledIfDependenciesAreSpecified(): void
    {
        $path = System::tempDir($this->getName());
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
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

        $result = $uploader->upload($request);

        $this->assertEquals($path, $result->getBaseDirectory());

        $this->assertEquals($path . '/solution.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [$path . '/composer.json', $path . '/solution.php'],
            array_map(fn (SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/solution.php');
        $this->assertEquals('<?php', file_get_contents($path . '/solution.php'));

        $this->assertFileExists($path . '/vendor');
        $this->assertFileExists($path . '/composer.lock');

        $this->assertFileExists($path . '/composer.json');
        $this->assertEquals('{"require":{"symfony\/console":"6.2.0"}}', file_get_contents($path . '/composer.json'));

        $this->filesystem->remove($path);
    }

    public function testWithAlternateEntryPoint(): void
    {
        $path = System::tempDir($this->getName());
        $generator = $this->createMock(PathGenerator::class);
        $generator->expects($this->once())
            ->method('random')
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

        $result = $uploader->upload($request);

        $this->assertEquals($path, $result->getBaseDirectory());
        $this->assertEquals($path . '/program.php', $result->getEntryPoint()->getAbsolutePath());

        $this->assertEquals(
            [
                $path . '/folder/file2.php',
                $path . '/folder/nested/file3.php',
                $path . '/program.php',
            ],
            array_map(fn (SolutionFile $f) => $f->getAbsolutePath(), $result->getFiles())
        );

        $this->assertFileExists($path . '/program.php');
        $this->assertFileExists($path . '/folder/file2.php');
        $this->assertFileExists($path . '/folder/nested/file3.php');

        $this->assertEquals('<?php', file_get_contents($path . '/program.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/file2.php'));
        $this->assertEquals('<?php', file_get_contents($path . '/folder/nested/file3.php'));

        $this->filesystem->remove($path);
    }
}
