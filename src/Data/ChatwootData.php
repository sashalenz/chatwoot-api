<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Base for all Chatwoot response DTOs. Chatwoot payloads are snake_case; the
 * DTOs expose idiomatic camelCase properties via the snake-case input mapper.
 * Unknown payload keys are ignored, so the DTOs tolerate Chatwoot adding fields.
 */
#[MapInputName(SnakeCaseMapper::class)]
abstract class ChatwootData extends Data {}
