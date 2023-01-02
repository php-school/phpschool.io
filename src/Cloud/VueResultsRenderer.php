<?php

namespace PhpSchool\Website\Cloud;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Result\FailureInterface;
use PhpSchool\PhpWorkshop\Result\ResultGroupInterface;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Result\SuccessInterface;
use PhpSchool\PhpWorkshop\ResultAggregator;

class VueResultsRenderer
{
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
            return [
                'success' => false,
                'name' => $result->getCheckName(),
                'type' => $result::class,
                'results' => array_map(
                    function (ResultInterface $result) {

                        if (!$result instanceof FailureInterface) {
                            return [
                                'success' => true,
                                'name' => $result->getCheckName(),
                            ];
                        }

                        return array_merge(
                            [
                                'success' => false,
                                'name' => $result->getCheckName(),
                                'type' => $result::class,
                            ],
                            $result->toArray()
                        );
                    },
                    $result->getResults()
                )
            ];
        }

        /** @var FailureInterface $result */
        return array_merge(
            [
                'success' => false,
                'name' => $result->getCheckName(),
                'type' => $result::class,
            ],
            $result->toArray()
        );
    }
}
