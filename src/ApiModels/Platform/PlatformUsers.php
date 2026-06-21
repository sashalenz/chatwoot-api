<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Illuminate\Support\Collection;
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->platformPath('users'), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $userId): Collection
    {
        return $this->httpGet($this->platformPath("users/{$userId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $userId, array $attributes): Collection
    {
        return $this->httpPatch($this->platformPath("users/{$userId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $userId): Collection
    {
        return $this->httpDelete($this->platformPath("users/{$userId}"));
    }

    /**
     * Get an SSO login link for the user.
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
