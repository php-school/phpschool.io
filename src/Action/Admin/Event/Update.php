<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Laminas\Filter\Exception\RuntimeException;

class Update
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

    public function showUpdateForm(Request $request, Response $response, string $id)
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/event/all');
        }

        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'Admin Area - Update Event',
            'pageDescription' => 'Admin Area - Update Event',
            'content'         => $this->renderer->fetch(
                'admin/event/update.phtml',
                [
                    'form'  => $this->formHandler->getForm($event->toArray()),
                    'event' => $event
                ]
            )
        ]);
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/event/all');
        }

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

        $event->setName($values['name'])
            ->setDescription($values['description'])
            ->setLink($values['link'])
            ->setDateTime(\DateTime::createFromFormat('Y-m-d\TH:i', $values['date']))
            ->setVenue($values['venue'])
            ->setPoster(isset($values['poster']['tmp_name']) ? basename($values['poster']['tmp_name']) : $event->getPoster());

        $this->repository->save($event);

        $this->messages->addMessage(
            'admin.success',
            sprintf('Event %s was successfully updated.', $event->getName())
        );

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/event/all');
    }
}
