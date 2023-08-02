<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Factory\CommandFactory\Strategy\Command;

use App\PullRequestDashboard\Application\Command\MovePullRequestCardToColumnByLabelCommand;
use App\Shared\Infrastructure\Factory\CommandFactory\CommandStrategyInterface;

class PullRequestReadyForReviewStrategy implements CommandStrategyInterface
{
    public function __construct(
        private readonly string $pullRequestDashboardNumber,
        private readonly string $readyForReviewColumnName,
    ) {
    }

    /**
     * If the PR is draft, or the repo is excluded, or the PR has a label excluded, we don't want to move the card.
     *
     * @param array{
     *     action: string,
     *     pull_request: array{
     *          base: array{
     *             repo: array{
     *                 name: string,
     *             },
     *          },
     *          draft: bool,
     *     }
     * } $payload
     */
    public function supports(string $eventType, array $payload): bool
    {
        return 'pull_request' === $eventType
            and in_array($payload['action'], ['opened', 'ready_for_review'])
            and false === $payload['pull_request']['draft'];
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
                $this->readyForReviewColumnName,
            ),
        ];
    }
}
