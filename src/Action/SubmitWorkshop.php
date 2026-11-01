<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\Workshop\EmailNotifier;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @phpstan-import-type SubmitWorkshopData from \PhpSchool\Website\InputFilter\SubmitWorkshop
 */
class SubmitWorkshop
{
    use JsonUtils;

    /**
     * @param FormHandler<SubmitWorkshopData> $formHandler
     */
    public function __construct(
        private readonly FormHandler $formHandler,
        private readonly WorkshopCreator $workshopCreator,
        private readonly EmailNotifier $emailNotifier,
        private readonly LoggerInterface $logger,
        private readonly SessionStorageInterface $session,
    ) {}


    public function __invoke(Request $request, Response $response): Response
    {
        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        /** @var StudentDTO $student */
        $student = $this->session->get('student');

        try {
            $workshop = $this->workshopCreator->create(
                [...$this->formHandler->getData(), 'email' => $student->email, 'name' => $student->name],
            );
        } catch (WorkshopCreationException $e) {
            return $this->withJson(['success' => false, 'workshop_errors' => $e->getErrors()], $response);
        }

        try {
            $this->emailNotifier->new($workshop);
        } catch (RuntimeException $e) {
            $this->logger->error(sprintf('Email could not be sent. Error: "%s"', $e->getMessage()));
        }
        return $this->jsonSuccess($response);
    }
}
