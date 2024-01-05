<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Laminas\Filter\Exception\RuntimeException;

class Update
{
    use JsonUtils;

    public function __construct(
        private readonly EventRepository $repository,
        private readonly FormHandler $formHandler,
        private readonly PhpRenderer $renderer,
    ) {
    }

    public function __invoke(Request $request, Response $response, string $id): MessageInterface
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $this->withJson(
                [
                    'error' => 'Could not find event with id: ' . $id
                ],
                $response,
                404
            );
        }

        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
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

        $event->setName($values['name'])
            ->setDescription($values['description'])
            ->setLink($values['link'])
            ->setDateTime(\DateTime::createFromFormat('Y-m-d\TH:i', $values['date']))
            ->setVenue($values['venue'])
            ->setPoster(isset($values['poster']['tmp_name']) ? basename($values['poster']['tmp_name']) : $event->getPoster());

        $this->repository->save($event);

        return $this->jsonSuccess($response);
    }
}
