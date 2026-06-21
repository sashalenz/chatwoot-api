<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A saved custom filter (a stored query-builder filter for the current user).
 */
final class CustomFilterData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $query
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $type = null,
        public array $query = [],
        public int|string|null $createdAt = null,
        public int|string|null $updatedAt = null,
    ) {}
}
