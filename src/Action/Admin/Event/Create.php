<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Laminas\Filter\Exception\RuntimeException;

/**
 * @phpstan-import-type EventData from \PhpSchool\Website\InputFilter\Event
 */
class Create
{
    use JsonUtils;

    private EventRepository $repository;

    /**
     * @var FormHandler<EventData>
     */
    private FormHandler $formHandler;

    /**
     * @param FormHandler<EventData> $formHandler
     */
    public function __construct(
        EventRepository $repository,
        FormHandler $formHandler,
    ) {
        $this->repository = $repository;
        $this->formHandler = $formHandler;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res->withStatus(400);
        }

        try {
            $values = $this->formHandler->getData();
        } catch (RuntimeException $e) {
            return $this->withJson(
                [
                    'success' => false,
                    'form_errors' => ['poster' => 'There was a problem uploading the file. Please try again.']
                ],
                $response
            );
        }

        $date = \DateTime::createFromFormat('Y-m-d\TH:i', $values['date']);

        if (false === $date) {
            return $this->withJson(
                [
                    'success' => false,
                    'form_errors' => ['date' => 'Invalid date format']
                ],
                $response
            );
        }

        $event = new Event(
            $values['name'],
            $values['description'],
            $values['link'] ?? null,
            $date,
            $values['venue'],
            isset($values['poster']['tmp_name']) ? basename($values['poster']['tmp_name']) : null
        );

        $this->repository->save($event);

        return $this->jsonSuccess($response);
    }
}
