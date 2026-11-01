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
use Symfony\Component\Semaphore\Exception\SemaphoreAcquiringException;
use Symfony\Component\Semaphore\Key;
use Symfony\Component\Semaphore\PersistingStoreInterface;
use Symfony\Component\Semaphore\SemaphoreFactory;

class ExerciseRunnerRateLimiterTest extends TestCase
{
    public function testRequestsWaitForAFreeSlotThenGiveUp(): void
    {
        $semaphoreFactory = new SemaphoreFactory(new InMemorySemaphoreStore());
        $limiter = $this->limiter($semaphoreFactory);

        //another student's run holds the only slot
        $running = $semaphoreFactory->createSemaphore('exerciseRunner', 1);
        $running->acquire();

        $this->assertSame(503, $this->runExercise($limiter)->getStatusCode());

        $running->release();

        $this->assertSame(200, $this->runExercise($limiter)->getStatusCode());
    }

    public function testTheSlotIsFreedAfterEachRun(): void
    {
        $limiter = $this->limiter(new SemaphoreFactory(new InMemorySemaphoreStore()));

        $this->assertSame(200, $this->runExercise($limiter)->getStatusCode());
        $this->assertSame(200, $this->runExercise($limiter)->getStatusCode());
    }

    private function limiter(SemaphoreFactory $semaphoreFactory): ExerciseRunnerRateLimiter
    {
        $session = new ArraySession();
        $session->set('student', new StudentDTO(Uuid::uuid4(), 's', 's@phpschool.io', 'S', null, null, new \DateTime(), false, new StudentCloudState([])));

        return new ExerciseRunnerRateLimiter($session, $this->factory('student', 10), $semaphoreFactory, maxRunning: 1, maxWaitSeconds: 0);
    }

    private function runExercise(ExerciseRunnerRateLimiter $limiter): ResponseInterface
    {
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };

        return $limiter(new ServerRequest('POST', '/api/online/workshop/run/a/exercise/b'), $handler);
    }

    private function factory(string $id, int $limit): RateLimiterFactory
    {
        return new RateLimiterFactory(
            ['id' => $id, 'policy' => 'sliding_window', 'limit' => $limit, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
    }
}

//symfony only ships a redis store, this one ignores ttls
class InMemorySemaphoreStore implements PersistingStoreInterface
{
    /** @var array<string, array<int, true>> resource => the keys holding a slot */
    private array $held = [];

    public function save(Key $key, float $ttlInSecond): void
    {
        $held = $this->held[(string) $key] ?? [];
        if (!isset($held[spl_object_id($key)]) && count($held) >= $key->getLimit()) {
            throw new SemaphoreAcquiringException($key, 'all slots are taken');
        }

        $this->held[(string) $key][spl_object_id($key)] = true;
    }

    public function delete(Key $key): void
    {
        unset($this->held[(string) $key][spl_object_id($key)]);
    }

    public function exists(Key $key): bool
    {
        return isset($this->held[(string) $key][spl_object_id($key)]);
    }

    public function putOffExpiration(Key $key, float $ttlInSecond): void {}
}
