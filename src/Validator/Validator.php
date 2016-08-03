<?php

namespace PhpSchool\Website\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Zend\InputFilter\InputFilter;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Validator extends InputFilter
{
    public function validateRequest(ServerRequestInterface $request) : bool
    {
        $this->setData($request->getParsedBody());
        return $this->isValid();
    }

    public function validateArray(array $data = []) : bool
    {
        $this->setData($data);
        return $this->isValid();
    }
}
