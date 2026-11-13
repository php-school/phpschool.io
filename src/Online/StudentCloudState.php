<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\UserState\UserState;

/**
 * @phpstan-type WorkshopState array<string, array{completedExercises: array<string>, currentExercise: string|null}>
 * @phpstan-type StudentState array{}|array{workshops: WorkshopState, total_completed: int}
 */
class StudentCloudState implements \JsonSerializable
{
    /**
     * @param WorkshopState $workshopState
     */
    public function __construct(
        private readonly array $workshopState,
    ) {}

    public function getStateForWorkshop(string $workshop): UserState
    {
        $workshopState = $this->workshopState[$workshop] ?? null;

        if ($workshopState) {
            return new UserState($workshopState['completedExercises'], $workshopState['currentExercise']);
        }

        return new UserState();
    }

    public function getTotalCompletedExercises(): int
    {
        return array_sum(array_map(
            fn(array $workshop) => count($workshop['completedExercises']),
            $this->workshopState,
        ));
    }

    /**
     * @return array{
     *     workshops: WorkshopState,
     *     total_completed: int
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'workshops' => $this->workshopState,
            'total_completed' => $this->getTotalCompletedExercises(),
        ];
    }
}
