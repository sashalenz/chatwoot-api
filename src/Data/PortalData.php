<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Help Center portal.
 */
final class PortalData extends ChatwootData
{
    /**
     * @param  array<int, string>  $allowedLocales
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $customDomain = null,
        public ?string $color = null,
        public ?string $homepageLink = null,
        public ?string $pageTitle = null,
        public ?string $headerText = null,
        public array $allowedLocales = [],
        public array $config = [],
    ) {}
}
