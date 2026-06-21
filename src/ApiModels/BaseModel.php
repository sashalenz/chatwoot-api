<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;
use Sashalenz\ChatwootApi\Request;

/**
 * Base for the fluent resource models (Contacts / Conversations / Messages),
 * mirroring the viber-bot-api / monobank-api `BaseModel` shape.
 *
 * Resolution seams (per-instance override wins, else config):
 *   ->token($t)      → config('chatwoot-api.token')      → `api_access_token` header
 *   ->accountId($id) → config('chatwoot-api.account_id') → path `accounts/{id}/…`
 */
abstract class BaseModel
{
    use Conditionable;

    private ?string $token = null;

    private ?int $accountId = null;

    public function token(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function accountId(int $accountId): static
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Config key the access token is read from. Application/Client resources use
     * the default; Platform API resources override this to the platform token.
     */
    protected function tokenConfigKey(): string
    {
        return 'chatwoot-api.token';
    }

    /**
     * @throws ChatwootApiException
     */
    protected function resolveToken(): string
    {
        $key = $this->tokenConfigKey();
        $token = $this->token ?? config($key);

        if (empty($token) || ! is_string($token)) {
            throw new ChatwootApiException("Chatwoot API token is not configured (config {$key}).");
        }

        return $token;
    }

    /**
     * @throws ChatwootApiException
     */
    protected function resolveAccountId(): int
    {
        $accountId = $this->accountId ?? config('chatwoot-api.account_id');

        if (empty($accountId) || ! is_numeric($accountId)) {
            throw new ChatwootApiException('Chatwoot account id is not configured (config chatwoot-api.account_id).');
        }

        return (int) $accountId;
    }

    /**
     * Build an account-scoped path, e.g. accountPath('contacts') →
     * `accounts/42/contacts`.
     */
    protected function accountPath(string $suffix): string
    {
        return 'api/v1/accounts/'.$this->resolveAccountId().'/'.ltrim($suffix, '/');
    }

    /**
     * @return array<string,string>
     *
     * @throws ChatwootApiException
     */
    protected function headers(): array
    {
        return ['api_access_token' => $this->resolveToken()];
    }

    /**
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    protected function httpGet(string $path, array $query = []): Collection
    {
        return $this->dispatch('GET', $path, $query);
    }

    /**
     * @param  array<string,mixed>  $body
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    protected function httpPost(string $path, array $body = []): Collection
    {
        return $this->dispatch('POST', $path, $body);
    }

    /**
     * @param  array<string,mixed>  $body
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    protected function httpPut(string $path, array $body = []): Collection
    {
        return $this->dispatch('PUT', $path, $body);
    }

    /**
     * @param  array<string,mixed>  $body
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    protected function httpPatch(string $path, array $body = []): Collection
    {
        return $this->dispatch('PATCH', $path, $body);
    }

    /**
     * @param  array<string,mixed>  $body
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    protected function httpDelete(string $path, array $body = []): Collection
    {
        return $this->dispatch('DELETE', $path, $body);
    }

    /**
     * @param  array<string,mixed>  $params
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    private function dispatch(string $method, string $path, array $params): Collection
    {
        $response = (new Request($method, $path, $params, $this->headers()))->make();

        /** @var array<string,mixed> $json */
        $json = $response->json() ?? [];

        return collect($json);
    }
}
