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
}
