<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ProvidesSolution;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProblemFileConverter;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExerciseEditor
{
    private CloudWorkshopRepository $installedWorkshops;
    private ProblemFileConverter $problemFileConverter;

    public function __construct(
        CloudWorkshopRepository $installedWorkshops,
        ProblemFileConverter $problemFileConverter
    ) {
        $this->installedWorkshops = $installedWorkshops;
        $this->problemFileConverter = $problemFileConverter;
    }

    public function __invoke(
        Request $request,
        Response $response,
        PhpRenderer $renderer,
        string $workshop,
        string $exercise
    ): Response {

        $workshop = $this->installedWorkshops->findByCode($workshop);
        $exercise = $workshop->findExerciseBySlug($exercise);
        $nextExercise = $workshop->findNextExercise($exercise);

        $link = null;
        if ($nextExercise) {
            $link = sprintf(
                '/cloud/workshop/%s/exercise/%s/editor',
                $workshop->getCode(),
                $renderer->slug($nextExercise->getName())
            );
        }

        $data = [
            'workshop' => $workshop,
            'exercise' => $exercise,
            'nextExerciseLink' => $link,
            'problem' => $this->problemFileConverter->htmlFromExercise($exercise)
        ];

        $data = $this->maybeAddOfficialSolution($data, $exercise);

        return $renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle' => 'PHP School Cloud',
            'pageDescription' => 'PHP School Cloud',
            'content' => $renderer->fetch('cloud/exercise-editor.phtml', $data)
        ]);
    }

    private function maybeAddOfficialSolution(array $data, ExerciseInterface $exercise): array
    {
        if (!$exercise instanceof ProvidesSolution) {
            return $data;
        }

        $data['official_solution'] = [];

        $entryPointPath = $exercise->getSolution()->getEntryPoint();

        foreach ($exercise->getSolution()->getFiles() as $file) {
            $data['official_solution'][] = [
                'file_path' => $file->getRelativePath(),
                'entry_point' => $file->getAbsolutePath() === $entryPointPath,
                'content'  => base64_encode($file->getContents())
            ];
        }

        return $data;
    }
}
