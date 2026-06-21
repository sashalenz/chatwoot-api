<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A parsed inbound Chatwoot webhook event.
 *
 * Chatwoot POSTs events to your configured Webhook URL with a top-level `event`
 * discriminator (`message_created`, `message_updated`, `conversation_created`,
 * `conversation_updated`, `conversation_status_changed`, `contact_created`,
 * `contact_updated`, …). The rest of the body IS the subject entity: for
 * message events the body is the message (with a nested `conversation` and
 * `sender`); for conversation events the body is the conversation; for contact
 * events the body is the contact.
 *
 * Usage in a controller:
 *
 *   $event = WebhookEvent::fromArray($request->all());
 *   if ($event->isMessageCreated() && $event->isIncoming()) {
 *       $msg = $event->message();          // MessageData
 *       $conv = $event->conversation();    // ConversationData|null
 *   }
 *
 * The raw payload is always available via {@see WebhookEvent::raw()} for events
 * or fields this helper does not model.
 */
final class WebhookEvent
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly ?string $event,
        public readonly array $payload,
    ) {}

    /**
     * @param  array<string, mixed>  $payload  the decoded webhook body (e.g. $request->all())
     */
    public static function fromArray(array $payload): self
    {
        $event = $payload['event'] ?? null;

        return new self(
            event: is_string($event) ? $event : null,
            payload: $payload,
        );
    }

    /**
     * Parse a raw JSON webhook body.
     */
    public static function fromJson(string $json): self
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json, true) ?: [];

        return self::fromArray($decoded);
    }

    public function isMessageCreated(): bool
    {
        return $this->event === 'message_created';
    }

    public function isMessageUpdated(): bool
    {
        return $this->event === 'message_updated';
    }

    public function isConversationCreated(): bool
    {
        return $this->event === 'conversation_created';
    }

    public function isConversationUpdated(): bool
    {
        return $this->event === 'conversation_updated';
    }

    public function isConversationStatusChanged(): bool
    {
        return $this->event === 'conversation_status_changed';
    }

    /**
     * True for message events that carry an incoming (customer-sent) message.
     */
    public function isIncoming(): bool
    {
        return $this->normalizedMessageType() === 'incoming';
    }

    /**
     * True for message events that carry an outgoing (agent/bot) message.
     */
    public function isOutgoing(): bool
    {
        return $this->normalizedMessageType() === 'outgoing';
    }

    /**
     * The message subject of a `message_*` event (the body itself is the
     * message). Returns null for non-message events.
     */
    public function message(): ?MessageData
    {
        if (! str_starts_with((string) $this->event, 'message_')) {
            return null;
        }

        return MessageData::from($this->payload);
    }

    /**
     * The conversation tied to the event: the nested `conversation` for message
     * events, or the body itself for `conversation_*` events. Null if absent.
     */
    public function conversation(): ?ConversationData
    {
        if (str_starts_with((string) $this->event, 'conversation_')) {
            return ConversationData::from($this->payload);
        }

        $conversation = $this->payload['conversation'] ?? null;

        return is_array($conversation) ? ConversationData::from($conversation) : null;
    }

    /**
     * The contact tied to the event: the body for `contact_*` events, otherwise
     * the message `sender` (when it is a contact) or the conversation's sender.
     */
    public function contact(): ?ContactData
    {
        if (str_starts_with((string) $this->event, 'contact_')) {
            return ContactData::from($this->payload);
        }

        $sender = $this->payload['sender']
            ?? data_get($this->payload, 'conversation.meta.sender')
            ?? data_get($this->payload, 'meta.sender');

        if (! is_array($sender)) {
            return null;
        }

        // Only a contact sender maps to ContactData; skip agents (`user`) and
        // bots (`agent_bot`). Absent type is treated as a contact.
        $type = $sender['type'] ?? null;
        if (is_string($type) && strtolower($type) !== 'contact') {
            return null;
        }

        return ContactData::from($sender);
    }

    /**
     * The raw decoded webhook body.
     *
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->payload;
    }

    private function normalizedMessageType(): ?string
    {
        $type = $this->payload['message_type'] ?? null;

        return match (true) {
            $type === 'incoming', $type === 0 => 'incoming',
            $type === 'outgoing', $type === 1 => 'outgoing',
            default => is_string($type) ? $type : null,
        };
    }
}
