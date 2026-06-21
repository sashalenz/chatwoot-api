<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Data\ContactData;
use Sashalenz\ChatwootApi\Data\ContactInboxData;
use Sashalenz\ChatwootApi\Data\ConversationData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Contacts.
 *
 * @see https://developers.chatwoot.com/api-reference/contacts-api
 */
final class Contacts extends BaseModel
{
    /**
     * Create a contact. The returned DTO carries `sourceId` (lifted from the
     * `contact_inbox` in the response) when `inbox_id` was supplied.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'phone_number'=>…, 'identifier'=>…, 'inbox_id'=>1, 'custom_attributes'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): ContactData
    {
        $payload = (array) $this->httpPost($this->accountPath('contacts'), $attributes)->get('payload', []);

        /** @var array<string,mixed> $contact */
        $contact = $payload['contact'] ?? $payload;
        $contact['source_id'] = data_get($payload, 'contact_inbox.source_id');

        return ContactData::from($contact);
    }

    /**
     * List contacts in the account.
     *
     * @param  array<string,mixed>  $query  optional: ['page'=>1, 'sort'=>'name|email|-last_activity_at|…']
     * @return Paginated<ContactData>
     *
     * @throws ChatwootApiException
     */
    public function list(array $query = []): Paginated
    {
        return Paginated::fromResponse(
            $this->httpGet($this->accountPath('contacts'), $query)->all(),
            ContactData::class,
        );
    }

    /**
     * Fetch a single contact.
     *
     * @throws ChatwootApiException
     */
    public function get(int $contactId): ContactData
    {
        $resp = $this->httpGet($this->accountPath("contacts/{$contactId}"))->all();

        return ContactData::from($resp['payload'] ?? $resp);
    }

    /**
     * Free-text search across name / email / phone / identifier.
     *
     * @param  array<string,mixed>  $query  optional: ['page'=>1, 'sort'=>…]
     * @return Paginated<ContactData>
     *
     * @throws ChatwootApiException
     */
    public function search(string $q, array $query = []): Paginated
    {
        return Paginated::fromResponse(
            $this->httpGet($this->accountPath('contacts/search'), ['q' => $q, ...$query])->all(),
            ContactData::class,
        );
    }

    /**
     * Advanced contact filtering (query-builder payload).
     *
     * @param  array<string,mixed>  $payload  e.g. ['payload'=>[['attribute_key'=>'email', 'filter_operator'=>'contains', 'values'=>['@a20']]]]
     * @return Paginated<ContactData>
     *
     * @throws ChatwootApiException
     */
    public function filter(array $payload): Paginated
    {
        return Paginated::fromResponse(
            $this->httpPost($this->accountPath('contacts/filter'), $payload)->all(),
            ContactData::class,
        );
    }

    /**
     * Update a contact (e.g. refresh `custom_attributes` from CRM).
     *
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $contactId, array $attributes): ContactData
    {
        $resp = $this->httpPut($this->accountPath("contacts/{$contactId}"), $attributes)->all();

        return ContactData::from($resp['payload'] ?? $resp);
    }

    /**
     * Delete a contact. Returns true on success (a non-2xx response throws).
     *
     * @throws ChatwootApiException
     */
    public function delete(int $contactId): bool
    {
        $this->httpDelete($this->accountPath("contacts/{$contactId}"));

        return true;
    }

    /**
     * Create a contact-inbox association → returns the new `sourceId`. Use when
     * the contact already exists but is not yet bound to the target inbox.
     *
     * @throws ChatwootApiException
     */
    public function createInbox(int $contactId, int $inboxId, ?string $sourceId = null): ContactInboxData
    {
        $resp = $this->httpPost(
            $this->accountPath("contacts/{$contactId}/contact_inboxes"),
            array_filter([
                'inbox_id' => $inboxId,
                'source_id' => $sourceId,
            ], static fn ($v): bool => $v !== null),
        )->all();

        return ContactInboxData::from($resp);
    }

    /**
     * Inboxes the contact can be reached on.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function contactableInboxes(int $contactId): Collection
    {
        return $this->httpGet($this->accountPath("contacts/{$contactId}/contactable_inboxes"));
    }

    /**
     * List the contact's conversations.
     *
     * @return Paginated<ConversationData>
     *
     * @throws ChatwootApiException
     */
    public function conversations(int $contactId): Paginated
    {
        return Paginated::fromResponse(
            $this->httpGet($this->accountPath("contacts/{$contactId}/conversations"))->all(),
            ConversationData::class,
        );
    }

    /**
     * List the labels attached to the contact.
     *
     * @return array<int,string>
     *
     * @throws ChatwootApiException
     */
    public function labels(int $contactId): array
    {
        /** @var array<int,string> $labels */
        $labels = $this->httpGet($this->accountPath("contacts/{$contactId}/labels"))->get('payload', []);

        return $labels;
    }

    /**
     * Replace the contact's labels with the given set; returns the new label set.
     *
     * @param  array<int,string>  $labels
     * @return array<int,string>
     *
     * @throws ChatwootApiException
     */
    public function addLabels(int $contactId, array $labels): array
    {
        /** @var array<int,string> $result */
        $result = $this->httpPost($this->accountPath("contacts/{$contactId}/labels"), ['labels' => $labels])->get('payload', []);

        return $result;
    }
}
