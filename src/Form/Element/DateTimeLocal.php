<?php

namespace PhpSchool\Website\Form\Element;

use AdamWathan\Form\Elements\Text;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DateTimeLocal extends Text
{
    protected $attributes = [
        'type' => 'datetime-local',
    ];

    public function value($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d\TH:i');
        }

        return parent::value($value);
    }
}
