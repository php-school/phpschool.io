<?php

namespace PhpSchool\Website\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Zend\InputFilter\InputFilter;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Validator extends InputFilter
{
    public function validateRequest(ServerRequestInterface $request) : bool
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

        $this->setData(array_merge_recursive($request->getParsedBody(), $files));
        return $this->isValid();
    }

    public function validateArray(array $data = []) : bool
    {
        $this->setData($data);
        return $this->isValid();
    }
}
