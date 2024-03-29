<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Result\FailureInterface;
use PhpSchool\PhpWorkshop\Result\ResultGroupInterface;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Result\SuccessInterface;
use PhpSchool\PhpWorkshop\ResultAggregator;

/**
 * @phpstan-type InnerResult array{
 *      success: bool,
 *      name: string,
 *      type?: class-string
 * }
 * @phpstan-type Result array{
 *     success: bool,
 *     name: string,
 *     type?: class-string,
 *     results?: array<InnerResult>
 * }
 */
class VueResultsRenderer
{
    /**
     * @return array<Result>
     */
    public function render(
        ResultAggregator $results,
        ExerciseInterface $exercise
    ): array {
        return array_map(
            function (ResultInterface $result) {
                return $this->renderResult($result);
            },
            $results->getIterator()->getArrayCopy()
        );
    }

    private function isSuccess(ResultInterface $result): bool
    {
        return $result instanceof SuccessInterface ||
            ($result instanceof ResultGroupInterface && $result->isSuccessful());
    }

    private function isResultGroup(ResultInterface $result): bool
    {
        return $result instanceof ResultGroupInterface;
    }

    /**
     * @return Result
     */
    private function renderResult(ResultInterface $result): array
    {
        if ($this->isSuccess($result)) {
            return [
                'success' => true,
                'name' => $result->getCheckName()
            ];
        }

        if ($this->isResultGroup($result)) {
            /** @var ResultGroupInterface $result */
            /** @var array<InnerResult> $results */
            $results = array_map(
                function (ResultInterface $innerResult) use ($result) {

                    if ($result->isResultSuccess($innerResult)) {
                        return [
                            'success' => true,
                            'name' => $innerResult->getCheckName(),
                        ];
                    }

                    /** @var FailureInterface $innerResult  */
                    return array_merge(
                        [
                            'success' => false,
                            'name' => $innerResult->getCheckName(),
                            'type' => $innerResult::class,
                        ],
                        $innerResult->toArray()
                    );
                },
                $result->getResults()
            );

            return [
                'success' => false,
                'name' => $result->getCheckName(),
                'type' => $result::class,
                'results' => $results
            ];
        }

        /** @var FailureInterface $result */
        /** @var InnerResult $failure */
        $failure = array_merge(
            [
                'success' => false,
                'name' => $result->getCheckName(),
                'type' => $result::class,
            ],
            $result->toArray()
        );

        return $failure;
    }
}
