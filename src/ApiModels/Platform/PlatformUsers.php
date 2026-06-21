<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Data\ProfileData;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Platform API — Users (installation-level user provisioning).
 *
 * @see https://developers.chatwoot.com/api-reference/users
 */
final class PlatformUsers extends PlatformModel
{
    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'email'=>…, 'password'=>…, 'custom_attributes'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): ProfileData
    {
        return ProfileData::from($this->httpPost($this->platformPath('users'), $attributes)->all());
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $userId): ProfileData
    {
        return ProfileData::from($this->httpGet($this->platformPath("users/{$userId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $userId, array $attributes): ProfileData
    {
        return ProfileData::from($this->httpPatch($this->platformPath("users/{$userId}"), $attributes)->all());
    }

    /**
     * Delete a user. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $userId): bool
    {
        $this->httpDelete($this->platformPath("users/{$userId}"));

        return true;
    }

    /**
     * Get an SSO login link for the user. The response is a small `{url: …}`
     * payload, returned as a Collection.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function login(int $userId): Collection
    {
        return $this->httpGet($this->platformPath("users/{$userId}/login"));
    }
}
