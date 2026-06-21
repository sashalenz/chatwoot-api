<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The sender of a message (a contact or an agent/user).
 */
final class SenderData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $thumbnail = null,
        public ?string $type = null,
        public ?string $availabilityStatus = null,
    ) {}
}
