<?php

namespace PhpSchool\Website\Form;

use AdamWathan\Form\OldInput\OldInputInterface;

class OldInput implements OldInputInterface
{
    private array $oldInput;

    public function __construct(array $oldInput = [])
    {
        $this->oldInput = $oldInput;
    }

    public function setOldInput(array $oldInput): void
    {
        $this->oldInput = $oldInput;
    }

    public function hasOldInput(): bool
    {
        return count($this->oldInput) > 0;
    }

    /**
     * @param string $key
     */
    public function getOldInput($key): ?string
    {
        return $this->oldInput[$key] ?? null;
    }
}
