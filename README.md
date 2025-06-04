## PSR-17

This is an implementation of the [PSR-17](https://www.php-fig.org/psr/psr-17/) specification.

Besides the psr methods, the factories also include some useful ones:

### ResponseFactory

```php
use AdinanCenci\Psr17\ResponseFactory;

$responseFactory = new ResponseFactory();

// Returns an instance of Psr\Http\Message\ResponseInterface with
// code 200
$factory->ok('your body here');

// 201
$factory->created('your body here');

// 301
$factory->movedPermanently('http://redirect.to');

// 302
$factory->movedTemporarily('http://redirect.to');

// 400
$factory->badRequest('your body here');

// 401
$factory->unauthorized('your body here');

// 403
$factory->forbidden('your body here');

// 404
$factory->notFound('your body here');

// 500
$factory->internalServerError('your body here');

// 501
$factory->notImplemented('your body here');

// 502
$factory->badGateway('your body here');

// 503
$factory->serviceUnavailable('your body here');
```

### ServerRequestFactory

```php
use AdinanCenci\Psr17\ServerRequestFactory;

$requestFactory = new ServerRequestFactory();

// Creates an instance of Psr\Http\Message\ServerRequestInterface
// out of the global values.
$request = $requestFactory->createFromGlobals();
```

### UploadedFileFactory

```php
use AdinanCenci\Psr17\UploadedFileFactory;

$filesFactory = new UploadedFileFactory();

// Will return the contents of $_FILES as 
// Psr\Http\Message\UploadedFileInterface instances.
$files = $filesFactory->getFilesFromGlobals();
```

<br><br><br>

## Licence

Mit
