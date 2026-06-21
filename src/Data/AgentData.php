<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * An agent (an account user with an agent/administrator role).
 */
final class AgentData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $name = null,
        public ?string $availableName = null,
        public ?string $email = null,
        public ?string $role = null,
        public ?string $availabilityStatus = null,
        public ?bool $confirmed = null,
        public ?bool $autoOffline = null,
        public ?string $thumbnail = null,
        public ?int $customRoleId = null,
    ) {}
}
