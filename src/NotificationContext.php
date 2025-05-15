<?php

namespace Padmission\MissingRecordRedirect;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

readonly class NotificationContext
{
    /**
     * @param  class-string<\Filament\Resources\Resource>  $resourceClass
     */
    public function __construct(
        protected string $resourceClass,
        protected ModelNotFoundException $exception,
        protected Request $request,
    ) {}

    /**
     * @return class-string<\Filament\Resources\Resource>
     */
    public function getResource(): string
    {
        return $this->resourceClass;
    }

    /**
     * Get the request instance
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the exception instance
     */
    public function getException(): ModelNotFoundException
    {
        return $this->exception;
    }
}
