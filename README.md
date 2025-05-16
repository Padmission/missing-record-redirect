# Missing Record Redirect

[![Latest Version on Packagist](https://img.shields.io/packagist/v/padmission/missing-record-redirect.svg?style=flat-square)](https://packagist.org/packages/padmission/missing-record-redirect)
[![Total Downloads](https://img.shields.io/packagist/dt/padmission/missing-record-redirect.svg?style=flat-square)](https://packagist.org/packages/padmission/missing-record-redirect)

A Filament plugin that provides elegant handling of "record not found" exceptions. When users try to view or edit a non-existent record in Filament, they'll be redirected to the resource's index page with a customizable notification instead of seeing an error page.

## Features

- Seamlessly redirects users when accessing non-existent records
- Displays customizable notifications explaining what happened
- Custom redirect destinations based on context
- Supports all resource pages using the `InteractsWithRecord` trait
- Fully customizable notification appearance and behavior
- Granular control over which resources and pages are handled
- Simple integration with any Filament panel

## Installation

You can install the package via composer:

```bash
composer require padmission/missing-record-redirect
```

## Usage

Add the plugin to your Filament panel in a panel provider:

```php
use Padmission\MissingRecordRedirect\MissingRecordRedirectPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            MissingRecordRedirectPlugin::make()
        );
}
```

The plugin will now handle missing record exceptions by redirecting users to the resource index page with a notification.

## Configuration

### Customizing the Notification

You can customize the notification that is shown to users:

```php
// Set simple text properties
MissingRecordRedirectPlugin::make()
    ->notificationTitle('Record Not Available')
    ->notificationBody('The record you were trying to access does not exist.')
    
// Or use the notification callback for advanced customization
MissingRecordRedirectPlugin::make()
    ->notification(function (Notification $notification, NotificationContext $context): Notification {
        $action = $context->isEditPage() ? 'edit' : 'view';
        
        return $notification
            ->title('Record Not Found')
            ->body("The {$context->getResource()::getModelLabel()} you were trying to {$action} has been deleted or does not exist.")
            ->warning()
            ->persistent();
    })
```

### Custom Redirect URL

Change where users are redirected when a record is not found:

```php
// Set a static URL
MissingRecordRedirectPlugin::make()
    ->redirectUrl('/admin/dashboard')
    
// Or use a callback for dynamic URLs based on context
MissingRecordRedirectPlugin::make()
    ->redirectUrl(function (NotificationContext $context) {
        // For edit pages, redirect to create page
        if ($context->isEditPage()) {
            return $context->getResource()::getUrl('create');
        }
        
        // Default to resource list
        return $context->getResource()::getUrl();
    })
```

### Excluding Resources

You can exclude specific resources, models, or page types from being handled by the plugin:

```php
MissingRecordRedirectPlugin::make()
    // Exclude specific resources
    ->excludeResources(
        App\Filament\Resources\UserResource::class,
        App\Filament\Resources\ProductResource::class
    )
    
    // Exclude specific models
    ->excludeModels(
        App\Models\Setting::class,
        App\Models\SystemLog::class
    )
    
    // Exclude specific resource page classes
    ->excludePages(
        App\Filament\Resources\PostResource\Pages\EditPost::class
    )
```

### Advanced Exception Handling

For complete control over exception handling:

```php
MissingRecordRedirectPlugin::make()
    ->handleException(function (NotFoundHttpException $exception, Request $request) {
        // Custom exception handling logic
        // Return a RedirectResponse or null
    })
```

## NotificationContext API

The `NotificationContext` object provides access to information about the current request:

| Method           | Description                    |
|------------------|--------------------------------|
| `getResource()`  | Get the resource class name    |
| `getPage()`      | Get the page instance          |
| `getRequest()`   | Get the current request        |
| `getException()` | Get the ModelNotFoundException |
| `isViewPage()`   | Check if this is a view page   |
| `isEditPage()`   | Check if this is an edit page  |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Padmission](https://github.com/Padmission)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
