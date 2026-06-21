<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Data\PublicContactData;
use Sashalenz\ChatwootApi\Data\PublicConversationData;
use Sashalenz\ChatwootApi\Data\PublicInboxData;
use Sashalenz\ChatwootApi\Data\PublicMessageData;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;
use Sashalenz\ChatwootApi\Request;

/**
 * Chatwoot **Client API** (the public API-channel surface):
 * `public/api/v1/inboxes/{inbox_identifier}/…`.
 *
 * This is the canonical path for an API-channel integration that pushes a
 * customer's messages INTO Chatwoot as `incoming`. Auth is the inbox identifier
 * in the path — NO agent access token. Identity validation (HMAC) is optional
 * and only required when the inbox has it enabled.
 *
 * Flow (inbound mirror):
 *   $c   = ChatwootApi::client()->createContact(['name' => …, 'identifier' => …]);
 *   $sid = $c->get('source_id');
 *   $cv  = ChatwootApi::client()->createConversation($sid);
 *   ChatwootApi::client()->createMessage($sid, (int) $cv->get('id'), 'Привіт');
 */
final class PublicClient
{
    use Conditionable;

    private ?string $identifier = null;

    public function __construct(?string $identifier = null)
    {
        $this->identifier = $identifier;
    }

    public function identifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Read-only inbox info (also handy as a token/identifier health check).
     *
     * @throws ChatwootApiException
     */
    public function inbox(): PublicInboxData
    {
        return PublicInboxData::from($this->dispatch('GET', 'public/api/v1/inboxes/'.$this->resolveIdentifier())->all());
    }

    /**
     * Create (or upsert) a contact in the inbox → returns `source_id` +
     * `pubsub_token`. Pass `identifier` to make the contact addressable/stable
     * across sessions (e.g. the transport uid or CRM client ref).
     *
     * @param  array<string,mixed>  $attributes  e.g. ['identifier'=>…, 'name'=>…, 'phone_number'=>…, 'custom_attributes'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function createContact(array $attributes): PublicContactData
    {
        return PublicContactData::from($this->dispatch('POST', $this->base('contacts'), $attributes)->all());
    }

    /**
     * Fetch a contact by `source_id`.
     *
     * @throws ChatwootApiException
     */
    public function getContact(string $sourceId): PublicContactData
    {
        return PublicContactData::from($this->dispatch('GET', $this->base("contacts/{$sourceId}"))->all());
    }

    /**
     * Update a contact (name / custom_attributes refresh from CRM).
     *
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function updateContact(string $sourceId, array $attributes): PublicContactData
    {
        return PublicContactData::from($this->dispatch('PATCH', $this->base("contacts/{$sourceId}"), $attributes)->all());
    }

    /**
     * Open a conversation for a contact (by `source_id`).
     *
     * @param  array<string,mixed>  $extra
     *
     * @throws ChatwootApiException
     */
    public function createConversation(string $sourceId, array $extra = []): PublicConversationData
    {
        return PublicConversationData::from($this->dispatch('POST', $this->base("contacts/{$sourceId}/conversations"), $extra)->all());
    }

    /**
     * List the contact's conversations.
     *
     * @return Paginated<PublicConversationData>
     *
     * @throws ChatwootApiException
     */
    public function listConversations(string $sourceId): Paginated
    {
        return Paginated::fromResponse(
            $this->dispatch('GET', $this->base("contacts/{$sourceId}/conversations"))->all(),
            PublicConversationData::class,
        );
    }

    /**
     * Fetch a single conversation of the contact.
     *
     * @throws ChatwootApiException
     */
    public function getConversation(string $sourceId, int $conversationId): PublicConversationData
    {
        return PublicConversationData::from($this->dispatch('GET', $this->base("contacts/{$sourceId}/conversations/{$conversationId}"))->all());
    }

    /**
     * Push a customer message into a conversation (always `incoming`).
     *
     * @param  array<string,mixed>  $extra
     *
     * @throws ChatwootApiException
     */
    public function createMessage(string $sourceId, int $conversationId, string $content, array $extra = []): PublicMessageData
    {
        return PublicMessageData::from($this->dispatch(
            'POST',
            $this->base("contacts/{$sourceId}/conversations/{$conversationId}/messages"),
            ['content' => $content, ...$extra],
        )->all());
    }

    /**
     * List the messages of a conversation.
     *
     * @return Paginated<PublicMessageData>
     *
     * @throws ChatwootApiException
     */
    public function listMessages(string $sourceId, int $conversationId): Paginated
    {
        return Paginated::fromResponse(
            $this->dispatch('GET', $this->base("contacts/{$sourceId}/conversations/{$conversationId}/messages"))->all(),
            PublicMessageData::class,
        );
    }

    /**
     * Update a message (e.g. submit a CSAT rating or feedback on the
     * conversation's survey message).
     *
     * @param  array<string,mixed>  $attributes  e.g. ['submitted_values'=>['csat_survey_response'=>['rating'=>5, 'feedback_message'=>'…']]]
     *
     * @throws ChatwootApiException
     */
    public function updateMessage(string $sourceId, int $conversationId, int $messageId, array $attributes): PublicMessageData
    {
        return PublicMessageData::from($this->dispatch(
            'PATCH',
            $this->base("contacts/{$sourceId}/conversations/{$conversationId}/messages/{$messageId}"),
            $attributes,
        )->all());
    }

    /**
     * Push an incoming message carrying file attachments (customer media:
     * pictures, video, documents). Sent as multipart — each item becomes a
     * repeated `attachments[]` part. `$content` is the optional text/caption.
     *
     * @param  array<int,array{contents:string,filename?:string}>  $attachments  raw file bytes + filename
     *
     * @throws ChatwootApiException
     */
    public function createMessageWithAttachments(string $sourceId, int $conversationId, ?string $content, array $attachments): PublicMessageData
    {
        $parts = array_map(
            static fn (array $a): array => [
                'name' => 'attachments[]',
                'contents' => $a['contents'],
                'filename' => $a['filename'] ?? 'attachment',
            ],
            array_values($attachments),
        );

        $response = (new Request(
            'POST',
            $this->base("contacts/{$sourceId}/conversations/{$conversationId}/messages"),
            $content !== null && $content !== '' ? ['content' => $content] : [],
            [],
            $parts,
        ))->make();

        /** @var array<string,mixed> $json */
        $json = $response->json() ?? [];

        return PublicMessageData::from($json);
    }

    /**
     * Compute the identity-validation hash for a contact identifier. Only needed
     * when the inbox has identity validation enabled.
     *
     * @throws ChatwootApiException
     */
    public function identifierHash(string $contactIdentifier): string
    {
        $key = config('chatwoot-api.hmac_key');

        if (empty($key) || ! is_string($key)) {
            throw new ChatwootApiException('Chatwoot HMAC key is not configured (config chatwoot-api.hmac_key).');
        }

        return hash_hmac('sha256', $contactIdentifier, $key);
    }

    private function base(string $suffix): string
    {
        return 'public/api/v1/inboxes/'.$this->resolveIdentifier().'/'.ltrim($suffix, '/');
    }

    /**
     * @throws ChatwootApiException
     */
    private function resolveIdentifier(): string
    {
        $identifier = $this->identifier ?? config('chatwoot-api.identifier');

        if (empty($identifier) || ! is_string($identifier)) {
            throw new ChatwootApiException('Chatwoot inbox identifier is not configured (config chatwoot-api.identifier).');
        }

        return $identifier;
    }

    /**
     * @param  array<string,mixed>  $params
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    private function dispatch(string $method, string $path, array $params = []): Collection
    {
        $response = (new Request($method, $path, $params, []))->make();

        /** @var array<string,mixed> $json */
        $json = $response->json() ?? [];

        return collect($json);
    }
}
