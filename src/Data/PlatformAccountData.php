<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The Platform API view of an account.
 */
final class PlatformAccountData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $customAttributes
     * @param  array<string, mixed>  $limits
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $locale = null,
        public ?string $domain = null,
        public ?string $supportEmail = null,
        public ?string $status = null,
        public array $customAttributes = [],
        public array $limits = [],
    ) {}
}
