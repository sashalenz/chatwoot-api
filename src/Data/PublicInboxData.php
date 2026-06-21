<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * The public (Client API) view of an inbox.
 */
final class PublicInboxData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $workingHours
     */
    public function __construct(
        public ?string $identifier = null,
        public ?string $name = null,
        public ?string $timezone = null,
        public ?bool $workingHoursEnabled = null,
        public ?bool $csatSurveyEnabled = null,
        public ?bool $greetingEnabled = null,
        public ?bool $identityValidationEnabled = null,
        public array $workingHours = [],
    ) {}
}
