<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\CannedResponseData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Canned Responses (saved replies / shortcodes).
 *
 * @see https://developers.chatwoot.com/api-reference/canned-responses
 */
final class CannedResponses extends BaseModel
{
    /**
     * @return Paginated<CannedResponseData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('canned_responses'))->all(), CannedResponseData::class);
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['short_code'=>'hi', 'content'=>'Hello 👋']
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): CannedResponseData
    {
        return CannedResponseData::from($this->httpPost($this->accountPath('canned_responses'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $cannedResponseId, array $attributes): CannedResponseData
    {
        return CannedResponseData::from($this->httpPatch($this->accountPath("canned_responses/{$cannedResponseId}"), $attributes)->all());
    }

    /**
     * Delete a canned response. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $cannedResponseId): bool
    {
        $this->httpDelete($this->accountPath("canned_responses/{$cannedResponseId}"));

        return true;
    }
}
