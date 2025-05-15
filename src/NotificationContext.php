<?php

namespace Padmission\MissingRecordRedirect;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

readonly class NotificationContext
{
    /**
     * @param  class-string<\Filament\Resources\Resource>  $resourceClass
     */
    public function __construct(
        protected string $resourceClass,
        protected Page $page,
        protected Request $request,
        protected ModelNotFoundException $exception,
    ) {}

    /**
     * @return class-string<\Filament\Resources\Resource>
     */
    public function getResource(): string
    {
        return $this->resourceClass;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function isViewPage(): bool
    {
        return $this->page instanceof ViewRecord;
    }

    public function isEditPage(): bool
    {
        return $this->page instanceof EditRecord;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): ModelNotFoundException
    {
        return $this->exception;
    }
}
