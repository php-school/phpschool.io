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
     * @var WorkshopState
     */
    private array $workshopState;

    /**
     * @param WorkshopState $workshopState
     */
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
        /** @var int $total */
        $total = collect($this->workshopState)
            ->pluck('completedExercises')
            ->map(fn(array $completed) => count($completed))
            ->sum();

        return $total;
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
            'total_completed' => $this->getTotalCompletedExercises()
        ];
    }
}
