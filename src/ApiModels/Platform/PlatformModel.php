<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Sashalenz\ChatwootApi\ApiModels\BaseModel;

/**
 * Base for the Platform API resources. These live under `platform/api/v1/…`
 * (no account scoping) and authenticate with the Platform App token rather than
 * the Application API token.
 */
abstract class PlatformModel extends BaseModel
{
    protected function tokenConfigKey(): string
    {
        return 'chatwoot-api.platform_token';
    }

    /**
     * Build a platform path, e.g. platformPath('users') →
     * `platform/api/v1/users`.
     */
    protected function platformPath(string $suffix): string
    {
        return 'platform/api/v1/'.ltrim($suffix, '/');
    }
}
