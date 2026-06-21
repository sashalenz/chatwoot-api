<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Teams (and their agent members).
 *
 * @see https://developers.chatwoot.com/api-reference/teams
 */
final class Teams extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('teams'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $teamId): Collection
    {
        return $this->httpGet($this->accountPath("teams/{$teamId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'description'=>…, 'allow_auto_assign'=>true]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('teams'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $teamId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("teams/{$teamId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $teamId): Collection
    {
        return $this->httpDelete($this->accountPath("teams/{$teamId}"));
    }

    /**
     * List the agents on the team.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function members(int $teamId): Collection
    {
        return $this->httpGet($this->accountPath("teams/{$teamId}/team_members"));
    }

    /**
     * Add agents to the team.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function addMembers(int $teamId, array $userIds): Collection
    {
        return $this->httpPost($this->accountPath("teams/{$teamId}/team_members"), ['user_ids' => $userIds]);
    }

    /**
     * Replace the team's agent set with the given user ids.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function updateMembers(int $teamId, array $userIds): Collection
    {
        return $this->httpPatch($this->accountPath("teams/{$teamId}/team_members"), ['user_ids' => $userIds]);
    }

    /**
     * Remove agents from the team.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function removeMembers(int $teamId, array $userIds): Collection
    {
        return $this->httpDelete($this->accountPath("teams/{$teamId}/team_members"), ['user_ids' => $userIds]);
    }
}
