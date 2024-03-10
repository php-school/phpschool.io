<?php

namespace PhpSchool\Website\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Action\RedirectUtils;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\User\Session;

/**
 * @template TFilteredValues of array
 */
class FormHandler
{
    use RedirectUtils;
    use JsonUtils;

    /**
     * @var InputFilter<TFilteredValues>
     */
    private InputFilter $inputFilter;
    private Session $session;

    /**
     * @param InputFilter<TFilteredValues> $inputFilter
     */
    public function __construct(InputFilter $inputFilter, Session $session)
    {
        $this->inputFilter = $inputFilter;
        $this->session = $session;
    }

    /**
     * @return bool|MessageInterface
     */
    public function validateAndRedirectIfErrors(Request $request, Response $response)
    {
        if ($this->validateRequest($request)) {
            return true;
        }

        $this->session->set('__old_input', (array) $request->getParsedBody());
        $this->session->set('__errors', $this->inputFilter->getMessages());

        return $this->redirect($request->getHeaderLine('referer'));
    }

    /**
     * @return bool|MessageInterface
     */
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

    public function validateRequest(Request $request): bool
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

        $this->inputFilter->setData(array_merge_recursive((array) $request->getParsedBody(), $files));
        return $this->inputFilter->isValid();
    }

    /**
     * @return TFilteredValues
     */
    public function getData(): array
    {
        return $this->inputFilter->getValues();
    }
}
