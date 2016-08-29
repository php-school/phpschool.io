<?php

namespace PhpSchool\Website\Action;

use AdamWathan\Form\Facades\Form;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\Validator\SubmitWorkshop as SubmitWorkshopValidator;
use PhpSchool\Website\Workshop\EmailNotifier;
use Psr\Http\Message\ResponseInterface;
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
     * @var FormHandler
     */
    private $formHandler;

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

    public function showSubmitForm(Request $request, Response $response, PhpRenderer $renderer)
    {
        return $renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle'       => 'Submit your workshop',
            'pageDescription' => 'Submit your workshop to the workshop registry!',
            'content'         => $renderer->fetch('submit.phtml')
        ]);
    }

    public function submit(Request $request, Response $response)
    {
        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        try {
            $workshop = $this->workshopCreator->create($this->formHandler->getData());
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
