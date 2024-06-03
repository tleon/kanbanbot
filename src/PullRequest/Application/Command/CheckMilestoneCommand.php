<?php

declare(strict_types=1);

namespace App\PullRequest\Application\Command;

class CheckMilestoneCommand
{
    public function __construct(
        public readonly string $repositoryOwner,
        public readonly string $repositoryName,
        public readonly string $pullRequestNumber,
    ) {
    }
}