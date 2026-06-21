<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The authenticated user's profile (also the shape of Platform API users).
 */
final class ProfileData extends ChatwootData
{
    /**
     * @param  array<int, mixed>  $accounts
     * @param  array<string, mixed>  $customAttributes
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $displayName = null,
        public ?string $email = null,
        public ?string $availableName = null,
        public ?string $avatarUrl = null,
        public ?string $role = null,
        public ?bool $confirmed = null,
        public ?string $accessToken = null,
        public ?string $pubsubToken = null,
        public array $accounts = [],
        public array $customAttributes = [],
    ) {}
}
