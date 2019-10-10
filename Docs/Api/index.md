# Stoic API Components
Collection of specialized components to aid in building straightforward API's.

## Approach
Since API's rely heavily on the composition of their URL's, it was appropriate
to offer a semi-strict set of classes to facilitate building API endpoints that
live within a single directory.  This should feel very similar to an MVC-type
pattern, but still requires you to explicitly build your own entry file (a step
that encourages transparency for program flow).

## Typical API Structure
A simple API may look similar to this in the filesystem:
```
/myApiRoot/1/                <- versioned API folder
/myApiRoot/1/.htaccess       <- enables URL rewrites
/myApiRoot/1/counter.api.php <- typical endpoint
/myApiRoot/1/index.php       <- entry file, loads all *.api.php files in directory
/myApiRoot/index.php         <- blank file for obfuscation
```

Entry file (/myApiRoot/1/index.php):
```php
<?php

	require('vendor/autoload.php');

	use Stoic\Web\Api\Stoic;

	// Grab Stoic API instance, pointing at the top directory as the 'root' (location of 'vendor/', etc)
	$stoic = Stoic::getInstance('../');

	// Pull in all files in the '~/1/' (resolves to '../1/') directory ending in '.api.php'
	$stoic->loadFilesByExtension('~/1/', '.api.php');

	// Trigger the API 
	$stoic->handle();
```

Counter endpoint (/myApiRoot/1/counter.api.php):
```php
<?php

	// Common elements used in this endpoint
	use Stoic\Web\Api\BaseDbApi;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Request;

	// BaseDbApi classes require PDO and Logger objects at initialization
	class CounterApi extends BaseDbApi {
		public $count = 0;


		/**
		 * Decrements the internal count by one.
		 *
		 * @param Request $request The current request which routed to this endpoint.
		 * @param array $matches Array of matches returned by the route regular expression.
		 * @return Response
		 */
		public function countDown(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$ret->setData(--$this->count);

			return $ret;
		}

		/**
		 * Increments the internal count by one.
		 *
		 * @param Request $request The current request which routed to this endpoint.
		 * @param array $matches Array of matches returned by the route regular expression.
		 * @return Response
		 */
		public function countUp(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$ret->setData(++$this->count);

			return $ret;
		}
	}

	// Grabs the current Stoic instance
	$stoic = Stoic::getInstance();

	// Creates an object for these endpoints
	$counterApi = new CounterApi($stoic->getDb(), $stoic->getLog());

	// Register the endpoints
	$stoic->registerEndpoint('POST', "/^count\/down\/?$/", [$counterApi, 'countDown']);
	$stoic->registerEndpoint('POST', "/^count\/up\/?$/",   [$counterApi, 'countUp']);
```

## Further Reading
* [BaseDbAPI](basedbapi.md)
* [Response](response.md)
* [Stoic](stoic.md)