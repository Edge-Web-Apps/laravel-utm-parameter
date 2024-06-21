# Laravel UTM-Parameters

[![Latest Version on Packagist](https://img.shields.io/packagist/v/suarez/laravel-utm-parameter.svg?style=flat-square)](https://packagist.org/packages/suarez/laravel-utm-parameter)
[![StyleCI](https://github.styleci.io/repos/448347178/shield?branch=main)](https://github.styleci.io/repos/448347178?branch=main)
[![Test PHP 8.x](https://github.com/toni-suarez/laravel-utm-parameter/actions/workflows/tests-php8.yml/badge.svg?branch=main)](https://github.com/toni-suarez/laravel-utm-parameter/actions/workflows/tests-php8.yml)
[![Packagist Downloads](https://img.shields.io/packagist/dt/suarez/laravel-utm-parameter)](https://packagist.org/packages/suarez/laravel-utm-parameter)
![GitHub](https://img.shields.io/github/license/toni-suarez/laravel-utm-parameter)
[![Statamic Addon](https://img.shields.io/badge/https%3A%2F%2Fstatamic.com%2Faddons%2Ftoni-suarez%2Futm-parameter?style=flat-square&logo=statamic&logoColor=rgb(255%2C%2038%2C%20158)&label=Statamic&link=https%3A%2F%2Fstatamic.com%2Faddons%2Ftoni-suarez%2Futm-parameter)](https://statamic.com/addons/toni-suarez/utm-parameter)

A lightweight way to handle UTM parameters session-based in your Laravel Application.

```blade
@hasUtm('source', 'newsletter')
  <p>Special Content for Newsletter-Subscriber.</p>
@endhasUtm
```

---

## Table of Content

- [Installation](#installation)
- [Usage](#usage)
- [License](#license)

## Installation

Follow these steps to install the Laravel UTM-Parameters package.

Open your terminal and navigate to your Laravel project directory. Then, use Composer to install the package:

```bash
$ composer require suarez/laravel-utm-parameter
```

Optionally, you can publish the config file of this package with this command:
```bash
php artisan vendor:publish --tag="utm-parameter"
```

### Middleware

Open the `app/Http/Kernel.php` file and add a new item to the `web` middleware group:

```php
# Laravel 10
protected $middlewareGroups = [
    'web' => [
        /* ... keep the existing middleware here */
        \Suarez\UtmParameter\Middleware\UtmParameters::class,
    ],
];
```

To enable UTM-Parameters only for certain requests to your site, add a new mapping to the `middlewareAliases` Array.

```php
# Laravel 10
protected $middlewareAliases = [
    /* ... keep the existing mappings here */
    'utm-parameters' => \Suarez\UtmParameter\Middleware\UtmParameters::class,
];
```

To apply UTM-Parameters to specific routes, use the following middleware: `utm-parameters`

```php
Route::middleware('utm-parameters')
  ->get('langing-page/{slug}', 'LandingPageController@show');
```

### Use as Facade

If you prefer not to use it as middleware, you can utilize the UtmParameter Facade directly in your controllers or other parts of your application. Once the `boot($request)`-method is called, you have access to it at any place. For example:

```php
use Suarez\UtmParameter\Facades\UtmParameter;

// Inside a controller method
class IndexController {
  public function index(Request $request)
  {
      UtmParameter::boot($request);
  }
}

class SomeDifferentController {
  public function index(Request $request)
  {
      $source = UtmParameter::get('source');
  }
}
```

## Configuration

The configuration file `config/utm-parameter.php` allows you to control the behavior of the UTM parameters handling.

```php
<?php

return [
  /*
   * Control Overwriting UTM Parameters (default: false)
   *
   * This setting determines how UTM parameters are handled within a user's session.
   *
   * - Enabled (true): New UTM parameters will overwrite existing ones during the session.
   * - Disabled (false): The initial UTM parameters will persist throughout the session.
   */
  'override_utm_parameters' => false,

  /*
   * Session Key for UTM Parameters (default: 'utm')
   *
   * This key specifies the name used to access and store UTM parameters within the session data.
   *
   * If you're already using 'utm' for another purpose in your application,
   * you can customize this key to avoid conflicts.
   * Simply provide your preferred key name as a string value.
   */
  'session_key' => 'utm',

  /*
    * Allowed UTM Parameters (default: ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_campaign_id'])
    *
    * This setting defines the UTM parameters that are allowed within your application.
    *
    * In this array, you can specify a list of allowed UTM parameter names. Each parameter should be listed as a string.
    * Only parameters from this list will be stored and processed in the session.
    * and any parameter without the 'utm_' prefix will be ignored regardless of its inclusion in this list.
    *
    * Example: To only allow the basic UTM parameters (source, medium, and campaign), you could update the array like this:
    *
    * 'allowed_utm_parameters' => [
    *     'utm_source',
    *     'utm_medium',
    *     'utm_campaign',
    * ],
    */
    'allowed_utm_parameters' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_campaign_id'
    ],
];
```


## Usage

### All UTM parameters

To get an array of all UTM parameters, use this helper:  `get_all_utm()`.

```php
$parameter = get_all_utm();
```

###  Get UTM parameter

If you need to retrieve certain UTM parameters, use `get_utm('source|medium|campaign|term|content')`.

```blade
 <p>You came from {{ get_utm('source') }}</p>
```

```php
// Some Task in your Class
public function someTask()
{
  return match(get_utm('source')) {
    'bing' => Bing::class,
    'google' => Google::class,
    'duckduckgo' => DuckDuckGo::class,
    'newsletter' => Newsletter::class,
    default => Default::class
  };
}

// Render a view based on an utm_source
Route::get('/', function () {
  return match(get_utm('source')) {
        'newsletter' => view('newsletter'),
        default => view('welcome')
    };
});
```

### Has UTM parameter

Sometimes you want to show or do something, if user might have some or specific utm-parameters.

Simply use:
- `has_utm('source|medium|campaign|term|content', 'optional-value')`
- `has_not_utm('source|medium|campaign|term|content', 'optional-value')`

```blade
@hasUtm('source', 'corporate-partner')
  <div>Some corporate partner related stuff</div>
@endhasUtm

@hasNotUtm('term')
  <p>You have any term.</p>
@endhasNotUtm
```

```php
 if (has_utm('campaign', 'special-sale')) {
   redirect('to/special-sale/page');
 }

 if (has_not_utm('campaign', 'newsletter')) {
   session()->flash('Did you know, we have a newsletter?');
 }
```

### contains_utm()

You can conditionally show or perform actions based on the presence or absence of a portion of an UTM parameters using contains.

Simply use:
- `contains_utm('source|medium|campaign|term|content', 'value')`
- `contains_not_utm('source|medium|campaign|term|content', 'value')`

```blade
@containsUtm('campaign', 'weekly')
  <div>Some Weekly related stuff</div>
@endhasUtm

@containsNotUtm('campaign', 'sales')
  <p>Some not Sales stuff</p>
@endhasNotUtm
```

```php
 if (contains_utm('campaign', 'weekly')) {
   redirect('to/special/page');
 }

 if (contains_not_utm('campaign', 'sale')) {
   session()->flash('Did you know, we have a newsletter?');
 }
```

## Extending the Middleware

You can extend the middleware to customize the behavior of accepting UTM parameters. For example, you can override the `shouldAcceptUtmParameter` method.

First, create a new middleware using Artisan:

```bash
php artisan make:middleware CustomMiddleware
```

Then, update the new middleware to extend UtmParameters and override the `shouldAcceptUtmParameter` method:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Suarez\UtmParameter\Middleware\UtmParameters;

class CustomMiddleware extends UtmParameters
{
    /**
     * Determines whether the given request/response pair should accept UTM-Parameters.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return bool
     */
    protected function shouldAcceptUtmParameter(Request $request)
    {
        return $request->isMethod('GET') || $request->isMethod('POST');
    }
}
```

Finally, update your `app/Http/Kernel.php` to **replace** the `UtmParameters` with `CustomMiddleware`:

```php
# app/Http/Kernel.php
use App\Http\Middleware\CustomMiddleware;

protected $middlewareGroups = [
    'web' => [
        /* ... keep the existing middleware here */
        CustomMiddleware::class,
    ],
];
```

## Resources
Explore additional use cases and resources on the [wiki pages](https://github.com/toni-suarez/laravel-utm-parameter/wiki)

- [Installation Guide](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Installation-Guide)
- [Installation Guide (Laravel 8.x to 10.x)](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Installation-Guide-(Laravel-8.x-to-10.x))
- [How it works](https://github.com/toni-suarez/laravel-utm-parameter/wiki/How-it-works)
- [Limitations](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Limitations)
- [Advanced Usage](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Advanced-Usage)
- [Blade Usage](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Blade-Usage)
- [Usage via Facade or Helper Class](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Usage-via-Facade-or-Helper-Class)

### Inspirations
- [Use Case: A/B Testing](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use-Case:-A-B-Testing)
- [Use Case: Different Styles for Social Media](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use-Case:-Different-Styles-for-Social-Media)
- [Use Case: Lead Attribution](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use-Case:-Lead-Attribution)
- [Use Case: Social Media Tracking](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use-Case:-Social-Media-Tracking)
- [Use‐Case: Newsletter Redirect on Product Detail Page](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use%E2%80%90Case:-Newsletter-Redirect-on-Product-Detail-Page)
- [Use‐Case: Offline Marketing Integration](https://github.com/toni-suarez/laravel-utm-parameter/wiki/Use%E2%80%90Case:-Offline-Marketing-Integration)

---

## License

The Laravel UTM-Parameters package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
