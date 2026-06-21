<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists integration apps', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::integrations()->apps();

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
