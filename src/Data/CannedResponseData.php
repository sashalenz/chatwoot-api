<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A canned response (saved reply addressed by a short code).
 */
final class CannedResponseData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $shortCode = null,
        public ?string $content = null,
        public int|string|null $createdAt = null,
        public int|string|null $updatedAt = null,
    ) {}
}
