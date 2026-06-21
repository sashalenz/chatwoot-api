<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;

/**
 * The Client API view of a conversation.
 */
final class PublicConversationData extends ChatwootData
{
    /**
     * @param  array<int, PublicMessageData>  $messages
     * @param  array<string, mixed>|null  $contact
     */
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public ?int $inboxId = null,
        public ?string $status = null,
        public int|string|null $contactLastSeenAt = null,
        public int|string|null $agentLastSeenAt = null,
        #[DataCollectionOf(PublicMessageData::class)]
        public array $messages = [],
        public ?array $contact = null,
    ) {}
}
