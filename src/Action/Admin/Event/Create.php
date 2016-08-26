<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Validator\CreateEvent;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Filter\Exception\RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Create
{
    /**
     * @var EventRepository
     */
    private $repository;


    /**
     * @var Messages
     */
    private $messages;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(EventRepository $repository, PhpRenderer $renderer, Messages $messages)
    {
        $this->repository = $repository;
        $this->messages = $messages;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        if ($request->isGet()) {
            return $this->renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area - Create Event',
                'pageDescription' => 'Admin Area - Create Event',
                'content'         => $this->renderer->fetch('admin/event/create.phtml')
            ]);
        }

        $validator = new CreateEvent;
        if (!$validator->validateRequest($request)) {
            return $this->renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area - Create Event',
                'pageDescription' => 'Admin Area - Create Event',
                'content'         => $this->renderer->fetch('admin/event/create.phtml', [
                    'messages' => $validator->getMessages(),
                ])
            ]);
        }

        try {
            $values = $validator->getValues();
        } catch (RuntimeException $e) {
            return $this->renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area - Create Event',
                'pageDescription' => 'Admin Area - Create Event',
                'content'         => $this->renderer->fetch('admin/event/create.phtml', [
                    'messages' => [
                        'poster' => [
                            'There was a problem uploading the file. Please try again.'
                        ]
                    ]
                ])
            ]);
        }

        $event = new Event(
            $values['title'],
            $values['description'],
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
            ->withHeader('Location', '/admin/events/all');
    }
}
