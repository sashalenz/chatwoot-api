<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Help Center article within a portal.
 */
final class ArticleData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?int $portalId = null,
        public ?int $categoryId = null,
        public ?int $authorId = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $content = null,
        public ?string $status = null,
        public ?int $position = null,
        public ?int $views = null,
        public array $meta = [],
    ) {}
}
