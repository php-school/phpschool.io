<?php

namespace PhpSchool\Website\Form;

use AdamWathan\BootForms\BasicFormBuilder;
use AdamWathan\BootForms\BootForm;
use AdamWathan\BootForms\HorizontalFormBuilder;
use AdamWathan\Form\FormBuilder;
use Psr\Http\Message\UploadedFileInterface;
use RKA\Session;
use Zend\InputFilter\InputFilter;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class FormHandler
{
    /**
     * @var InputFilter
     */
    private $inputFilter;

    /**
     * @var Session
     */
    private $session;

    public function __construct(InputFilter $inputFilter, Session $session)
    {
        $this->inputFilter = $inputFilter;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool|Response
     */
    public function validateAndRedirectIfErrors(Request $request, Response $response)
    {
        if ($this->validateRequest($request)) {
            return true;
        }

        $this->session->set('__old_input', $request->getParsedBody());
        $this->session->set('__errors', $this->inputFilter->getMessages());

        return $response->withRedirect($request->getHeaderLine('referer'));
    }

    public function redirectWithErrors(Request $request, Response $response, array $errors)
    {
        $this->session->set('__old_input', $request->getParsedBody());
        $this->session->set('__errors', $errors);

        return $response->withRedirect($request->getHeaderLine('referer'));
    }

    public function validateJsonRequest(Request $request, Response $response)
    {
        if ($this->validateRequest($request)) {
            return true;
        }

        return $response->withJson([
           'success'        => false,
           'form_errors'    => $this->inputFilter->getMessages(),
        ]);
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

    /**
     * @param $bind
     * @return BootForm
     */
    public function getForm($bind = null)
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

    public function getPreviousErrors()
    {
        $errors = $this->session->get('__errors', []);
        $this->session->delete('__errors');
        return $errors;
    }

    public function getData()
    {
        return $this->inputFilter->getValues();
    }
}
