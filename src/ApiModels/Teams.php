<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Data\AgentData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Data\TeamData;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Teams (and their agent members).
 *
 * @see https://developers.chatwoot.com/api-reference/teams
 */
final class Teams extends BaseModel
{
    /**
     * @return Paginated<TeamData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('teams'))->all(), TeamData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $teamId): TeamData
    {
        return TeamData::from($this->httpGet($this->accountPath("teams/{$teamId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'description'=>…, 'allow_auto_assign'=>true]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): TeamData
    {
        return TeamData::from($this->httpPost($this->accountPath('teams'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $teamId, array $attributes): TeamData
    {
        return TeamData::from($this->httpPatch($this->accountPath("teams/{$teamId}"), $attributes)->all());
    }

    /**
     * Delete a team. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $teamId): bool
    {
        $this->httpDelete($this->accountPath("teams/{$teamId}"));

        return true;
    }

    /**
     * List the agents on the team.
     *
     * @return Paginated<AgentData>
     *
     * @throws ChatwootApiException
     */
    public function members(int $teamId): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath("teams/{$teamId}/team_members"))->all(), AgentData::class);
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
     * Remove agents from the team. Returns true on success.
     *
     * @param  array<int,int>  $userIds
     *
     * @throws ChatwootApiException
     */
    public function removeMembers(int $teamId, array $userIds): bool
    {
        $this->httpDelete($this->accountPath("teams/{$teamId}/team_members"), ['user_ids' => $userIds]);

        return true;
    }
}
