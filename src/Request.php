<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Transport layer. Speaks the Chatwoot Application API over `{base_url}/api/v1`.
 * Auth is the `api_access_token` header (NOT `Authorization: Bearer`). Unlike
 * Viber, Chatwoot is a real REST API — the HTTP verb carries meaning, so this
 * class dispatches by method. Logical errors surface as non-2xx and are turned
 * into {@see ChatwootApiException} via `->throw()`.
 */
final class Request
{
    private const TIMEOUT = 15;

    private const RETRY_TIMES = 2;

    private const RETRY_SLEEP = 200;

    /**
     * @param  array<string,mixed>  $params  query for GET, JSON body otherwise
     * @param  array<string,string>  $headers
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $params,
        private readonly array $headers,
    ) {}

    /**
     * @throws ChatwootApiException
     */
    public function make(): Response
    {
        $request = Http::timeout(self::TIMEOUT)
            ->retry(self::RETRY_TIMES, self::RETRY_SLEEP)
            ->baseUrl(rtrim((string) config('chatwoot-api.base_url'), '/'))
            ->withHeaders($this->headers)
            ->acceptJson();

        try {
            return (match (strtoupper($this->method)) {
                'GET' => $request->get($this->path, $this->params),
                'POST' => $request->asJson()->post($this->path, $this->params),
                'PUT' => $request->asJson()->put($this->path, $this->params),
                'PATCH' => $request->asJson()->patch($this->path, $this->params),
                'DELETE' => $request->asJson()->delete($this->path, $this->params),
                default => throw new ChatwootApiException("Unsupported HTTP method [{$this->method}]."),
            })->throw();
        } catch (RequestException $e) {
            throw new ChatwootApiException('Chatwoot API transport error: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
