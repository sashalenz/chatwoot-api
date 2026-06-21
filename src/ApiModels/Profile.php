<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — the profile of the user/agent that owns the access token.
 * Not account-scoped: lives at `/api/v1/profile`.
 *
 * @see https://developers.chatwoot.com/api-reference/profile
 */
final class Profile extends BaseModel
{
    /**
     * Fetch the authenticated user's profile.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(): Collection
    {
        return $this->httpGet('api/v1/profile');
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  array<string,mixed>  $profile  e.g. ['display_name'=>…, 'availability'=>'online|busy|offline']
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(array $profile): Collection
    {
        return $this->httpPut('api/v1/profile', ['profile' => $profile]);
    }
}
