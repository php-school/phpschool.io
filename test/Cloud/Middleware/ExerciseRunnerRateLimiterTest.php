<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Online\Middleware\ExerciseRunnerRateLimiter;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\ArraySession;
use PhpSchool\Website\User\StudentDTO;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class ExerciseRunnerRateLimiterTest extends TestCase
{
    public function testTheSharedLimitAppliesAcrossStudents(): void
    {
        $session = new ArraySession();
        $limiter = new ExerciseRunnerRateLimiter(
            $session,
            $this->factory('student', 10),
            $this->factory('all', 2),
        );

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };

        $statuses = [];
        foreach (range(1, 3) as $i) {
            //a different student each time, so only the shared limit can reject
            $session->set('student', new StudentDTO(Uuid::uuid4(), 's', 's@phpschool.io', 'S', null, null, new \DateTime(), false, new StudentCloudState([])));
            $statuses[] = $limiter(new ServerRequest('POST', '/api/online/workshop/run/a/exercise/b'), $handler)->getStatusCode();
        }

        $this->assertSame([200, 200, 429], $statuses);
    }

    private function factory(string $id, int $limit): RateLimiterFactory
    {
        return new RateLimiterFactory(
            ['id' => $id, 'policy' => 'sliding_window', 'limit' => $limit, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
    }
}
