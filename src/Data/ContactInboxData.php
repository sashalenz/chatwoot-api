<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A contact ↔ inbox association. `sourceId` is the stable key used by the
 * Application/Client APIs to address the contact within the inbox.
 */
final class ContactInboxData extends ChatwootData
{
    /**
     * @param  array<string, mixed>|null  $inbox
     */
    public function __construct(
        public ?string $sourceId = null,
        public ?array $inbox = null,
    ) {}
}
