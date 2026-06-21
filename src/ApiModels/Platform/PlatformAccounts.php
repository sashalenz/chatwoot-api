<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Sashalenz\ChatwootApi\Data\AccountUserData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Data\PlatformAccountData;
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
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): PlatformAccountData
    {
        return PlatformAccountData::from($this->httpPost($this->platformPath('accounts'), $attributes)->all());
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $accountId): PlatformAccountData
    {
        return PlatformAccountData::from($this->httpGet($this->platformPath("accounts/{$accountId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $accountId, array $attributes): PlatformAccountData
    {
        return PlatformAccountData::from($this->httpPatch($this->platformPath("accounts/{$accountId}"), $attributes)->all());
    }

    /**
     * Delete an account. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $accountId): bool
    {
        $this->httpDelete($this->platformPath("accounts/{$accountId}"));

        return true;
    }

    /**
     * List the users linked to an account.
     *
     * @return Paginated<AccountUserData>
     *
     * @throws ChatwootApiException
     */
    public function users(int $accountId): Paginated
    {
        return Paginated::fromResponse(
            $this->httpGet($this->platformPath("accounts/{$accountId}/account_users"))->all(),
            AccountUserData::class,
        );
    }

    /**
     * Link a user to an account with a role.
     *
     * @throws ChatwootApiException
     */
    public function createUser(int $accountId, int $userId, string $role = 'agent'): AccountUserData
    {
        return AccountUserData::from($this->httpPost($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
            'role' => $role,
        ])->all());
    }

    /**
     * Unlink a user from an account. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function deleteUser(int $accountId, int $userId): bool
    {
        $this->httpDelete($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
        ]);

        return true;
    }
}
