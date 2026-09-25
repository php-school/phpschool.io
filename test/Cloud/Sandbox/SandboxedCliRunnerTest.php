<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud\Sandbox;

use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\BufferedOutput;
use PhpSchool\PhpWorkshop\ResultAggregator;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\Online\Sandbox\PhpSandbox;
use PhpSchool\Website\Online\Sandbox\SandboxedCliRunner;
use PhpSchool\Website\TestUtils\BaseFilesystemTest;
use PhpSchool\WebsiteTest\Asset\CliExerciseImpl;
use Symfony\Component\Filesystem\Filesystem;

class SandboxedCliRunnerTest extends BaseFilesystemTest
{
    private string $outsideDirectory;

    public function setUp(): void
    {
        $this->outsideDirectory = System::tempDir('sandbox-outside-' . bin2hex(random_bytes(4)));
        mkdir($this->outsideDirectory, 0777, true);
        file_put_contents($this->outsideDirectory . '/.env', 'JWT_SECRET=top-secret');
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove([$this->outsideDirectory, $this->getTemporaryDirectoryWithoutCreating()]);
        unset($_ENV['SANDBOX_TEST_SECRET'], $_SERVER['SANDBOX_TEST_SECRET']);
        putenv('SANDBOX_TEST_SECRET');
    }

    public function testStudentCanReadFilesInsideItsOwnDirectory(): void
    {
        $this->getTemporaryFile('data.txt', 'hello from inside');

        $output = $this->runStudentCode('<?php echo file_get_contents(__DIR__ . "/data.txt");');

        $this->assertStringContainsString('hello from inside', $output);
    }

    public function testStudentCannotReadFilesOutsideItsDirectory(): void
    {
        $output = $this->runStudentCode(
            sprintf('<?php var_dump(@file_get_contents(%s));', var_export($this->outsideDirectory . '/.env', true))
        );

        $this->assertStringContainsString('bool(false)', $output);
        $this->assertStringNotContainsString('top-secret', $output);
    }

    public function testStudentCannotReadApplicationFiles(): void
    {
        $output = $this->runStudentCode(
            sprintf('<?php var_dump(@file_get_contents(%s));', var_export(__FILE__, true))
        );

        $this->assertStringNotContainsString('class SandboxedCliRunnerTest', $output);
    }

    public function testStudentCanReadPathsPassedAsArguments(): void
    {
        $file = $this->outsideDirectory . '/input.txt';
        file_put_contents($file, 'exercise input');

        $output = $this->runStudentCode('<?php echo file_get_contents($argv[1]);', [$file]);

        $this->assertStringContainsString('exercise input', $output);
    }

    public function testMissingPathsPassedAsArgumentsCanBeCheckedWithoutWarnings(): void
    {
        $file = $this->outsideDirectory . '/missing.txt';

        $output = $this->runStudentCode('<?php var_dump(file_exists($argv[1]));', [$file]);

        $this->assertStringNotContainsString('open_basedir', $output);
        $this->assertStringContainsString('bool(false)', $output);
    }

    /**
     * @dataProvider disabledFunctionProvider
     */
    public function testDangerousFunctionsAreDisabled(string $code): void
    {
        $output = $this->runStudentCode('<?php ' . $code);

        $this->assertStringContainsString('Call to undefined function', $output);
        $this->assertStringNotContainsString('SANDBOX_ESCAPED', $output);
    }

    /**
     * @return array<array<string>>
     */
    public function disabledFunctionProvider(): array
    {
        return [
            ['echo shell_exec("echo SANDBOX_ESCAPED");'],
            ['system("echo SANDBOX_ESCAPED");'],
            ['exec("echo SANDBOX_ESCAPED", $o); echo implode($o);'],
            ['passthru("echo SANDBOX_ESCAPED");'],
            ['proc_open("echo SANDBOX_ESCAPED", [], $pipes);'],
            ['popen("echo SANDBOX_ESCAPED", "r");'],
            ['putenv("LD_PRELOAD=/tmp/evil.so");'],
            ['mail("a@b.c", "s", "m");'],
            ['stream_socket_client("tcp://127.0.0.1:6379");'],
            ['fsockopen("127.0.0.1", 6379);'],
        ];
    }

    public function testRuntimeLimitsAreApplied(): void
    {
        $output = $this->runStudentCode(
            '<?php echo ini_get("memory_limit"), "|", ini_get("allow_url_fopen"), "|", extension_loaded("ffi") ? ini_get("ffi.enable") : "0";'
        );

        $this->assertStringContainsString('128M|0|0', $output);
    }

    public function testApplicationEnvironmentIsNotInherited(): void
    {
        $_ENV['SANDBOX_TEST_SECRET'] = 'from-dotenv';
        $_SERVER['SANDBOX_TEST_SECRET'] = 'from-dotenv';
        putenv('SANDBOX_TEST_SECRET=from-dotenv');

        $output = $this->runStudentCode('<?php var_dump(getenv("SANDBOX_TEST_SECRET"));');

        $this->assertStringContainsString('bool(false)', $output);
        $this->assertStringNotContainsString('from-dotenv', $output);
    }

    public function testSandboxCannotBeLoosenedAtRuntime(): void
    {
        $output = $this->runStudentCode(
            sprintf(
                '<?php ini_set("open_basedir", "/"); var_dump(@file_get_contents(%s));',
                var_export($this->outsideDirectory . '/.env', true)
            )
        );

        $this->assertStringContainsString('bool(false)', $output);
    }

    /**
     * @param array<string> $args
     */
    private function runStudentCode(string $code, array $args = []): string
    {
        $program = $this->getTemporaryFile('solution.php', $code);

        $exercise = new class ($args) extends CliExerciseImpl {
            /** @param array<string> $args */
            public function __construct(private array $args)
            {
                parent::__construct();
            }

            public function getArgs(): array
            {
                return [$this->args];
            }
        };

        $runner = new SandboxedCliRunner(
            $exercise,
            new EventDispatcher(new ResultAggregator()),
            new PhpSandbox()
        );

        $output = new BufferedOutput();
        $runner->run(
            new Input('app', ['program' => $program, SandboxedCliRunner::ROOT_ARGUMENT => $this->getTemporaryDirectory()]),
            $output
        );

        return $output->fetch();
    }
}
