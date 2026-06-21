<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * An agent bot definition.
 */
final class AgentBotData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $botConfig
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $thumbnail = null,
        public ?string $outgoingUrl = null,
        public ?string $botType = null,
        public ?string $accessToken = null,
        public ?bool $systemBot = null,
        public array $botConfig = [],
    ) {}
}
