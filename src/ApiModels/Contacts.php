<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Contacts.
 *
 * @see https://developers.chatwoot.com/api-reference/contacts-api
 */
final class Contacts extends BaseModel
{
    /**
     * Create a contact. When `inbox_id` is included, the response payload also
     * carries a `contact_inbox.source_id` (the stable contact↔inbox key).
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'phone_number'=>…, 'identifier'=>…, 'inbox_id'=>1, 'custom_attributes'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('contacts'), $attributes);
    }

    /**
     * List contacts in the account.
     *
     * @param  array<string,mixed>  $query  optional: ['page'=>1, 'sort'=>'name|email|-last_activity_at|…']
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(array $query = []): Collection
    {
        return $this->httpGet($this->accountPath('contacts'), $query);
    }

    /**
     * Fetch a single contact.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $contactId): Collection
    {
        return $this->httpGet($this->accountPath("contacts/{$contactId}"));
    }

    /**
     * Free-text search across name / email / phone / identifier.
     *
     * @param  array<string,mixed>  $query  optional: ['page'=>1, 'sort'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function search(string $q, array $query = []): Collection
    {
        return $this->httpGet($this->accountPath('contacts/search'), ['q' => $q, ...$query]);
    }

    /**
     * Advanced contact filtering (query-builder payload).
     *
     * @param  array<string,mixed>  $payload  e.g. ['payload'=>[['attribute_key'=>'email', 'filter_operator'=>'contains', 'values'=>['@a20']]]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function filter(array $payload): Collection
    {
        return $this->httpPost($this->accountPath('contacts/filter'), $payload);
    }

    /**
     * Delete a contact.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $contactId): Collection
    {
        return $this->httpDelete($this->accountPath("contacts/{$contactId}"));
    }

    /**
     * Update a contact (e.g. refresh `custom_attributes` from CRM).
     *
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $contactId, array $attributes): Collection
    {
        return $this->httpPut($this->accountPath("contacts/{$contactId}"), $attributes);
    }

    /**
     * Create a contact-inbox association → returns `source_id`. Use when the
     * contact already exists but is not yet bound to the target inbox.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createInbox(int $contactId, int $inboxId, ?string $sourceId = null): Collection
    {
        return $this->httpPost(
            $this->accountPath("contacts/{$contactId}/contact_inboxes"),
            array_filter([
                'inbox_id' => $inboxId,
                'source_id' => $sourceId,
            ], static fn ($v): bool => $v !== null),
        );
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function conversations(int $contactId): Collection
    {
        return $this->httpGet($this->accountPath("contacts/{$contactId}/conversations"));
    }

    /**
     * List the labels attached to the contact.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function labels(int $contactId): Collection
    {
        return $this->httpGet($this->accountPath("contacts/{$contactId}/labels"));
    }

    /**
     * Replace the contact's labels with the given set.
     *
     * @param  array<int,string>  $labels
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function addLabels(int $contactId, array $labels): Collection
    {
        return $this->httpPost($this->accountPath("contacts/{$contactId}/labels"), ['labels' => $labels]);
    }
}
