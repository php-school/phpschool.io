<?php

namespace PhpSchool\Website\Online;

use League\CommonMark\MarkdownConverterInterface;
use PhpSchool\PhpWorkshop\Exception\ProblemFileDoesNotExistException;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;

class ProblemFileConverter
{
    private MarkdownConverterInterface $commonMarkConverter;

    public function __construct(MarkdownConverterInterface $commonMarkConverter)
    {
        $this->commonMarkConverter = $commonMarkConverter;
    }

    public function htmlFromExercise(ExerciseInterface $exercise): string
    {
        $problemFile = $exercise->getProblem();
        if (!is_readable($problemFile)) {
            throw ProblemFileDoesNotExistException::fromFile($problemFile);
        }

        return $this->commonMarkConverter->convertToHtml((string) file_get_contents($problemFile));
    }
}
