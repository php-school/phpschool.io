<?php

namespace PhpSchool\Website\Form;

use AdamWathan\BootForms\BasicFormBuilder;
use AdamWathan\BootForms\BootForm;
use AdamWathan\BootForms\HorizontalFormBuilder;
use AdamWathan\Form\FormBuilder;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Action\RedirectUtils;
use Psr\Http\Message\UploadedFileInterface;
use Laminas\InputFilter\InputFilter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\User\Session;

class FormHandler
{
    use RedirectUtils;
    use JsonUtils;

    private InputFilter $inputFilter;
    private Session $session;

    public function __construct(InputFilter $inputFilter, Session $session)
    {
        $this->inputFilter = $inputFilter;
        $this->session = $session;
    }

    /**
     * @return bool|Response
     */
    public function validateAndRedirectIfErrors(Request $request, Response $response)
    {
        if ($this->validateRequest($request)) {
            return true;
        }

        $this->session->set('__old_input', $request->getParsedBody());
        $this->session->set('__errors', $this->inputFilter->getMessages());

        return $this->redirect($request->getHeaderLine('referer'));
    }

    public function redirectWithErrors(Request $request, Response $response, array $errors): Response
    {
        $this->session->set('__old_input', $request->getParsedBody());
        $this->session->set('__errors', $errors);

        return $this->redirect($request->getHeaderLine('referer'));
    }

    public function validateJsonRequest(Request $request, Response $response)
    {
        if ($this->validateRequest($request)) {
            return true;
        }

        return $this->withJson([
            'success' => false,
            'form_errors' => $this->inputFilter->getMessages(),
        ], $response);
    }

    public function validateRequest(Request $request) : bool
    {
        $files = collect($request->getUploadedFiles())
            //filter out missing files
            ->reject(function (UploadedFileInterface $file) {
                return $file->getError() === UPLOAD_ERR_NO_FILE;
            })
            ->map(function (UploadedFileInterface $file) {
                return [
                    'tmp_name' => $file->getStream()->getMetadata('uri'),
                    'name' => $file->getClientFilename(),
                    'type' => $file->getClientMediaType(),
                    'size' => $file->getSize(),
                    'error' => $file->getError()
                ];
            })
            ->all();

        $this->inputFilter->setData(array_merge_recursive($request->getParsedBody(), $files));
        return $this->inputFilter->isValid();
    }

    public function getForm($bind = null): BootForm
    {
        $formBuilder = new FormBuilder;
        $formBuilder->setOldInputProvider(new OldInput($this->session->get('__old_input', [])));
        $formBuilder->setErrorStore(new ErrorStore($this->session->get('__errors', [])));

        if (null !== $bind) {
            $formBuilder->bind($bind);
        }

        $this->session->delete('__old_input');
        $this->session->delete('__errors');

        $basicBootFormsBuilder = new BasicFormBuilder($formBuilder);
        $horizontalBootFormsBuilder = new HorizontalFormBuilder($formBuilder);
        return new BootForm($basicBootFormsBuilder, $horizontalBootFormsBuilder);
    }

    public function getPreviousErrors(): array
    {
        $errors = $this->session->get('__errors', []);
        $this->session->delete('__errors');
        return $errors;
    }

    public function getData(): array
    {
        return $this->inputFilter->getValues();
    }
}
