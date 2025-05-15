<?php

namespace Padmission\MissingRecordRedirect;

use Padmission\MissingRecordRedirect\Commands\MissingRecordRedirectCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MissingRecordRedirectServiceProvider extends PackageServiceProvider
{
    public static string $name = 'missing-record-redirect';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('padmission/missing-record-redirect');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }
    }

    public function packageRegistered(): void
    {
        //
    }

    public function packageBooted(): void
    {
        //
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            MissingRecordRedirectCommand::class,
        ];
    }
}
