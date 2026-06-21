<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('custom_filters'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $customFilterId): Collection
    {
        return $this->httpGet($this->accountPath("custom_filters/{$customFilterId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'type'=>'conversation|contact|report', 'query'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('custom_filters'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $customFilterId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("custom_filters/{$customFilterId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $customFilterId): Collection
    {
        return $this->httpDelete($this->accountPath("custom_filters/{$customFilterId}"));
    }
}
