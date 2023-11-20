<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ProvidesInitialCode;
use PhpSchool\PhpWorkshop\Exercise\ProvidesSolution;
use PhpSchool\PhpWorkshop\Solution\SolutionFile;
use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProblemFileConverter;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\SessionStorageInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExerciseEditor
{
    use RedirectUtils;

    private CloudWorkshopRepository $installedWorkshops;
    private ProblemFileConverter $problemFileConverter;
    private StudentWorkshopState $studentState;
    private SessionStorageInterface $session;

    public function __construct(
        CloudWorkshopRepository $installedWorkshops,
        ProblemFileConverter $problemFileConverter,
        StudentWorkshopState $studentProgress,
        SessionStorageInterface $session
    ) {
        $this->installedWorkshops = $installedWorkshops;
        $this->problemFileConverter = $problemFileConverter;
        $this->studentState = $studentProgress;
        $this->session = $session;
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
            return $this->redirect('/cloud');
        }
        $nextExercise = $workshop->findNextExercise($exercise);

        $this->studentState->setCurrentExercise($workshop->getCode(), $exercise->getName());

        $link = null;
        if ($nextExercise) {
            $link = sprintf(
                '/online/workshop/%s/exercise/%s/editor',
                $workshop->getCode(),
                $renderer->slug($nextExercise->getName())
            );
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
            'nextExerciseLink' => $link,
            'problem' => $this->problemFileConverter->htmlFromExercise($exercise),
            'totalExerciseCount' => $this->installedWorkshops->totalExerciseCount(),
        ];

        $data = $this->maybeAddOfficialSolution($data, $exercise);
        $data = $this->addInitialCode($data, $exercise);

        return $renderer->render($response, 'layouts/online.phtml', [
            'pageTitle' => 'PHP School Cloud',
            'pageDescription' => 'PHP School Cloud',
            'content' => $renderer->fetch('online/exercise-editor.phtml', $data)
        ]);
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
