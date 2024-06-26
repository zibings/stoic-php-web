<?php

	namespace Stoic\Web\Api;

	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\NodeBase;
	use Stoic\Log\Logger;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\ApiAuthorizationDispatch;
	use Stoic\Web\Resources\ApiEndpoint;
	use Stoic\Web\Resources\AuthorizationDispatchStrings;
	use Stoic\Web\Resources\HttpStatusCodes;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\ServerIndices;
	use Stoic\Web\Resources\SettingsStrings;

	/**
	 * Specialized version of Stoic singleton-ish class to more strictly coordinate API routing.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class Stoic extends \Stoic\Web\Stoic {
		/**
		 * Chain to process authorization for requests.
		 *
		 * @var null|ChainHelper
		 */
		protected ?ChainHelper $authChain = null;
		/**
		 * Endpoint to serve if no route is matched.
		 *
		 * @var null|ApiEndpoint
		 */
		protected ?ApiEndpoint $defaultEndpoint = null;
		/**
		 * Collection of registered endpoints.
		 *
		 * @var array
		 */
		protected array $endpoints = [];


		/**
		 * Static method to retrieve the most recent singleton instance for the system.  If instance exists but the Logger
		 * and PageVariables arguments are provided, a new instance is created and returned from the stack. If the instance
		 * doesn't exist, one is created.
		 *
		 * @param null|string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param null|PageVariables $variables Collection of 'predefined' variables, if not supplied an instance is created from globals.
		 * @param null|Logger $log Logger instance for use by instance, if not supplied a new instance is used.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @return Stoic
		 */
		public static function getInstance(?string $corePath = null, ?PageVariables $variables = null, ?Logger $log = null, mixed $input = null) : static {
			$instance = parent::getInstance($corePath, $variables, $log, $input);

			if ($instance->authChain === null) {
				// @codeCoverageIgnoreStart
				$instance->authChain = new ChainHelper(false, true);
				$instance->authChain->hookLogger([$instance->log, 'debug']);
				// @codeCoverageIgnoreEnd
			}

			return $instance;
		}


		/**
		 * Attempts to handle the API request, outputting some default headers (Content-Type, Access-Control-Allow-Origin,
		 * and Cache-Control). Option to override URL parameter in case a change is made to the .htaccess rules.
		 *
		 * @param string $urlParam Optional string value to change parameter for URL delivery via .htaccess, defaults to 'url'.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return void
		 */
		public function handle(string $urlParam = 'url') : void {
			if (empty($urlParam)) {
				return;
			}

			$req    = $this->getRequest();
			$config = $this->getConfig();
			$server = $req->getServer();
			$get    = $req->getGet();

			$this->setHeader('Access-Control-Allow-Credentials', 'true');
			$this->setHeader('Cache-Control', $config->get(SettingsStrings::API_CACHE_CONTROL, 'max-age=500'));
			$this->setHeader('Content-Type', $config->get(SettingsStrings::API_CONTENT_TYPE, 'application/json'));

			$corsAllowedOrigin = '*';

			if ($server->has(ServerIndices::HTTP_ORIGIN)) {
				$allowedOrigins = [];

				foreach ($config->get(SettingsStrings::CORS_ORIGINS, []) as $origin) {
					$allowedOrigins[$origin] = true;
				}

				if (isset($allowedOrigins['*']) || isset($allowedOrigins[$server->getString(ServerIndices::HTTP_ORIGIN, '')])) {
					$corsAllowedOrigin = $server->getString(ServerIndices::HTTP_ORIGIN, '*');
				}
			}

			$this->setHeader('Access-Control-Allow-Origin', $corsAllowedOrigin);

			if ($server->has(ServerIndices::REQUEST_METHOD) && $server->getString(ServerIndices::REQUEST_METHOD, '') == 'OPTIONS' && $server->has('HTTP_ACCESS_CONTROL_REQUEST_METHOD')) {
				$this->setRawHeader('HTTP/1.1 200 OK');
				$this->setHeader('Access-Control-Allow-Headers', $config->get(SettingsStrings::CORS_HEADERS, 'Accept, Authorization, Content-Type, X-CSRF-Token, App-Token, Token'));
				$this->setHeader('Access-Control-Allow-Methods', $config->get(SettingsStrings::CORS_METHODS, 'POST, GET, OPTIONS'));

				exit;
			}

			if (!$get->has($urlParam)) {
				if ($this->defaultEndpoint !== null) {
					$this->handleUserFunc($this->defaultEndpoint->callback, $req);

					return;
				}

				$this->setHttpResponseCode(HttpStatusCodes::NOT_FOUND);
				echo(json_encode('Invalid URL'));

				return;
			}

			$handled = false;
			$type    = $req->getRequestType();
			$url     = $get->getString($urlParam);
			$script  = $server->getString(\Stoic\Web\Resources\ServerIndices::SCRIPT_NAME);
			$url     = str_replace(substr($script, 0, strripos($script, '/') + 1), "", $url);

			if (array_key_exists($type->getName(), $this->endpoints) !== false) {
				$vendpoints = $this->endpoints[$type->getName()];

				foreach ($vendpoints as $pattern => $ep) {
					if (preg_match($pattern, $url, $matches, PREG_OFFSET_CAPTURE) === 1) {
						if ($ep->authRoles !== null && $ep->authRoles !== false) {
							$disp = new ApiAuthorizationDispatch();
							$disp->initialize([
								AuthorizationDispatchStrings::INDEX_INPUT => $req->getInput(),
								AuthorizationDispatchStrings::INDEX_ROLES => $ep->authRoles
							]);

							if (!$this->authChain->traverse($disp, $this)) {
								$this->setHttpResponseCode(HttpStatusCodes::FORBIDDEN);
								echo(json_encode("Unable to perform authorization"));

								return;
							}

							if (!$disp->isAuthorized()) {
								$this->setHttpResponseCode(HttpStatusCodes::FORBIDDEN);
								echo(json_encode("Unauthorized access for auth-only endpoint"));

								return;
							}
						}

						$handled = true;
						$this->handleUserFunc($ep->callback, $req, $matches);

						break;
					}
				}
			}

			if (!$handled) {
				if ($this->defaultEndpoint !== null) {
					$this->handleUserFunc($this->defaultEndpoint->callback, $req);

					return;
				}

				$this->setHttpResponseCode(HttpStatusCodes::NOT_FOUND);
				echo(json_encode("URL mis-match"));
			}

			return;
		}

		/**
		 * Internal method to consistently handle output from an API endpoint.
		 *
		 * @param callable $callback Endpoint callback to execute.
		 * @param Request $request API request that is being passed to endpoint.
		 * @param null|array $matches Array of matches (if any) from URL pattern match.
		 * @return void
		 */
		protected function handleUserFunc(callable $callback, Request $request, ?array $matches = null) : void {
			$out = null;

			ob_start();
			$out = call_user_func($callback, $request, $matches ?? []);
			ob_end_clean();

			if ($out !== null) {
				if ($out instanceof Response) {
					$outData = json_encode($out->getData());
					$this->setHttpResponseCode($out->getStatus()->getValue());

					if ($outData === false) {
						// @codeCoverageIgnoreStart
						echo(json_last_error_msg());
						// @codeCoverageIgnoreEnd
					} else {
						echo($outData);
					}
				} else {
					// @codeCoverageIgnoreStart
					echo($out);
					// @codeCoverageIgnoreEnd
				}
			}

			return;
		}

		/**
		 * Links a processing node into the authorization chain.
		 *
		 * @param NodeBase $node Processing node to make part of chain.
		 * @return void
		 */
		public function linkAuthorizationNode(NodeBase &$node) : void {
			$this->authChain->linkNode($node);

			return;
		}

		/**
		 * Adds an endpoint callback to the internal collection.
		 *
		 * @param null|string $verbs String value of applicable request verbs for endpoint, '*' for all verbs or use pipe (|) to combine multiple verbs.
		 * @param null|string $pattern String value of URL pattern for endpoint, `null` will set this endpoint as the 'default'.
		 * @param callable $callback Endpoint callback for use when matched.
		 * @param mixed $authRoles Optional string, array of string values, or boolean value representing authorization requirements for endpoint.
		 * @return void
		 */
		public function registerEndpoint(?string $verbs, ?string $pattern, callable $callback, mixed $authRoles = null) : void {
			$ep = new ApiEndpoint($authRoles, $callback, $pattern);

			if ($pattern === null) {
				$this->defaultEndpoint = $ep;

				return;
			}

			$v = $this->splitVerbs($verbs);

			foreach ($v as $verb) {
				if (array_key_exists($verb, $this->endpoints) === false) {
					$this->endpoints[$verb] = [];
				}

				if (array_key_exists($pattern, $this->endpoints[$verb]) === false) {
					$this->endpoints[$verb][$pattern] = $ep;
				}
			}

			return;
		}

		/**
		 * Sets and sends the HTTP response code.
		 *
		 * @codeCoverageIgnore
		 * @param int $code Integer value of response code.
		 * @return void
		 */
		public function setHttpResponseCode(int $code) : void {
			if (!function_exists('http_response_code')) {
				$this->setHeader('X-PHP-Response-Code', $code, true, $code);
			} else {
				@http_response_code($code);
			}

			return;
		}

		/**
		 * Internal method to return array of verbs given a string that can be pipe-delimited.  The '*' character returns
		 * all verbs.
		 *
		 * @param string $verbs String value to split into verb array.
		 * @return string[]
		 */
		protected function splitVerbs(string $verbs) : array {
			if ($verbs == '*') {
				return [
					'DELETE',
					'ERROR',
					'GET',
					'HEAD',
					'OPTIONS',
					'POST',
					'PUT'
				];
			}

			return explode('|', $verbs);
		}
	}
