<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Illuminate\Http\Client\PendingRequest;
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
     * @param  array<string,mixed>  $params  query for GET, JSON/form body otherwise
     * @param  array<string,string>  $headers
     * @param  array<int,array{name?:string,contents:string,filename?:string}>  $attachments  when non-empty the request is sent as multipart POST (files + $params as form fields)
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $params,
        private readonly array $headers,
        private readonly array $attachments = [],
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

        if ($this->attachments !== []) {
            return $this->makeMultipart($request);
        }

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

    /**
     * Multipart POST: attach each file as a repeated `attachments[]` part and
     * send $params as plain form fields. The Chatwoot Client API message
     * endpoint accepts customer media exactly this way (same as the web widget).
     *
     * @throws ChatwootApiException
     */
    private function makeMultipart(PendingRequest $request): Response
    {
        foreach ($this->attachments as $attachment) {
            $request = $request->attach(
                $attachment['name'] ?? 'attachments[]',
                $attachment['contents'],
                $attachment['filename'] ?? 'attachment',
            );
        }

        try {
            return $request->post($this->path, $this->params)->throw();
        } catch (RequestException $e) {
            throw new ChatwootApiException('Chatwoot API transport error: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
