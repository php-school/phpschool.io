<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud\Sandbox;

use GuzzleHttp\Psr7\Request;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Exercise\CgiExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\BufferedOutput;
use PhpSchool\PhpWorkshop\ResultAggregator;
use PhpSchool\Website\Online\Sandbox\PhpSandbox;
use PhpSchool\Website\Online\Sandbox\SandboxedCgiRunner;
use PhpSchool\Website\Online\Sandbox\SandboxedCliRunner;
use PhpSchool\Website\TestUtils\BaseFilesystemTest;
use PhpSchool\WebsiteTest\Asset\CliExerciseImpl;
use Symfony\Component\Filesystem\Filesystem;

class SandboxedCgiRunnerTest extends BaseFilesystemTest
{
    public function tearDown(): void
    {
        (new Filesystem())->remove($this->getTemporaryDirectoryWithoutCreating());
        unset($_ENV['SANDBOX_TEST_SECRET']);
    }

    public function testStudentRequestIsHandled(): void
    {
        $output = $this->runStudentCode('<?php echo "method:" . $_SERVER["REQUEST_METHOD"] . " body:" . file_get_contents("php://input");');

        $this->assertStringContainsString('method:POST body:name=aydin', $output);
    }

    public function testStudentCannotReadFilesOutsideItsDirectory(): void
    {
        $output = $this->runStudentCode(
            sprintf('<?php var_dump(@file_get_contents(%s));', var_export(__FILE__, true))
        );

        $this->assertStringNotContainsString('class SandboxedCgiRunnerTest', $output);
    }

    public function testShellFunctionsAreDisabled(): void
    {
        $output = $this->runStudentCode('<?php echo shell_exec("echo SANDBOX_ESCAPED");');

        $this->assertStringContainsString('Call to undefined function', $output);
        $this->assertStringNotContainsString('SANDBOX_ESCAPED', $output);
    }

    public function testApplicationEnvironmentIsNotInherited(): void
    {
        $_ENV['SANDBOX_TEST_SECRET'] = 'from-dotenv';

        $output = $this->runStudentCode('<?php var_dump(getenv("SANDBOX_TEST_SECRET"));');

        $this->assertStringNotContainsString('from-dotenv', $output);
    }

    private function runStudentCode(string $code): string
    {
        $program = $this->getTemporaryFile('solution.php', $code);

        $exercise = new class () extends CliExerciseImpl implements CgiExercise {
            public function getRequests(): array
            {
                return [
                    (new Request('POST', 'http://www.time.com/api', ['Content-Type' => 'application/x-www-form-urlencoded'], 'name=aydin')),
                ];
            }

            public function getType(): ExerciseType
            {
                return ExerciseType::CGI();
            }
        };

        $runner = new SandboxedCgiRunner(
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
