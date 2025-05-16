<?php

namespace Padmission\MissingRecordRedirect;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MissingRecordRedirectPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $notificationTitle = null;

    protected string | Closure | null $notificationBody = null;

    protected string | Closure | null $redirectUrl = null;

    protected ?Closure $notificationCallback = null;

    protected ?Closure $exceptionCallback = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'missing-record-redirect';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        $handler = app(ExceptionHandler::class);

        if (method_exists($handler, 'renderable')) {
            $handler->renderable(function (NotFoundHttpException $e, Request $request) {
                $response = $this->handleNotFoundHttpException($e, $request);

                if ($response !== null) {
                    return $response;
                }

                return null;
            });
        }
    }

    public function notificationTitle(string | Closure | null $title = null): static
    {
        $this->notificationTitle = $title;

        return $this;
    }

    public function notificationBody(string | Closure | null $body = null): static
    {
        $this->notificationBody = $body;

        return $this;
    }

    public function redirectUrl(string | Closure | null $url = null): static
    {
        $this->redirectUrl = $url;

        return $this;
    }

    public function notification(?Closure $callback = null): static
    {
        $this->notificationCallback = $callback;

        return $this;
    }

    public function handleException(?Closure $callback = null): static
    {
        $this->exceptionCallback = $callback;

        return $this;
    }

    public function getNotificationTitle(): string
    {
        return $this->evaluate($this->notificationTitle) ?? 'Record Deleted';
    }

    public function getNotificationBody(): string
    {
        return $this->evaluate($this->notificationBody) ?? 'The record you were trying to view has been deleted or does not exist.';
    }

    protected function handleNotFoundHttpException(NotFoundHttpException $e, Request $request): ?RedirectResponse
    {
        if ($this->exceptionCallback !== null) {
            $callbackResponse = $this->evaluate($this->exceptionCallback, [
                'exception' => $e,
                'request' => $request,
            ]);

            if ($callbackResponse instanceof RedirectResponse) {
                return $callbackResponse;
            }
        }

        $previous = $e->getPrevious();

        if (! $previous instanceof ModelNotFoundException) {
            return null;
        }

        $route = $request->route();

        if (! $route instanceof Route) {
            return null;
        }

        $controller = $route->getController();

        if (! $controller instanceof Page || ! $this->usesInteractsWithRecordTrait($controller)) {
            return null;
        }

        $resource = $controller::getResource();

        $context = new NotificationContext(
            resourceClass: $resource,
            page: $controller,
            request: $request,
            exception: $previous,
        );

        $redirectUrl = $this->evaluate($this->redirectUrl, [
            'context' => $context,
        ]) ?? $resource::getUrl();

        $currentUrl = $request->url();

        if ($redirectUrl !== $currentUrl) {
            $notification = Notification::make()
                ->title($this->getNotificationTitle())
                ->body($this->getNotificationBody())
                ->warning()
                ->persistent();

            if ($this->notificationCallback !== null) {
                $notification = $this->evaluate($this->notificationCallback, [
                    'notification' => $notification,
                    'context' => $context,
                ]) ?? $notification;
            }

            $notification->send();

            return new RedirectResponse($redirectUrl);
        }

        return null;
    }

    protected function usesInteractsWithRecordTrait(Page $page): bool
    {
        return array_key_exists(
            'Filament\\Resources\\Pages\\Concerns\\InteractsWithRecord',
            class_uses_recursive($page),
        );
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
