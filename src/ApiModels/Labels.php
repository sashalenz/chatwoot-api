<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Labels (account-level label catalogue).
 *
 * @see https://developers.chatwoot.com/api-reference/labels
 */
final class Labels extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('labels'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $labelId): Collection
    {
        return $this->httpGet($this->accountPath("labels/{$labelId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['title'=>…, 'description'=>…, 'color'=>'#FF0000', 'show_on_sidebar'=>true]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('labels'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $labelId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("labels/{$labelId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $labelId): Collection
    {
        return $this->httpDelete($this->accountPath("labels/{$labelId}"));
    }
}
