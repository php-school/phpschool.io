<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\Validator\SubmitWorkshop as SubmitWorkshopValidator;
use PhpSchool\Website\Workshop\EmailNotifier;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class SubmitWorkshop
{
    /**
     * @var SubmitWorkshopValidator
     */
    private $submitWorkshopValidator;

    /**
     * @var WorkshopCreator
     */
    private $workshopCreator;

    /**
     * @var EmailNotifier
     */
    private $emailNotifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        SubmitWorkshopValidator $submitWorkshopValidator,
        WorkshopCreator $workshopCreator,
        EmailNotifier $emailNotifier,
        LoggerInterface $logger
    ) {
        $this->submitWorkshopValidator = $submitWorkshopValidator;
        $this->workshopCreator = $workshopCreator;
        $this->emailNotifier = $emailNotifier;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $phpRenderer)
    {
        if (!$this->submitWorkshopValidator->validateRequest($request)) {
            return $response->withJson([
                'success' => false,
                'form_errors' => $this->submitWorkshopValidator->getMessages(),
            ]);
        }

        try {
            $workshop = $this->workshopCreator->create($this->submitWorkshopValidator->getValues());
        } catch (WorkshopCreationException $e) {
            return $response->withJson([
               'success' => false,
               'workshop_errors' => $e->getErrors()
           ]);
        }

        try {
            $this->emailNotifier->new($workshop);
        } catch (RuntimeException $e) {
            $this->logger->error(sprintf('Email could not be sent. Error: "%s"', $e->getMessage()));
        }

        return $response->withJson([
            'success' => true,
        ]);
    }
}
