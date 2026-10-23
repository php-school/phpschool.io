<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use League\CommonMark\MarkdownConverterInterface;
use PhpSchool\PhpWorkshop\Exception\ProblemFileDoesNotExistException;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;

class ProblemFileConverter
{
    public function __construct(
        private readonly MarkdownConverterInterface $commonMarkConverter,
    ) {}

    public function htmlFromExercise(ExerciseInterface $exercise): string
    {
        $problemFile = $exercise->getProblem();
        if (!is_readable($problemFile)) {
            throw ProblemFileDoesNotExistException::fromFile($problemFile);
        }

        return (string) $this->commonMarkConverter->convertToHtml((string) file_get_contents($problemFile));
    }
}
