<?php

declare(strict_types=1);

use Sashalenz\ChatwootApi\Data\ContactData;
use Sashalenz\ChatwootApi\Data\ConversationData;
use Sashalenz\ChatwootApi\Data\MessageData;
use Sashalenz\ChatwootApi\Data\WebhookEvent;

it('parses a message_created webhook with nested conversation and sender', function (): void {
    $event = WebhookEvent::fromArray([
        'event' => 'message_created',
        'id' => 555,
        'content' => 'Привіт',
        'message_type' => 'incoming',
        'conversation' => ['id' => 9, 'status' => 'open'],
        'sender' => ['id' => 7, 'name' => 'Petro', 'type' => 'contact'],
    ]);

    expect($event->event)->toBe('message_created')
        ->and($event->isMessageCreated())->toBeTrue()
        ->and($event->isIncoming())->toBeTrue()
        ->and($event->isOutgoing())->toBeFalse();

    expect($event->message())->toBeInstanceOf(MessageData::class)
        ->and($event->message()->id)->toBe(555)
        ->and($event->message()->content)->toBe('Привіт')
        ->and($event->message()->messageType)->toBe('incoming');

    expect($event->conversation())->toBeInstanceOf(ConversationData::class)
        ->and($event->conversation()->id)->toBe(9);

    expect($event->contact())->toBeInstanceOf(ContactData::class)
        ->and($event->contact()->name)->toBe('Petro');
});

it('treats an agent sender as not a contact', function (): void {
    $event = WebhookEvent::fromArray([
        'event' => 'message_created',
        'id' => 556,
        'message_type' => 'outgoing',
        'sender' => ['id' => 1, 'name' => 'Agent', 'type' => 'user'],
    ]);

    expect($event->isOutgoing())->toBeTrue()
        ->and($event->contact())->toBeNull();
});

it('treats an agent_bot sender as not a contact', function (): void {
    $event = WebhookEvent::fromArray([
        'event' => 'message_created',
        'id' => 557,
        'message_type' => 'outgoing',
        'sender' => ['id' => 3, 'name' => 'Router', 'type' => 'agent_bot'],
    ]);

    expect($event->contact())->toBeNull();
});

it('parses a conversation_created webhook where the body is the conversation', function (): void {
    $event = WebhookEvent::fromArray([
        'event' => 'conversation_created',
        'id' => 42,
        'status' => 'open',
        'messages' => [['id' => 1, 'content' => 'hi', 'message_type' => 0]],
    ]);

    expect($event->isConversationCreated())->toBeTrue()
        ->and($event->message())->toBeNull()
        ->and($event->conversation())->toBeInstanceOf(ConversationData::class)
        ->and($event->conversation()->id)->toBe(42)
        ->and($event->conversation()->messages[0])->toBeInstanceOf(MessageData::class);
});

it('parses a contact_created webhook where the body is the contact', function (): void {
    $event = WebhookEvent::fromArray([
        'event' => 'contact_created',
        'id' => 7,
        'name' => 'Petro',
        'phone_number' => '+380501234567',
    ]);

    expect($event->contact())->toBeInstanceOf(ContactData::class)
        ->and($event->contact()->name)->toBe('Petro')
        ->and($event->contact()->phoneNumber)->toBe('+380501234567')
        ->and($event->conversation())->toBeNull();
});

it('parses from a raw JSON body and exposes the raw payload', function (): void {
    $event = WebhookEvent::fromJson('{"event":"message_created","id":1,"message_type":1}');

    expect($event->event)->toBe('message_created')
        ->and($event->isOutgoing())->toBeTrue()
        ->and($event->raw())->toBe(['event' => 'message_created', 'id' => 1, 'message_type' => 1]);
});

it('handles an unknown / malformed event gracefully', function (): void {
    $event = WebhookEvent::fromArray(['foo' => 'bar']);

    expect($event->event)->toBeNull()
        ->and($event->message())->toBeNull()
        ->and($event->conversation())->toBeNull()
        ->and($event->contact())->toBeNull();
});
