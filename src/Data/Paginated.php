<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

use Spatie\LaravelData\Data;

/**
 * A typed, paginated list response: a `payload` of hydrated DTOs plus the raw
 * `meta` block Chatwoot returns alongside it (shapes vary per endpoint).
 *
 * @template T of Data
 */
final class Paginated extends Data
{
    /**
     * @param  array<int, T>  $payload  the hydrated items
     * @param  array<string, mixed>  $meta  raw meta block (counts, current_page, …)
     */
    public function __construct(
        public array $payload = [],
        public array $meta = [],
    ) {}

    /**
     * Build from a raw Chatwoot list response by hydrating each item into the
     * given DTO class.
     *
     * @template TData of Data
     *
     * @param  array<array-key, mixed>  $response
     * @param  class-string<TData>  $dataClass
     * @return self<TData>
     */
    public static function fromResponse(array $response, string $dataClass): self
    {
        // Chatwoot list endpoints come in two shapes: `{payload: [...], meta: {...}}`
        // and a bare top-level array. Conversation lists wrap one level deeper in
        // `data` — callers unwrap that before handing the inner block here.
        if (array_key_exists('payload', $response)) {
            /** @var array<int, mixed> $items */
            $items = is_array($response['payload']) ? $response['payload'] : [];
            /** @var array<string, mixed> $meta */
            $meta = is_array($response['meta'] ?? null) ? $response['meta'] : [];
        } elseif (array_is_list($response)) {
            $items = $response;
            $meta = [];
        } else {
            $items = [];
            $meta = $response;
        }

        return new self(
            payload: array_map(static fn (mixed $item): Data => $dataClass::from($item), array_values($items)),
            meta: $meta,
        );
    }

    public function count(): int
    {
        return count($this->payload);
    }

    public function currentPage(): ?int
    {
        $page = $this->meta['current_page'] ?? null;

        return $page === null ? null : (int) $page;
    }

    public function totalCount(): ?int
    {
        $count = $this->meta['count'] ?? $this->meta['all_count'] ?? null;

        return $count === null ? null : (int) $count;
    }
}
