<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A Chatwoot inbox (a channel connected to the account).
 */
final class InboxData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $workingHours
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $channelType = null,
        public ?int $channelId = null,
        public ?string $websiteUrl = null,
        public ?string $avatarUrl = null,
        public ?string $widgetColor = null,
        public ?string $websiteToken = null,
        public ?bool $enableAutoAssignment = null,
        public ?bool $greetingEnabled = null,
        public ?string $greetingMessage = null,
        public ?bool $workingHoursEnabled = null,
        public ?bool $csatSurveyEnabled = null,
        public ?string $timezone = null,
        public ?string $phoneNumber = null,
        public ?string $provider = null,
        public array $workingHours = [],
    ) {}
}
