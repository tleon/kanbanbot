<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Factory\CommandFactory\Strategy\Command;

use App\PullRequestDashboard\Application\Command\MovePullRequestCardToColumnByLabelCommand;
use App\Shared\Infrastructure\Factory\CommandFactory\CommandStrategyInterface;

class PullRequestReopenedStrategy implements CommandStrategyInterface
{
    public function __construct(
        private readonly string $pullRequestDashboardNumber,
        private readonly string $reopenedColumnName,
    ) {
    }

    /**
     * @param array{
     *     action: string,
     * } $payload
     */
    public function supports(string $eventType, array $payload): bool
    {
        return 'pull_request' === $eventType and 'reopened' === $payload['action'];
    }

    /**
     * @param array{
     *     pull_request: array{
     *         base: array{
     *             repo: array{
     *                 name: string,
     *                 owner: array{
     *                     login: string
     *                 }
     *             }
     *         },
     *         number: int,
     *     },
     * } $payload
     *
     * @return array<MovePullRequestCardToColumnByLabelCommand>
     */
    public function createCommandsFromPayload(array $payload): array
    {
        return [
            new MovePullRequestCardToColumnByLabelCommand(
                $this->pullRequestDashboardNumber,
                $payload['pull_request']['base']['repo']['owner']['login'],
                $payload['pull_request']['base']['repo']['name'],
                (string) $payload['pull_request']['number'],
                $this->reopenedColumnName,
            ),
        ];
    }
}
