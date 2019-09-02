<?php

	namespace Stoic\Web;

	/**
	 * Exception that signifies exceptions were already sent by the currently
	 * executing script.
	 *
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class HeadersAlreadySentException extends \Exception {
		/**
		 * Collection of headers that were already sent (or queued).
		 *
		 * @var string[]
		 */
		public $headers = [];


		/**
		 * Instantiates a new HeadersAlreadySent exception with the list of headers
		 * sent pre-populated.
		 *
		 * @param string $message The Exception message to throw.
		 * @param int $code The Exception code.
		 * @param \Throwable $previous The previous Exception used for chaining.
		 * @return HeadersAlreadySentException
		 */
		public static function newWithHeaders(string $message, int $code = 0, \Throwable $previous = null) : HeadersAlreadySentException {
			$tmp = new HeadersAlreadySentException($message);
			$tmp->headers = headers_list();

			return $tmp;
		}
	}
