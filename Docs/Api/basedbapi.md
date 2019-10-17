## BaseDbAPI Class
The `BaseDbApi` class is provided on top of the [Stoic\Pdo\BaseDbClass](https://github.com/zibings/stoic-php-pdo/blob/master/Pdo/BaseDbClass.php) class to
provide basic methods that are commonly used within API endpoints.

### Usage
Using the `BaseDbApi` class is straightforward.  Simply inherit the class into a new class for your
endpoints, and every then you'll be forced to provide a `PDO` instance (and optionally a [Logger](https://github.com/zibings/stoic-php-core/blob/master/Docs/Logging/index.md) instance)
for the class.

```php
<?php

	class MyApiClass extends Stoic\Web\Api\BaseDbApi {
		public function anEndpoint(Stoic\Web\Request $request, array $matches = null) : Stoic\Web\Api\Response {
			/* ... */
		}
	}

	$myApiClass = new MyApiClass(); // Will throw an error without a PDO object
```

### Methods
- [public:Stoic\Web\Api\Response] `newResponse()` -> Creates a new [Stoic\Web\Api\Response](response.md) object with a default `OK` HTTP status
- [public:bool] `requestHasInputVars(Stoic\Web\Request $request, array $keysToFind)` -> Checks the parameterized input from the given request to ensure it has the provided keys