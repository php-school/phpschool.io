<?php

namespace PhpSchool\Website\Action\Online;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ProvidesInitialCode;
use PhpSchool\PhpWorkshop\Exercise\ProvidesSolution;
use PhpSchool\PhpWorkshop\Solution\SolutionFile;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProblemFileConverter;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\SessionStorageInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
        PhpRenderer $renderer,
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

        $data = [
            'student' => $this->session->get('student'),
            'workshop' => $workshop,
            'exercise' => [
                'name' => $exercise->getName(),
                'slug' => $renderer->slug($exercise->getName()),
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
}