<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\Validator\SubmitWorkshop as SubmitWorkshopValidator;
use PhpSchool\Website\Workshop\EmailNotifier;
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

    public function __construct(
        SubmitWorkshopValidator $submitWorkshopValidator,
        WorkshopCreator $workshopCreator,
        EmailNotifier $emailNotifier
    ) {
        $this->submitWorkshopValidator = $submitWorkshopValidator;
        $this->workshopCreator = $workshopCreator;
        $this->emailNotifier = $emailNotifier;
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
            //log
        }

        return $response->withJson([
            'success' => true,
        ]);
    }
}
