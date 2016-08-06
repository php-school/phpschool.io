<?php

namespace PhpSchool\Website\Action;

use Github\Client;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\Validator\SubmitWorkshop as SubmitWorkshopValidator;
use PhpSchool\Website\Validator\WorkshopComposerJson as WorkshopComposerJsonValidator;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Callback;
use Zend\Validator\Regex;

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

    public function __construct(
        SubmitWorkshopValidator $submitWorkshopValidator,
        WorkshopCreator $workshopCreator
    ) {
        $this->submitWorkshopValidator = $submitWorkshopValidator;
        $this->workshopCreator = $workshopCreator;
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
            $this->workshopCreator->create($this->submitWorkshopValidator->getValues());
        } catch (WorkshopCreationException $e) {
            return $response->withJson([
               'success' => false,
               'workshop_errors' => $e->getErrors()
           ]);
        }

        return $response->withJson([
            'success' => true,
        ]);
    }
}
