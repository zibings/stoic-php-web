<?php

	namespace Stoic\Web;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\InvalidRequestException;
	use Stoic\Web\Resources\NonJsonInputException;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\RequestType;
	use Stoic\Web\Resources\ServerIndices;

	/**
	 * Class to represent a single API request and provide meta information about the request to handler callbacks.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class Request {
		/**
		 * Any available input data for the request. Can be JSON payload or from request variables.
		 *
		 * @var mixed
		 */
		protected mixed $input = null;
		/**
		 * Whether the request is deemed valid.
		 *
		 * @var bool
		 */
		protected bool $isValid = false;
		/**
		 * Whether the request has a JSON payload.
		 *
		 * @var bool
		 */
		protected bool $isJson = false;
		/**
		 * Enumerated value representing the request verb.
		 *
		 * @var null|RequestType
		 */
		protected ?RequestType $requestType = null;
		/**
		 * Local instance of 'predefined' global variables.
		 *
		 * @var null|PageVariables
		 */
		protected ?PageVariables $variables = null;


		/**
		 * Instantiates a new Request object, either taking information provided or pulling automatically from the
		 * $_COOKIE, $_GET, and $_SERVER superglobals as well as the `php://input` stream.
		 *
		 * @param null|PageVariables $variables Optional variable values to supply instead of using superglobals.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @throws InvalidRequestException|\ReflectionException
		 */
		public function __construct(?PageVariables $variables = null, mixed $input = null) {
			$this->requestType = new RequestType(RequestType::ERROR);
			$this->variables = $variables ?? PageVariables::fromGlobals();

			$server = $this->getServer();

			if (!$server->has(ServerIndices::REQUEST_METHOD)) {
				throw new InvalidRequestException("Server collection was missing '" . ServerIndices::REQUEST_METHOD . "' value");
			}

			$reqMeth = strtoupper($server->getString(ServerIndices::REQUEST_METHOD));
			$this->requestType = RequestType::fromString($reqMeth);

			if ($this->requestType->getValue() === null) {
				throw new InvalidRequestException("Invalid request method provided: {$reqMeth}");
			}

			if (!$this->requestType->is(RequestType::GET)) {
				if ($input !== null) {
					$this->input = $input;
				} else {
					if ($this->input === null) {
						$this->readInput();
					}

					if (empty($this->input)) {
						return;
					}

					// @codeCoverageIgnoreStart
					json_decode(trim($this->input), true);

					if (json_last_error() == JSON_ERROR_NONE) {
						$this->isJson = true;
					}
					// @codeCoverageIgnoreEnd
				}
			}

			$this->isValid = true;

			return;
		}

		/**
		 * Retrieves the contents of the provided $_COOKIE collection.
		 *
		 * @return ParameterHelper
		 */
		public function getCookies() : ParameterHelper {
			return new ParameterHelper($this->variables->cookie);
		}

		/**
		 * Retrieves the contents of the provided $_ENV collection.
		 *
		 * @return ParameterHelper
		 */
		public function getEnv() : ParameterHelper {
			return new ParameterHelper($this->variables->env);
		}

		/**
		 * Retrieves the contents of the provided $_FILES collection.
		 *
		 * @return FileUploadHelper
		 */
		public function getFiles() : FileUploadHelper {
			return new FileUploadHelper($this->variables->files);
		}

		/**
		 * Retrieves the contents of the provided $_GET collection.
		 *
		 * @return ParameterHelper
		 */
		public function getGet() : ParameterHelper {
			return new ParameterHelper($this->variables->get);
		}

		/**
		 * Returns the request input payload as a ParameterHelper object. If the request is a GET, the $_GET collection
		 * will be returned. If the request doesn't have a JSON payload, an exception is thrown.
		 *
		 * @throws InvalidRequestException|NonJsonInputException
		 * @return ParameterHelper
		 */
		public function getInput() : ParameterHelper {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			if ($this->requestType->is(RequestType::GET)) {
				return $this->getGet();
			}

			if (!$this->isJson) {
				throw new NonJsonInputException("Can't get parameterized input for non-json payload");
			}

			if ($this->input === '') {
				return new ParameterHelper([]);
			}

			// @codeCoverageIgnoreStart
			return new ParameterHelper(json_decode($this->input, true));
			// @codeCoverageIgnoreEnd
		}

		/**
		 * Retrieves the contents of the provided $_POST collection.
		 *
		 * @return ParameterHelper
		 */
		public function getPost() : ParameterHelper {
			return new ParameterHelper($this->variables->post);
		}

		/**
		 * Returns the raw input value from the request.
		 *
		 * @throws InvalidRequestException
		 * @return mixed
		 */
		public function getRawInput() : mixed {
			if (!$this->isValid) {
				// @codeCoverageIgnoreStart
				throw new InvalidRequestException("Can't get input on an invalid request");
				// @codeCoverageIgnoreEnd
			}

			return $this->input;
		}

		/**
		 * Retrieves the contents of the provided $_REQUEST collection.
		 *
		 * @return ParameterHelper
		 */
		public function getRequest() : ParameterHelper {
			return new ParameterHelper($this->variables->request);
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
		 * Retrieves the contents of the provided $_SERVER collection.
		 *
		 * @return ParameterHelper
		 */
		public function getServer() : ParameterHelper {
			return new ParameterHelper($this->variables->server);
		}

		/**
		 * Retrieves the contents of the provided $_SESSION collection.
		 *
		 * @return ParameterHelper
		 */
		public function getSession() : ParameterHelper {
			return new ParameterHelper($this->variables->session);
		}

		/**
		 * Retrieves a copy of the PageVariables provided to the instance.
		 *
		 * @return PageVariables
		 */
		public function getVariables() : PageVariables {
			return clone $this->variables;
		}

		/**
		 * Indicates whether the request is 'valid' based on the input data.
		 *
		 * @return bool
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
