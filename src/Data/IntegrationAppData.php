<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * An integration app available in the account.
 */
final class IntegrationAppData extends ChatwootData
{
    /**
     * @param  array<int, mixed>  $hooks
     */
    public function __construct(
        public int|string|null $id = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $hookType = null,
        public ?bool $enabled = null,
        public ?bool $allowMultipleHooks = null,
        public array $hooks = [],
    ) {}
}
