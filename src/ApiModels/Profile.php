<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\ProfileData;
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
     * @throws ChatwootApiException
     */
    public function get(): ProfileData
    {
        return ProfileData::from($this->httpGet('api/v1/profile')->all());
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  array<string,mixed>  $profile  e.g. ['display_name'=>…, 'availability'=>'online|busy|offline']
     *
     * @throws ChatwootApiException
     */
    public function update(array $profile): ProfileData
    {
        return ProfileData::from($this->httpPut('api/v1/profile', ['profile' => $profile])->all());
    }
}
