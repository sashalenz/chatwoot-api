<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * An automation rule.
 */
final class AutomationRuleData extends ChatwootData
{
    /**
     * @param  array<int, mixed>  $conditions
     * @param  array<int, mixed>  $actions
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $eventName = null,
        public ?bool $active = null,
        public array $conditions = [],
        public array $actions = [],
        public int|string|null $createdAt = null,
    ) {}
}
