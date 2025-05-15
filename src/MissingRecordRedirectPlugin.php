<?php

namespace Padmission\MissingRecordRedirect;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MissingRecordRedirectPlugin implements Plugin
{
    use EvaluatesClosures;

    protected ?Closure $notificationCallback = null;

    protected string | Closure | null $notificationTitle = null;

    protected string | Closure | null $notificationBody = null;

    protected string | Closure | null $notificationType = 'warning';

    protected bool | Closure $isPersistent = true;

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
        //
    }

    public function notification(?Closure $callback = null): static
    {
        $this->notificationCallback = $callback;

        return $this;
    }

    public function notificationTitle(string | Closure | null $title): static
    {
        $this->notificationTitle = $title;

        return $this;
    }

    public function getNotificationTitle(): string
    {
        return $this->evaluate($this->notificationTitle) ?? 'Record Deleted';
    }

    public function notificationBody(string | Closure | null $body): static
    {
        $this->notificationBody = $body;

        return $this;
    }

    public function getNotificationBody(): string
    {
        return $this->evaluate($this->notificationBody) ?? 'The record you were trying to view has been deleted or does not exist.';
    }

    public function notificationType(string | Closure | null $type): static
    {
        $this->notificationType = $type;

        return $this;
    }

    public function getNotificationType(): string
    {
        return $this->evaluate($this->notificationType) ?? 'warning';
    }

    public function persistent(bool | Closure $isPersistent = true): static
    {
        $this->isPersistent = $isPersistent;

        return $this;
    }

    public function isPersistent(): bool
    {
        return $this->evaluate($this->isPersistent);
    }

    public function getNotification(array $data = []): Notification
    {
        $notification = Notification::make()
            ->title($this->getNotificationTitle())
            ->body($this->getNotificationBody());

        $type = $this->getNotificationType();
        if (method_exists($notification, $type)) {
            $notification->{$type}();
        }

        if ($this->isPersistent()) {
            $notification->persistent();
        }

        if ($this->notificationCallback !== null) {
            $notification = $this->evaluate($this->notificationCallback, [
                'notification' => $notification,
                ...$data,
            ]) ?? $notification;
        }

        return $notification;
    }

    public function handleNotFoundHttpException(NotFoundHttpException $e, Request $request, Panel $panel): ?RedirectResponse
    {
        $previous = $e->getPrevious();

        if (! $previous instanceof ModelNotFoundException) {
            return null;
        }

        $route = $request->route();

        if (! $route instanceof Route) {
            return null;
        }

        $controller = $route->getController();

        if (! $controller instanceof ViewRecord) {
            return null;
        }

        $resource = $controller::getResource();

        $redirectUrl = $resource::getUrl();
        $currentUrl = $request->url();

        if ($redirectUrl !== $currentUrl) {
            $model = $previous->getModel();

            $notification = $this->getNotification([
                'resource' => $resource,
                'model' => $model,
                'request' => $request,
                'previousException' => $previous,
            ]);

            $notification->send();

            return new RedirectResponse($redirectUrl);
        }

        return null;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
