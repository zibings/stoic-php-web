<?php

	namespace Stoic\Web\Api;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\InvalidRequestException;
	use Stoic\Web\Resources\NonJsonInputException;
	use Stoic\Web\Resources\RequestType;
	use Stoic\Web\Resources\ServerIndices;

	/**
	 * Class to represent a single API request and provide meta information about
	 * the request to handler callbacks.
	 *
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class Request {
		/**
		 * Any available cookie data for request.
		 *
		 * @var ParameterHelper
		 */
		protected $cookie = null;
		/**
		 * Any available input data for the request. Can be JSOn payload or from
		 * request variables.
		 *
		 * @var mixed
		 */
		protected $input = null;
		/**
		 * Whether or not the request is deemed valid.
		 *
		 * @var boolean
		 */
		protected $isValid = false;
		/**
		 * Whether or not the request has a JSON payload.
		 *
		 * @var boolean
		 */
		protected $isJson = false;
		/**
		 * Enumerated value representing the request verb.
		 *
		 * @var RequestType
		 */
		protected $requestType = null;
		/**
		 * Any available server data for the request.
		 *
		 * @var ParameterHelper
		 */
		protected $server = null;


		/**
		 * Instantiates a new Request object, either taking information provided or
		 * pulling automatically from the $_COOKIE, $_GET, and $_SERVER
		 * superglobals as well as the `php://input` stream.
		 *
		 * @param array $server Optional server collection to use in place of $_SERVER superglobal.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @param array $get Optional get collection to use in place of $_GET superglobal.
		 * @param array $cookie Optional cookie collection to use in place of $_COOKIE superglobal.
		 * @throws InvalidRequestException
		 */
		public function __construct(array $server = null, $input = null, array $get = null, array $cookie = null) {
			$this->get = new ParameterHelper($get ?? $_GET);
			$this->cookie = new ParameterHelper($cookie ?? $_COOKIE);
			$this->server = new ParameterHelper($server ?? $_SERVER);
			$this->requestType = new RequestType(RequestType::ERROR);

			if (!$this->server->has(ServerIndices::REQUEST_METHOD)) {
				throw new InvalidRequestException("Server collection was missing '" . ServerIndices::REQUEST_METHOD . "' value");
			}

			$reqMeth = strtoupper($this->server->getString(ServerIndices::REQUEST_METHOD));
			$this->requestType = RequestType::fromString($reqMeth);

			if ($this->requestType->getValue() === null) {
				throw new InvalidRequestException("Invalid request method provided: {$reqMeth}");
			}

			if ($this->requestType->is(RequestType::GET)) {
				$this->isValid = true;
			} else {
				if ($input !== null) {
					$this->isValid = true;
					$this->input = $input;
				} else {
					if ($this->input === null) {
						$this->readInput();
					}

					if (empty($this->input)) {
						return;
					}

					// @codeCoverageIgnoreStart
					$this->isValid = true;

					json_decode(trim($this->input), true);

					if (json_last_error() == JSON_ERROR_NONE) {
						$this->isJson = true;
					}
					// @codeCoverageIgnoreEnd
				}
			}

			return;
		}

		/**
		 * Returns a ParameterHelper containing the contents of the $_COOKIE
		 * collection.
		 *
		 * @throws InvalidRequestException
		 * @return ParameterHelper
		 */
		public function getParameterizedCookie() : ParameterHelper {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			return $this->cookie;
		}

		/**
		 * Returns a ParameterHelper containing the contents of the $_GET
		 * collection.
		 *
		 * @return ParameterHelper
		 */
		public function getParameterizedGet() : ParameterHelper {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			return $this->get;
		}

		/**
		 * Returns the request input payload as a ParameterHelper object. If the
		 * request is a GET, the $_GET collection will be returned. If the request
		 * doesn't have a JSON payload, an exception is thrown.
		 *
		 * @throws InvalidRequestException
		 * @throws NonJsonInputException
		 * @return ParameterHelper
		 */
		public function getParameterizedInput() : ParameterHelper {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			if ($this->requestType->is(RequestType::GET)) {
				return $this->get;
			}

			if (!$this->isJson) {
				throw new NonJsonInputException("Can't get parameterized input for non-json payload");
			}

			// @codeCoverageIgnoreStart
			return new ParameterHelper(json_decode($this->input, true));
			// @codeCoverageIgnoreEnd
		}

		/**
		 * Returns the raw input value from the request.
		 *
		 * @throws InvalidRequestException
		 * @return mixed
		 */
		public function getRawInput() {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			return $this->input;
		}

		/**
		 * Returns a copy of the internal RequestType object.
		 *
		 * @return RequestType
		 */
		public function getRequestType() : RequestType {
			return clone $this->requestType;
		}

		/**
		 * Indicates whether or not the request is 'valid' based on the input
		 * data.
		 *
		 * @return boolean
		 */
		public function isValid() : bool {
			return $this->isValid;
		}

		/**
		 * Internal method to attempt reading the PHP input stream.
		 *
		 * @codeCoverageIgnore
		 * @return void
		 */
		protected function readInput() : void {
			if ($this->input === null) {
				try {
					if (($this->input = @file_get_contents("php://input")) === false) {
						$this->input = '';
					}
				} catch (\Exception $ex) {
					$this->input = '';
				}
			}

			return;
		}
	}
