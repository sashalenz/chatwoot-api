<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\ProfileData;

it('fetches the profile (not account scoped)', function (): void {
    Http::fake(['*' => Http::response(['id' => 11, 'name' => 'Agent'], 200)]);

    $result = ChatwootApi::profile()->get();

    expect($result)->toBeInstanceOf(ProfileData::class)
        ->and($result->id)->toBe(11)
        ->and($result->name)->toBe('Agent');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/profile'
        && $request->hasHeader('api_access_token', 'test-token'));
});

it('updates the profile via PUT wrapped in profile key', function (): void {
    Http::fake(['*' => Http::response(['id' => 11], 200)]);

    ChatwootApi::profile()->update(['display_name' => 'Petro', 'availability' => 'online']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'https://chatwoot.test/api/v1/profile'
        && data_get($request->data(), 'profile.display_name') === 'Petro');
});
