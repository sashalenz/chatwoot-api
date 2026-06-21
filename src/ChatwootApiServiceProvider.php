<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChatwootApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('chatwoot-api')
            ->hasConfigFile();
    }
}
