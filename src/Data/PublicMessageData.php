<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The Client API view of a message.
 */
final class PublicMessageData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $contentAttributes
     * @param  array<int, array<string, mixed>>  $attachments
     * @param  array<string, mixed>|null  $sender
     */
    public function __construct(
        public ?int $id = null,
        public ?string $content = null,
        public int|string|null $messageType = null,
        public ?string $contentType = null,
        public ?int $conversationId = null,
        public array $contentAttributes = [],
        public array $attachments = [],
        public ?array $sender = null,
        public int|string|null $createdAt = null,
    ) {}
}
