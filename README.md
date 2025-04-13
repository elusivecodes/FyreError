# FyreError

**FyreError** is a free, open-source error handling library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Middleware](#middleware)
- [Exceptions](#exceptions)
    - [Http Exceptions](#http-exceptions)



## Installation

**Using Composer**

```
composer require fyre/error
```

In PHP:

```php
use Fyre\Error\ErrorHandler;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$io` is a [*Console*](https://github.com/elusivecodes/FyreConsole).
- `$logManager` is a [*LogManager*](https://github.com/elusivecodes/FyreLog).
- `$eventManager` is an [*EventManager*](https://github.com/elusivecodes/FyreEvent).
- `$config` is a [*Config*](https://github.com/elusivecodes/FyreConfig).

```php
$errorHandler = new ErrorHandler($container, $io, $logManager, $eventManager, $config);
```

Default configuration options will be resolved from the "*Error*" key in the [*Config*](https://github.com/elusivecodes/FyreConfig).

- `$options` is an array containing the configuration options.
    - `level` is a number representing the error reporting level, and will default to `E_ALL`.
    - `renderer` is a *Closure* that will be used to render an *Exception*, and will default to *null*.
    - `log` is a boolean indicating whether to log exception messages, and will default to *true*.

```php
$container->use(Config::class)->set('Error', $options);
```

**Autoloading**

It is recommended to bind the *ErrorHandler* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(ErrorHandler::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$errorHandler = $container->use(ErrorHandler::class);
```


## Methods

**Disable CLI**

Disable CLI error handling.

```php
$errorHandler->disableCli();
```

**Enable CLI**

Enable CLI error handling.

```php
$errorHandler->enableCli();
```

**Get Exception**

Get the current *Exception*.

```php
$exception = $errorHandler->getException();
```

**Get Renderer**

Get the error renderer.

```php
$renderer = $errorHandler->getRenderer();
```

**Register**

Register the error handler.

```php
$errorHandler->register();
```

**Render**

Render an *Exception*.

```php
$response = $errorHandler->render($exception);
```

**Set Renderer**

Set the error renderer.

- `$renderer` is a *Closure* that accepts an *Exception* as the first argument.

```php
$errorHandler->setRenderer($renderer);
```

The renderer should return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses) or a string.

**Unregister**

Unregister the error handler.


## Middleware

```php
use Fyre\Error\Middleware\ErrorHandlerMiddleware;
```

- `$errorHandler` is an *ErrorHandler*.

```php
$middleware = new ErrorHandlerMiddleware($errorHandler);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$middleware = $container->use(ErrorHandlerMiddleware::class);
```

**Handle**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$next` is a *Closure*.

```php
$response = $middleware->handle($request, $next);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).


## Exceptions

Custom exceptions can be created by extending the `Fyre\Error\Exceptions\Exception` class.

- `$message` is a string representing the error message.
- `$code` is a number representing the error code, and will default to *500*.
- `$previous` is an *Exception* representing the previous exception, and will default to *null*.

```php
new Exception($message, $code, $previous);
```


### Http Exceptions

**Bad Request**

400 Bad Request error.

```php
use Fyre\Error\Exceptions\BadRequestException;
```

**Unauthorized**

401 Unauthorized error.

```php
use Fyre\Error\Exceptions\UnauthorizedException;
```

**Forbidden**

403 Forbidden error.

```php
use Fyre\Error\Exceptions\Forbidden;
```

**Not Found**

404 Not Found error.

```php
use Fyre\Error\Exceptions\NotFoundException;
```

**Method Not Allowed**

405 Method Not Allowed error.

```php
use Fyre\Error\Exceptions\MethodNotAllowedException;
```

**Not Acceptable**

406 Not Acceptable error.

```php
use Fyre\Error\Exceptions\NotAcceptableException;
```

**Conflict**

409 Conflict error.

```php
use Fyre\Error\Exceptions\ConflictException;
```

**Gone**

410 Gone error.

```php
use Fyre\Error\Exceptions\GoneException;
```

**Internal Server**

500 Internal Server error.

```php
use Fyre\Error\Exceptions\InternalServerException;
```

**Not Implemented**

501 Not Implemented error.

```php
use Fyre\Error\Exceptions\NotImplementedException;
```

**Service Unavailable**

503 Service Unavailable error.

```php
use Fyre\Error\Exceptions\ServiceUnavailableException;
```