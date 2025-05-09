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
	 * @version 1.3.12
	 */
	class Request {
		protected null|string $contentType = null;
		protected bool $hasFileUploads = false;
		protected mixed $input = null;
		protected bool $isValid = false;
		protected bool $isJson = false;
		protected null|RequestType $requestType = null;
		protected null|PageVariables $variables = null;


		/**
		 * Instantiates a new Request object, either taking information provided or pulling automatically from the
		 * $_COOKIE, $_GET, and $_SERVER superglobals as well as the `php://input` stream.
		 *
		 * @param null|PageVariables $variables Optional variable values to supply instead of using superglobals.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @throws InvalidRequestException|\ReflectionException
		 */
		public function __construct(null|PageVariables $variables = null, mixed $input = null) {
			$this->requestType = new RequestType(RequestType::ERROR);
			$this->variables   = $variables ?? PageVariables::fromGlobals();
			$server            = $this->getServer();

			if (!$server->has(ServerIndices::REQUEST_METHOD)) {
				throw new InvalidRequestException("Server collection was missing '" . ServerIndices::REQUEST_METHOD . "' value");
			}

			$reqMeth           = strtoupper($server->getString(ServerIndices::REQUEST_METHOD));
			$this->requestType = RequestType::fromString($reqMeth);

			if ($this->requestType->getValue() === null) {
				throw new InvalidRequestException("Invalid request method provided: {$reqMeth}");
			}

			if ($server->has(ServerIndices::CONTENT_TYPE)) {
				$this->contentType = $server->getString(ServerIndices::CONTENT_TYPE, '');
			}

			if ($this->getFiles()->count() > 0) {
				$this->hasFileUploads = true;
			}

			if (!$this->requestType->is(RequestType::GET)) {
				if ($this->hasFileUploads) {
					// @codeCoverageIgnoreStart
					$this->input = $this->variables->post;
					// @codeCoverageIgnoreEnd
				} else if ($input !== null) {
					$this->input = $input;
				} else {
					$this->readInput();

					if (empty($this->input) && !empty($this->variables->post)) {
						// @codeCoverageIgnoreStart
						$this->input = $this->variables->post;
						// @codeCoverageIgnoreEnd
					} else if (empty($this->input)) {
						return;
					}
				}

				// @codeCoverageIgnoreStart
				if (!$this->hasFileUploads && is_string($this->input)) {
					if (json_validate(trim($this->input))) {
						$this->isJson = true;
					}
				}
				// @codeCoverageIgnoreEnd
			}

			$this->isValid = true;

			return;
		}

		/**
		 * Retrieves the detected CONTENT_TYPE of the request, if available.  Returns an empty string if not found.
		 *
		 * @return string
		 */
		public function getContentType() : string {
			return $this->contentType ?? '';
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
			if ($this->requestType->is(RequestType::GET)) {
				return $this->getGet();
			}

			if ($this->hasFileUploads || (is_array($this->input) && !$this->isJson)) {
				// @codeCoverageIgnoreStart
				return new ParameterHelper(is_array($this->input) ? $this->input : []);
				// @codeCoverageIgnoreEnd
			}

			if ($this->isJson && is_string($this->input)) {
				// @codeCoverageIgnoreStart
				$input = json_decode($this->input, true);

				return new ParameterHelper(is_array($input) ? $input : [$input]);
				// @codeCoverageIgnoreEnd
			}

			// @codeCoverageIgnoreStart
			return new ParameterHelper([]);
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
		 * Indicates whether the request includes file uploads.
		 *
		 * @return bool
		 */
		public function hasFileUploads() : bool {
			return $this->hasFileUploads;
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
					if (($this->input = file_get_contents("php://input")) === false) {
						$this->input = '';
					}
				} catch (\Exception $ex) {
					$this->input = '';
				}
			}

			return;
		}
	}
