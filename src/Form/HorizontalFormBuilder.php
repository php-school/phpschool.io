<?php

namespace PhpSchool\Website\Form;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class HorizontalFormBuilder extends \AdamWathan\BootForms\HorizontalFormBuilder
{
    public function dateTimeLocal($label, $name, $value = null)
    {
        $control = $this->builder->dateTimeLocal($name)->value($value);

        return $this->formGroup($label, $name, $control);
    }
}
