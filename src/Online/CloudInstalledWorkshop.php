<?php

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Application;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\ExerciseRepository;
use PhpSchool\PhpWorkshop\Listener\InitialCodeListener;
use PhpSchool\PhpWorkshop\Listener\OutputRunInfoListener;
use PhpSchool\PhpWorkshop\Logger\Logger;
use PhpSchool\PhpWorkshop\Output\BufferedOutput;
use PhpSchool\PhpWorkshop\Output\OutputInterface;
use PhpSchool\Website\Entity\Workshop;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\String\Slugger\AsciiSlugger;

class CloudInstalledWorkshop implements \JsonSerializable
{
    private Application $application;
    private \DI\Container $container;
    private Workshop $workshop;
    private AsciiSlugger $slugger;

    public function __construct(Application $application, Workshop $workshop)
    {
        $this->application = $application;
        /** @var \DI\Container $container */
        $container = $application->configure();
        $this->container = $container;
        $this->workshop = $workshop;
        $this->slugger = new AsciiSlugger();

        $this->configureLogger();
        $this->configureEvents();
    }

    public function getCode(): string
    {
        return $this->workshop->getCode();
    }

    public function getName(): string
    {
        return $this->workshop->getDisplayName();
    }

    public function getDescription(): string
    {
        return $this->workshop->getDescription();
    }

    public function getType(): string
    {
        return $this->workshop->getTypeCode();
    }

    /**
     * @return array<int, ExerciseInterface>
     */
    public function findAllExercises(): array
    {
        return $this->container->get(ExerciseRepository::class)->findAll();
    }

    public function findExerciseBySlug(string $slug): ExerciseInterface
    {
        foreach ($this->findAllExercises() as $exercise) {
            if ($this->slugger->slug($exercise->getName())->equalsTo($slug)) {
                return $exercise;
            }
        }

        throw new RuntimeException(sprintf('Cannot find workshop exercise with slug: "%s"', $slug));
    }

    public function findExerciseByName(string $name): ExerciseInterface
    {
        foreach ($this->findAllExercises() as $exercise) {
            if ($exercise->getName() === $name) {
                return $exercise;
            }
        }

        throw new RuntimeException(sprintf('Cannot find workshop exercise with name: "%s"', $name));
    }

    public function findNextExercise(ExerciseInterface $currentExercise): ?ExerciseInterface
    {
        $exercises = $this->findAllExercises();
        foreach ($exercises as $i => $exercise) {
            if ($exercise === $currentExercise && isset($exercises[$i + 1])) {
                return $exercises[$i + 1];
            }
        }

        return null;
    }

    public function getExerciseDispatcher(): ExerciseDispatcher
    {
        return $this->container->get(ExerciseDispatcher::class);
    }

    public function getService(string $service): mixed
    {
        return $this->container->get($service);
    }

    private function configureLogger(): void
    {
        $logger = new Logger(__DIR__ . "/../../var/logs/{$this->getCode()}.log");

        $this->container->set(LoggerInterface::class, $logger);
        $this->container->set(OutputInterface::class, new BufferedOutput());
    }

    private function configureEvents(): void
    {
        /** @var EventDispatcher $eventDispatch */
        $eventDispatch = $this->container->get(EventDispatcher::class);

        $initialCodeListener = $this->container->get(InitialCodeListener::class);
        $outputRunInfoListener = $this->container->get(OutputRunInfoListener::class);

        $eventDispatch->removeListener('exercise.selected', [$initialCodeListener, '__invoke']);
        $eventDispatch->removeListener('cli.run.student-execute.pre', [$outputRunInfoListener, '__invoke']);
        $eventDispatch->removeListener('cgi.run.student-execute.pre', [$outputRunInfoListener, '__invoke']);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'logo' => $this->getService('workshopLogo'),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'exercises' => array_map(function (ExerciseInterface $exercise) {
                return [
                    'slug' => $this->slugger->slug($exercise->getName())->toString(),
                    'name' => $exercise->getName(),
                    'description' => $exercise->getDescription(),
                    'type' => $exercise->getType()->getValue()
                ];
            }, $this->findAllExercises())
        ];
    }
}
