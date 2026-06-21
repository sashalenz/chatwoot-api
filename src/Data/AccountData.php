<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Chatwoot account.
 */
final class AccountData extends ChatwootData
{
    /**
     * @param  array<int, string>  $features
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $customAttributes
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $locale = null,
        public ?string $domain = null,
        public ?string $supportEmail = null,
        public ?string $status = null,
        public ?string $role = null,
        public array $features = [],
        public array $settings = [],
        public array $customAttributes = [],
        public int|string|null $createdAt = null,
    ) {}
}
