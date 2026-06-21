<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A custom attribute definition (the schema of a custom attribute on
 * conversations or contacts).
 */
final class CustomAttributeDefinitionData extends ChatwootData
{
    /**
     * @param  array<int, mixed>  $attributeValues
     */
    public function __construct(
        public ?int $id = null,
        public ?string $attributeDisplayName = null,
        public ?string $attributeDisplayType = null,
        public ?string $attributeDescription = null,
        public ?string $attributeKey = null,
        public ?string $attributeModel = null,
        public ?string $regexPattern = null,
        public ?string $regexCue = null,
        public mixed $defaultValue = null,
        public array $attributeValues = [],
        public int|string|null $createdAt = null,
        public int|string|null $updatedAt = null,
    ) {}
}
