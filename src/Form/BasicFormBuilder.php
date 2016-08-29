<?php

namespace PhpSchool\Website\Form;

class BasicFormBuilder extends \AdamWathan\BootForms\BasicFormBuilder
{
    public function dateTimeLocal($label, $name, $value = null)
    {
        $control = $this->builder->dateTimeLocal($name)->value($value);

        return $this->formGroup($label, $name, $control);
    }
}
