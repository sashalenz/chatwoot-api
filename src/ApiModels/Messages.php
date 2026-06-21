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
        return $this->httpPost($this->accountPath("conversations/{$conversationId}/messages"), [
            'content' => $content,
            'message_type' => $messageType,
            ...$extra,
        ]);
    }

    /**
     * List messages in a conversation (newest page first; paginate with
     * `before`/`after` message ids).
     *
     * @param  array<string,mixed>  $query  optional: ['before'=>…, 'after'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(int $conversationId, array $query = []): Collection
    {
        return $this->httpGet($this->accountPath("conversations/{$conversationId}/messages"), $query);
    }

    /**
     * Delete a message from a conversation.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $conversationId, int $messageId): Collection
    {
        return $this->httpDelete($this->accountPath("conversations/{$conversationId}/messages/{$messageId}"));
    }
}
