<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The Client API view of a contact (carries the `sourceId` used to address it).
 */
final class PublicContactData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?string $sourceId = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $pubsubToken = null,
    ) {}
}
