<?php

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\UserState\UserState;

class StudentCloudState implements \JsonSerializable
{
    private array $workshopState;

    public function __construct(array $workshopState)
    {
        $this->workshopState = $workshopState;
    }

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
        return collect($this->workshopState)
            ->pluck('completedExercises')
            ->map(fn (array $completed) => count($completed))
            ->sum();
    }

    public function jsonSerialize(): array
    {
        return [
            'workshops' => $this->workshopState,
            'total_completed' => $this->getTotalCompletedExercises()
        ];
    }
}
