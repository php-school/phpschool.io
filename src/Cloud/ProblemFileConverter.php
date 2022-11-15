<?php

namespace PhpSchool\Website\Cloud;

use League\CommonMark\CommonMarkConverter;
use PhpSchool\PhpWorkshop\Exception\ProblemFileDoesNotExistException;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;

class ProblemFileConverter
{
    private CommonMarkConverter $commonMarkConverter;

    public function __construct(CommonMarkConverter $commonMarkConverter)
    {
        $this->commonMarkConverter = $commonMarkConverter;
    }

    public function htmlFromExercise(ExerciseInterface $exercise): string
    {
        $problemFile = $exercise->getProblem();
        if (!is_readable($problemFile)) {
            throw ProblemFileDoesNotExistException::fromFile($problemFile);
        }

        return $this->commonMarkConverter->convertToHtml(file_get_contents($problemFile));
    }
}
