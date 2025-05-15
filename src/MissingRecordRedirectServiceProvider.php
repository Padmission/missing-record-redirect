<?php

namespace Padmission\MissingRecordRedirect;

use Filament\Facades\Filament;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Padmission\MissingRecordRedirect\Commands\MissingRecordRedirectCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // We need to hook our handler into Laravel's exception handling system
        // without replacing the existing handler or any other handlers defined by users
        $this->app->booted(function () {
            $handler = $this->app->make(ExceptionHandler::class);

            if (method_exists($handler, 'renderable')) {
                $handler->renderable(function (NotFoundHttpException $e, Request $request) {
                    foreach (Filament::getPanels() as $panel) {
                        $plugin = $panel->getPlugin('missing-record-redirect');

                        if ($plugin instanceof MissingRecordRedirectPlugin) {
                            $response = $plugin->handleNotFoundHttpException($e, $request, $panel);

                            if ($response !== null) {
                                return $response;
                            }
                        }
                    }

                    return null;
                });
            }
        });
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
