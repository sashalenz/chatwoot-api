<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Messages.
 *
 * @see https://developers.chatwoot.com/api-reference/messages-api
 */
final class Messages extends BaseModel
{
    /**
     * Create a message in a conversation.
     *
     * `message_type`:
     *   - `incoming` — mirror of what the customer sent (inbound bridge);
     *   - `outgoing` — an agent/bot reply.
     *
     * @param  array<string,mixed>  $extra  optional: ['private'=>true, 'content_type'=>'text', 'content_attributes'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(int $conversationId, string $content, string $messageType = 'incoming', array $extra = []): Collection
    {
        return $this->post($this->accountPath("conversations/{$conversationId}/messages"), [
            'content' => $content,
            'message_type' => $messageType,
            ...$extra,
        ]);
    }
}
