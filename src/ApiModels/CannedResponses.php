<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Canned Responses (saved replies / shortcodes).
 *
 * @see https://developers.chatwoot.com/api-reference/canned-responses
 */
final class CannedResponses extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('canned_responses'));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['short_code'=>'hi', 'content'=>'Hello 👋']
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('canned_responses'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $cannedResponseId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("canned_responses/{$cannedResponseId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $cannedResponseId): Collection
    {
        return $this->httpDelete($this->accountPath("canned_responses/{$cannedResponseId}"));
    }
}
