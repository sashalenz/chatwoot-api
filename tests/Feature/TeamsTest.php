<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\AgentData;

it('lists teams', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::teams()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams');
});

it('fetches a team', function (): void {
    Http::fake(['*' => Http::response(['id' => 2], 200)]);

    ChatwootApi::teams()->get(2);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2');
});

it('creates a team', function (): void {
    Http::fake(['*' => Http::response(['id' => 2], 200)]);

    ChatwootApi::teams()->create(['name' => 'Sales']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams'
        && $request['name'] === 'Sales');
});

it('updates a team via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 2], 200)]);

    ChatwootApi::teams()->update(2, ['name' => 'Support']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2');
});

it('deletes a team', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::teams()->delete(2);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2');
});

it('lists team members as agent DTOs', function (): void {
    Http::fake(['*' => Http::response([
        ['id' => 5, 'name' => 'Petro', 'role' => 'agent'],
    ], 200)]);

    $result = ChatwootApi::teams()->members(2);

    expect($result->count())->toBe(1)
        ->and($result->payload[0])->toBeInstanceOf(AgentData::class)
        ->and($result->payload[0]->name)->toBe('Petro');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2/team_members');
});

it('adds team members', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::teams()->addMembers(2, [5, 6]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2/team_members'
        && $request['user_ids'] === [5, 6]);
});

it('updates team members via PATCH', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::teams()->updateMembers(2, [5]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2/team_members'
        && $request['user_ids'] === [5]);
});

it('removes team members via DELETE', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::teams()->removeMembers(2, [6]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/teams/2/team_members'
        && $request['user_ids'] === [6]);
});
