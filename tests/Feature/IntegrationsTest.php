<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\IntegrationAppData;

it('lists integration apps as typed DTOs', function (): void {
    Http::fake(['*' => Http::response([
        'payload' => [['id' => 'dialogflow', 'name' => 'Dialogflow', 'enabled' => true]],
    ], 200)]);

    $result = ChatwootApi::integrations()->apps();

    expect($result->count())->toBe(1)
        ->and($result->payload[0])->toBeInstanceOf(IntegrationAppData::class)
        ->and($result->payload[0]->name)->toBe('Dialogflow');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/integrations/apps');
});

it('creates an integration hook', function (): void {
    Http::fake(['*' => Http::response(['id' => 4], 200)]);

    ChatwootApi::integrations()->createHook(['app_id' => 'dialogflow', 'inbox_id' => 1, 'settings' => []]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/integrations/hooks'
        && $request['app_id'] === 'dialogflow');
});

it('updates an integration hook via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 4], 200)]);

    ChatwootApi::integrations()->updateHook(4, ['settings' => ['project_id' => 'x']]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/integrations/hooks/4');
});

it('deletes an integration hook', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::integrations()->deleteHook(4);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/integrations/hooks/4');
});
