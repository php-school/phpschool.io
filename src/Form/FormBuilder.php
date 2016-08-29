<?php

namespace PhpSchool\Website\Form;

use PhpSchool\Website\Form\Element\DateTimeLocal;


/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class FormBuilder extends \AdamWathan\Form\FormBuilder
{
    public function dateTimeLocal($name)
    {
        $date = new DateTimeLocal($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        return $date;
    }
}
