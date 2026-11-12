<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Online;

use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProjectUploader;
use PhpSchool\Website\Online\Sandbox;
use PhpSchool\Website\Online\StageStream;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\Online\VueResultsRenderer;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class VerifyExercise
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly ProjectUploader $projectUploader,
        private readonly SessionStorageInterface $session,
        private readonly StudentWorkshopState $studentState,
        private readonly VueResultsRenderer $resultsRenderer,
        private readonly StageStream $stages,
        private readonly Sandbox $sandbox,
    ) {}

    public function __invoke(
        Request $request,
        Response $response,
        string $workshop,
        string $exercise,
    ): Response {
        try {
            $workshop = $this->installedWorkshops->findByCode($workshop);
            $exercise = $workshop->findExerciseBySlug($exercise);
        } catch (\RuntimeException $e) {
            return $this->withJson(['success' => false, 'error' => $e->getMessage()], $response);
        }

        $this->studentState->setCurrentExercise($workshop->getCode(), $exercise->getName());

        try {
            $project = $this->projectUploader->upload($request, $this->getStudent());
        } catch (\RuntimeException $e) {
            return $this->withJson(['success' => false, 'error' => $e->getMessage()], $response);
        }

        $events = $workshop->getExerciseDispatcher()->getEventDispatcher();
        $events->listen(['cli.verify.reference-execute.pre', 'cgi.verify.reference-execute.pre'], fn() => $this->stages->send('reference-execute'));
        $events->listen(['cli.verify.student-execute.pre', 'cgi.verify.student-execute.pre'], fn() => $this->stages->send('student-execute'));
        $events->listen('verify.post.execute', fn() => $this->stages->send('check'));
        $events->listen('verify.post.check', fn() => $this->stages->send('cleanup'));

        try {
            $results = $this->sandbox->run(fn() => $workshop->getExerciseDispatcher()->verify(
                $exercise,
                new Input($workshop->getCode(), ['program' => $project->getEntryPoint()->getAbsolutePath()]),
            ));
        } finally {
            (new Filesystem())->remove($project->getBaseDirectory());
        }

        $data = [
            'results' => $this->resultsRenderer->render($results, $exercise),
            'success' => $results->isSuccessful(),
        ];

        if ($results->isSuccessful()) {
            $this->studentState->completeExercise(
                $workshop->getCode(),
                $exercise->getName(),
            );
        }

        return $this->withJson($data, $response);
    }

    private function getStudent(): StudentDTO
    {
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            throw new \RuntimeException('Needs a logged in user');
        }

        return $student;
    }
}
