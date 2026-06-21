<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A label in the account-level label catalogue.
 */
final class LabelData extends ChatwootData
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $color = null,
        public ?bool $showOnSidebar = null,
    ) {}
}
