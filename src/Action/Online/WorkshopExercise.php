<?php

namespace PhpSchool\Website\Action\Online;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ProvidesInitialCode;
use PhpSchool\PhpWorkshop\Exercise\ProvidesSolution;
use PhpSchool\PhpWorkshop\Solution\SolutionFile;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudInstalledWorkshop;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProblemFileConverter;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @phpstan-type Exercise array{
 *    student: StudentDTO,
 *    workshop: CloudInstalledWorkshop,
 *    exercise: array{name: string, slug: string, description: string, type: string},
 *    problem: string,
 *    totalExerciseCount: int,
 * }
 *
 * @phpstan-type ExerciseWithSolution array{
 *     student: StudentDTO,
 *     workshop: CloudInstalledWorkshop,
 *     exercise: array{name: string, slug: string, description: string, type: string},
 *     problem: string,
 *     totalExerciseCount: int,
 *     official_solution?: array<array{file_path: string, entry_point: bool, content: string}>,
 *  }
 *
 * @phpstan-type ExerciseWithInitialFiles array{
 *    student: StudentDTO,
 *    workshop: CloudInstalledWorkshop,
 *    exercise: array{name: string, slug: string, description: string, type: string},
 *    problem: string,
 *    totalExerciseCount: int,
 *    official_solution?: array<array{file_path: string, entry_point: bool, content: string}>,
 *    initial_files: array<array{name: string, content: string}>,
 *    entry_point: string
 * }
 */
class WorkshopExercise
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly ProblemFileConverter $problemFileConverter,
        private readonly SessionStorageInterface $session
    ) {
    }

    public function __invoke(
        Request $request,
        Response $response,
        string $workshop,
        string $exercise
    ): MessageInterface {
        try {
            $workshop = $this->installedWorkshops->findByCode($workshop);
            $exercise = $workshop->findExerciseBySlug($exercise);
        } catch (\RuntimeException $e) {
            return $this->withJson([
                'success' => false,
                'error' => $e->getMessage()
            ], $response, 404);
        }

        /** @var StudentDTO $student */
        $student = $this->session->get('student');
        $data = [
            'student' => $student,
            'workshop' => $workshop,
            'exercise' => [
                'name' => $exercise->getName(),
                'slug' => $this->slug($exercise->getName()),
                'description' => $exercise->getDescription(),
                'type' => $exercise->getType()
            ],
            'problem' => $this->problemFileConverter->htmlFromExercise($exercise),
            'totalExerciseCount' => $this->installedWorkshops->totalExerciseCount(),
        ];

        $data = $this->maybeAddOfficialSolution($data, $exercise);
        $data = $this->addInitialCode($data, $exercise);

        return $this->withJson($data, $response);
    }

    /**
     * @param Exercise $data
     * @return ExerciseWithSolution
     */
    private function maybeAddOfficialSolution(array $data, ExerciseInterface $exercise): array
    {
        if (!$exercise instanceof ProvidesSolution) {
            return $data;
        }

        $data['official_solution'] = [];

        $entryPoint = $exercise->getSolution()->getEntryPoint();

        foreach ($exercise->getSolution()->getFiles() as $file) {
            $data['official_solution'][] = [
                'file_path' => $file->getRelativePath(),
                'entry_point' => $file === $entryPoint,
                'content'  => base64_encode($file->getContents())
            ];
        }

        return $data;
    }

    /**
     * @param ExerciseWithSolution $data
     * @return ExerciseWithInitialFiles
     */
    private function addInitialCode(array $data, ExerciseInterface $exercise): array
    {
        if (!$exercise instanceof ProvidesInitialCode) {
            $data['initial_files'][] = [
                'name' => 'solution.php',
                'content' => '<?php ',
            ];
            $data['entry_point'] = 'solution.php';

            return $data;
        }

        $data['initial_files'] = array_map(
            fn (SolutionFile $file) => [
                'name' => $file->getRelativePath(),
                'content' => $file->getContents(),
            ],
            $exercise->getInitialCode()->getFiles()
        );

        $data['entry_point'] = $exercise->getInitialCode()->getEntryPoint()->getRelativePath();

        return $data;
    }

    private function slug(string $string): string
    {
        return (new AsciiSlugger())->slug($string)->toString();
    }
}
