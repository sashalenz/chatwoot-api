<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;

/**
 * A Chatwoot conversation. List/create responses populate a subset of fields;
 * the show endpoint additionally returns the embedded `messages`.
 */
final class ConversationData extends ChatwootData
{
    /**
     * @param  array<int, string>  $labels
     * @param  array<string, mixed>  $additionalAttributes
     * @param  array<string, mixed>  $customAttributes
     * @param  array<int, MessageData>  $messages
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?int $inboxId = null,
        public ?string $uuid = null,
        public ?string $status = null,
        public ?string $priority = null,
        public ?bool $canReply = null,
        public ?bool $muted = null,
        public ?int $unreadCount = null,
        public array $labels = [],
        public array $additionalAttributes = [],
        public array $customAttributes = [],
        #[DataCollectionOf(MessageData::class)]
        public array $messages = [],
        public array $meta = [],
        public int|string|null $snoozedUntil = null,
        public int|string|null $createdAt = null,
        public int|string|null $updatedAt = null,
        public int|string|null $lastActivityAt = null,
    ) {}
}
