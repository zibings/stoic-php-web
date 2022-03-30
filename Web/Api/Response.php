<?php

	namespace Stoic\Web\Api;

	use Stoic\Web\Resources\HttpStatusCodes;

	/**
	 * Class to supply a semi-structured response to API requests.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class Response {
		/**
		 * Data returned by response.
		 *
		 * @var mixed
		 */
		protected mixed $data = null;
		/**
		 * HTTP status code for response.
		 *
		 * @var null|HttpStatusCodes
		 */
		protected ?HttpStatusCodes $status = null;


		/**
		 * Instantiates a new API response object.
		 *
		 * @param null|int|HttpStatusCodes $status Optional HTTP status code value for response.
		 * @param mixed $data Optional data to store in response.
		 * @throws \ReflectionException
		 */
		public function __construct(null|int|HttpStatusCodes $status = null, mixed $data = null) {
			if ($status !== null) {
				$this->status = HttpStatusCodes::tryGet($status);
			}

			if ($data !== null) {
				$this->data = $data;
			}

			return;
		}

		/**
		 * Retrieves the raw data for the response.
		 *
		 * @return mixed
		 */
		public function getData() : mixed {
			return $this->data;
		}

		/**
		 * Retrieves the HTTP status code for the response.
		 *
		 * @return HttpStatusCodes
		 */
		public function getStatus() : HttpStatusCodes {
			return $this->status ?? new HttpStatusCodes();
		}

		/**
		 * Shortcut for setting Response to be an error. Same as calling Response::setStatus() and Response::setData().
		 *
		 * @param string $message Error message to use as response data.
		 * @param int|HttpStatusCodes $status Optional HTTP status code for response, defaults to `HttpStatusCodes::INTERNAL_SERVER_ERROR`.
		 * @throws \InvalidArgumentException|\ReflectionException
		 * @return void
		 */
		public function setAsError(string $message, int|HttpStatusCodes $status = HttpStatusCodes::INTERNAL_SERVER_ERROR) : void {
			$status = HttpStatusCodes::tryGet($status);

			if ($status->getValue() === null) {
				throw new \InvalidArgumentException("Invalid status code supplied for Response");
			}

			$this->data   = $message;
			$this->status = $status;

			return;
		}

		/**
		 * Sets the response data.
		 *
		 * @param mixed $data Data for response to return.
		 * @return void
		 */
		public function setData(mixed $data) : void {
			$this->data = $data;

			return;
		}

		/**
		 * Sets the response HTTP status code.
		 *
		 * @param int|HttpStatusCodes $status HTTP status code for response to return.
		 * @throws \ReflectionException
		 * @return void
		 */
		public function setStatus(int|HttpStatusCodes $status) : void {
			$this->status = HttpStatusCodes::tryGet($status);

			return;
		}
	}
