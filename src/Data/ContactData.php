<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Chatwoot contact. `sourceId` is populated by the create endpoint (lifted
 * from the returned `contact_inbox`) for convenience.
 */
final class ContactData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $additionalAttributes
     * @param  array<string, mixed>  $customAttributes
     * @param  array<int, array<string, mixed>>  $contactInboxes
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $identifier = null,
        public ?string $thumbnail = null,
        public ?bool $blocked = null,
        public ?string $availabilityStatus = null,
        public ?string $sourceId = null,
        public array $additionalAttributes = [],
        public array $customAttributes = [],
        public array $contactInboxes = [],
        public int|string|null $lastActivityAt = null,
        public int|string|null $createdAt = null,
    ) {}
}
