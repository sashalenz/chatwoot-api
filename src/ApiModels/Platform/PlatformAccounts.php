<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Platform API — Accounts (and their account-user links).
 *
 * @see https://developers.chatwoot.com/api-reference/accounts
 */
final class PlatformAccounts extends PlatformModel
{
    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'locale'=>…, 'support_email'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->platformPath('accounts'), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $accountId): Collection
    {
        return $this->httpGet($this->platformPath("accounts/{$accountId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $accountId, array $attributes): Collection
    {
        return $this->httpPatch($this->platformPath("accounts/{$accountId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $accountId): Collection
    {
        return $this->httpDelete($this->platformPath("accounts/{$accountId}"));
    }

    /**
     * List the users linked to an account.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function users(int $accountId): Collection
    {
        return $this->httpGet($this->platformPath("accounts/{$accountId}/account_users"));
    }

    /**
     * Link a user to an account with a role.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createUser(int $accountId, int $userId, string $role = 'agent'): Collection
    {
        return $this->httpPost($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    /**
     * Unlink a user from an account.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function deleteUser(int $accountId, int $userId): Collection
    {
        return $this->httpDelete($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
        ]);
    }
}
