<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\CustomFilterData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Custom Filters (saved query-builder filters for the
 * current user).
 *
 * @see https://developers.chatwoot.com/api-reference/custom-filters
 */
final class CustomFilters extends BaseModel
{
    /**
     * @return Paginated<CustomFilterData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('custom_filters'))->all(), CustomFilterData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $customFilterId): CustomFilterData
    {
        return CustomFilterData::from($this->httpGet($this->accountPath("custom_filters/{$customFilterId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'type'=>'conversation|contact|report', 'query'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): CustomFilterData
    {
        return CustomFilterData::from($this->httpPost($this->accountPath('custom_filters'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $customFilterId, array $attributes): CustomFilterData
    {
        return CustomFilterData::from($this->httpPatch($this->accountPath("custom_filters/{$customFilterId}"), $attributes)->all());
    }

    /**
     * Delete a custom filter. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $customFilterId): bool
    {
        $this->httpDelete($this->accountPath("custom_filters/{$customFilterId}"));

        return true;
    }
}
