<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sashalenz\ChatwootApi\ChatwootApiServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            ChatwootApiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('chatwoot-api.base_url', 'https://chatwoot.test');
        $app['config']->set('chatwoot-api.account_id', 1);
        $app['config']->set('chatwoot-api.token', 'test-token');
    }
}
