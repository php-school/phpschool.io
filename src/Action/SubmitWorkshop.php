<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\Workshop\EmailNotifier;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SubmitWorkshop
{
    use JsonUtils;

    private FormHandler $formHandler;
    private WorkshopCreator $workshopCreator;
    private EmailNotifier $emailNotifier;
    private LoggerInterface $logger;

    public function __construct(
        FormHandler $formHandler,
        WorkshopCreator $workshopCreator,
        EmailNotifier $emailNotifier,
        LoggerInterface $logger
    ) {
        $this->formHandler = $formHandler;
        $this->workshopCreator = $workshopCreator;
        $this->emailNotifier = $emailNotifier;
        $this->logger = $logger;
    }


    public function __invoke(Request $request, Response $response): Response
    {
        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        try {
            $workshop = $this->workshopCreator->create($this->formHandler->getData());
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
