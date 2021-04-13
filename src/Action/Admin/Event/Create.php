<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Laminas\Filter\Exception\RuntimeException;

class Create
{
    private EventRepository $repository;
    private FormHandler $formHandler;
    private PhpRenderer $renderer;
    private Messages $messages;

    public function __construct(
        EventRepository $repository,
        FormHandler $formHandler,
        PhpRenderer $renderer,
        Messages $messages
    ) {
        $this->repository = $repository;
        $this->messages = $messages;
        $this->renderer = $renderer;
        $this->formHandler = $formHandler;
    }

    public function showCreateForm(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'Admin Area - Create Event',
            'pageDescription' => 'Admin Area - Create Event',
            'content'         => $this->renderer->fetch(
                'admin/event/create.phtml',
                ['form' => $this->formHandler->getForm()]
            )
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        $res = $this->formHandler->validateAndRedirectIfErrors($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        try {
            $values = $this->formHandler->getData();
        } catch (RuntimeException $e) {
            return $this->formHandler->redirectWithErrors(
                $request,
                $response,
                [ 'poster' => [
                    'There was a problem uploading the file. Please try again.'
                ]]
            );
        }

        $event = new Event(
            $values['name'],
            $values['description'],
            $values['link'] ?? null,
            \DateTime::createFromFormat('Y-m-d\TH:i', $values['date']),
            $values['venue'],
            $values['poster']['tmp_name'] ? basename($values['poster']['tmp_name']) : null
        );

        $this->repository->save($event);

        $this->messages->addMessage(
            'admin.success',
            sprintf('Successfully added event %s', $event->getName())
        );

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/event/all');
    }
}
