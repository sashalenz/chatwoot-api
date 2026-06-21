<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\AccountData;
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
     * @throws ChatwootApiException
     */
    public function get(): AccountData
    {
        return AccountData::from($this->httpGet($this->selfPath())->all());
    }

    /**
     * Update the account.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'locale'=>…, 'auto_resolve_duration'=>…]
     *
     * @throws ChatwootApiException
     */
    public function update(array $attributes): AccountData
    {
        return AccountData::from($this->httpPatch($this->selfPath(), $attributes)->all());
    }

    /**
     * @throws ChatwootApiException
     */
    private function selfPath(): string
    {
        return rtrim($this->accountPath(''), '/');
    }
}
