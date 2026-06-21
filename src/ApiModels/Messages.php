<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\MessageData;
use Sashalenz\ChatwootApi\Data\Paginated;
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
     *
     * @throws ChatwootApiException
     */
    public function create(int $conversationId, string $content, string $messageType = 'incoming', array $extra = []): MessageData
    {
        return MessageData::from(
            $this->httpPost($this->accountPath("conversations/{$conversationId}/messages"), [
                'content' => $content,
                'message_type' => $messageType,
                ...$extra,
            ])->all(),
        );
    }

    /**
     * List messages in a conversation (newest page first; paginate with
     * `before`/`after` message ids).
     *
     * @param  array<string,mixed>  $query  optional: ['before'=>…, 'after'=>…]
     * @return Paginated<MessageData>
     *
     * @throws ChatwootApiException
     */
    public function list(int $conversationId, array $query = []): Paginated
    {
        return Paginated::fromResponse(
            $this->httpGet($this->accountPath("conversations/{$conversationId}/messages"), $query)->all(),
            MessageData::class,
        );
    }

    /**
     * Delete a message from a conversation. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $conversationId, int $messageId): bool
    {
        $this->httpDelete($this->accountPath("conversations/{$conversationId}/messages/{$messageId}"));

        return true;
    }
}
