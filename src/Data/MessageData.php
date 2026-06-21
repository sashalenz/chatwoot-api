<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

use Spatie\LaravelData\Attributes\MapInputName;

/**
 * A message within a conversation. `messageType` is Chatwoot's integer enum:
 * 0 = incoming, 1 = outgoing, 2 = activity, 3 = template.
 */
final class MessageData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $contentAttributes
     * @param  array<int, array<string, mixed>>  $attachments
     */
    public function __construct(
        public ?int $id = null,
        public ?string $content = null,
        public ?int $accountId = null,
        public ?int $inboxId = null,
        public ?int $conversationId = null,
        public ?int $messageType = null,
        public ?string $contentType = null,
        public ?string $status = null,
        #[MapInputName('private')]
        public bool $isPrivate = false,
        public ?string $sourceId = null,
        public ?int $senderId = null,
        public ?string $senderType = null,
        public array $contentAttributes = [],
        public array $attachments = [],
        public ?SenderData $sender = null,
        public int|string|null $createdAt = null,
        public int|string|null $updatedAt = null,
    ) {}
}
