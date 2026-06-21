<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A team (a group of agents).
 */
final class TeamData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?bool $allowAutoAssign = null,
        public ?bool $isMember = null,
    ) {}
}
