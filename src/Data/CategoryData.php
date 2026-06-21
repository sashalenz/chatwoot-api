<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Help Center category within a portal.
 */
final class CategoryData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?int $portalId = null,
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $locale = null,
        public ?int $position = null,
        public ?int $parentCategoryId = null,
        public ?int $associatedCategoryId = null,
    ) {}
}
