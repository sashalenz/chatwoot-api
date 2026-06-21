<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — the current Account itself.
 *
 * @see https://developers.chatwoot.com/api-reference/account
 */
final class Account extends BaseModel
{
    /**
     * Fetch the account details.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(): Collection
    {
        return $this->httpGet($this->selfPath());
    }

    /**
     * Update the account.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'locale'=>…, 'auto_resolve_duration'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(array $attributes): Collection
    {
        return $this->httpPatch($this->selfPath(), $attributes);
    }

    /**
     * @throws ChatwootApiException
     */
    private function selfPath(): string
    {
        return rtrim($this->accountPath(''), '/');
    }
}
