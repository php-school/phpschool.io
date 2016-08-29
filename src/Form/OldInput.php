<?php

namespace PhpSchool\Website\Form;

use AdamWathan\Form\OldInput\OldInputInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class OldInput implements OldInputInterface
{
    /**
     * @var array
     */
    private $oldInput;

    public function __construct(array $oldInput = [])
    {
        $this->oldInput = $oldInput;
    }

    public function setOldInput(array $oldInput)
    {
        $this->oldInput = $oldInput;
    }

    public function hasOldInput()
    {
        return count($this->oldInput) > 0;
    }

    public function getOldInput($key)
    {
        return $this->oldInput[$key] ?? null;
    }
}
