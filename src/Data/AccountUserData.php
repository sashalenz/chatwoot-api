<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A user ↔ account membership (Platform API).
 */
final class AccountUserData extends ChatwootData
{
    public function __construct(
        public ?int $accountId = null,
        public ?int $userId = null,
        public ?string $role = null,
    ) {}
}
