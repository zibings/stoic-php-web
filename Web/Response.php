<?php

	namespace Stoic\Web;

	use Stoic\Utilities\EnumBase;

	class HttpStatusCodes extends EnumBase {
		// 100's
		const CONTINU                          = 100;
		const PROTO_SWITCH                     = 101;
		const PROCESSING                       = 102;
		// 200's
		const OK                               = 200;
		const CREATED                          = 201;
		const ACCEPTED                         = 202;
		const NON_AUTH_INFO                    = 203;
		const NO_CONTENT                       = 204;
		const RESET_CONTENT                    = 205;
		const PARTIAL_CONTENT                  = 206;
		const MULTI_STATUS                     = 207;
		const ALREADY_REPORTED                 = 208;
		const IM_USED                          = 226;
		// 300's
		const MULTIPLE_CHOICES                 = 300;
		const MOVED_PERMANENTLY                = 301;
		const FOUND                            = 302;
		const SEE_OTHER                        = 303;
		const NOT_MODIFIED                     = 304;
		const USE_PROXY                        = 305;
		const SWITCH_PROXY                     = 306;
		const TEMPORARY_REDIRECT               = 307;
		const PERMANENT_REDIRECT               = 308;
		// 400's
		const BAD_REQUEST                      = 400;
		const UNAUTHORIZED                     = 401;
		const PAYMENT_REQUIRED                 = 402;
		const FORBIDDEN                        = 403;
		const NOT_FOUND                        = 404;
		const METHOD_NOT_ALLOWED               = 405;
		const NOT_ACCEPTABLE                   = 406;
		const PROXY_AUTH_REQUIRED              = 407;
		const REQUEST_TIMEOUT                  = 408;
		const CONFLICT                         = 409;
		const GONE                             = 410;
		const LENGTH_REQUIRED                  = 411;
		const PRECONDITION_FAILED              = 412;
		const PAYLOAD_TOO_LARGE                = 413;
		const URI_TOO_LONG                     = 414;
		const UNSUPPORTED_MEDIA_TYPE           = 415;
		const RANGE_NOT_SATISFIABLE            = 416;
		const EXPECTATION_FAILED               = 417;
		const IM_A_TEAPOT                      = 418;
		const MISDIRECTED_REQUEST              = 421;
		const UNPROCESSABLE_ENTITY             = 422;
		const LOCKED                           = 423;
		const FAILED_DEPENDENCY                = 424;
		const TOO_EARLY                        = 425;
		const UPGRADE_REQUIRED                 = 426;
		const PRECONDITION_REQUIRED            = 428;
		const TOO_MANY_REQUESTS                = 429;
		const REQUEST_HEADER_FIELDS_TOO_LARGE  = 431;
		const UNAVAILABLE_FOR_LEGAL_REASONS    = 451;
		// 500's
		const INTERNAL_SERVER_ERROR            = 500;
		const NOT_IMPLEMENTED                  = 501;
		const BAD_GATEWAY                      = 502;
		const SERVICE_UNAVAILABLE              = 503;
		const GATEWAY_TIMEOUT                  = 504;
		const HTTP_VERSION_NOT_SUPPORTED       = 505;
		const VARIANT_ALSO_NEGOTIATES          = 506;
		const INSUFFICIENT_STORAGE             = 507;
		const LOOP_DETECTED                    = 508;
		const NOT_EXTENDED                     = 510;
		const NETWORK_AUTHENTICATEION_REQUIRED = 511;


		protected static $friendlyLookup = [
			self::CONTINU                          => 'Continue',
			self::PROTO_SWITCH                     => 'Switching Protocols',
			self::PROCESSING                       => 'Processing',
			self::OK                               => 'OK',
			self::CREATED                          => 'Created',
			self::ACCEPTED                         => 'Accepted',
			self::NON_AUTH_INFO                    => 'Non-Authoritative Information',
			self::NO_CONTENT                       => 'No Content',
			self::RESET_CONTENT                    => 'Reset Content',
			self::PARTIAL_CONTENT                  => 'Partial Content',
			self::MULTI_STATUS                     => 'Multi-Status',
			self::ALREADY_REPORTED                 => 'Already Reported',
			self::IM_USED                          => 'IM Used',
			self::MULTIPLE_CHOICES                 => 'Multiple Choices',
			self::MOVED_PERMANENTLY                => 'Moved Permanently',
			self::FOUND                            => 'Found',
			self::SEE_OTHER                        => 'See Other',
			self::NOT_MODIFIED                     => 'Not Modified',
			self::USE_PROXY                        => 'Use Proxy',
			self::SWITCH_PROXY                     => 'Switch Proxy',
			self::TEMPORARY_REDIRECT               => 'Temporary Redirect',
			self::PERMANENT_REDIRECT               => 'Permanent Redirect',
			self::BAD_REQUEST                      => 'Bad Request',
			self::UNAUTHORIZED                     => 'Unauthorized',
			self::PAYMENT_REQUIRED                 => 'Payment Required',
			self::FORBIDDEN                        => 'Fobidden',
			self::NOT_FOUND                        => 'Not Found',
			self::METHOD_NOT_ALLOWED               => 'Method Not Allowed',
			self::NOT_ACCEPTABLE                   => 'Not Acceptable',
			self::PROXY_AUTH_REQUIRED              => 'Proxy Authentication Required',
			self::REQUEST_TIMEOUT                  => 'Request Timeout',
			self::CONFLICT                         => 'Conflict',
			self::GONE                             => 'Gone',
			self::LENGTH_REQUIRED                  => 'Length Required',
			self::PRECONDITION_FAILED              => 'Preconditions Failed',
			self::PAYLOAD_TOO_LARGE                => 'Payload Too Large',
			self::URI_TOO_LONG                     => 'URI Too Long',
			self::UNSUPPORTED_MEDIA_TYPE           => 'Unsupported Media Type',
			self::RANGE_NOT_SATISFIABLE            => 'Range Not Satisfiable',
			self::EXPECTATION_FAILED               => 'Expectation Failed',
			self::IM_A_TEAPOT                      => "I'm a teapot",
			self::MISDIRECTED_REQUEST              => 'Misdirected Request',
			self::UNPROCESSABLE_ENTITY             => 'Unprocessable Entity',
			self::LOCKED                           => 'Locked',
			self::FAILED_DEPENDENCY                => 'Failed Dependency',
			self::TOO_EARLY                        => 'Too Early',
			self::UPGRADE_REQUIRED                 => 'Upgrade Required',
			self::PRECONDITION_REQUIRED            => 'Precondition Required',
			self::TOO_MANY_REQUESTS                => 'Too Many Requests',
			self::REQUEST_HEADER_FIELDS_TOO_LARGE  => 'Request Header Fields Too Large',
			self::UNAVAILABLE_FOR_LEGAL_REASONS    => 'Unavailable For Legal Reasons',
			self::INTERNAL_SERVER_ERROR            => 'Internal Server Error',
			self::NOT_IMPLEMENTED                  => 'Not Implemented',
			self::BAD_GATEWAY                      => 'Bad Gateway',
			self::SERVICE_UNAVAILABLE              => 'Service Unavailable',
			self::GATEWAY_TIMEOUT                  => 'Gateway Timeout',
			self::HTTP_VERSION_NOT_SUPPORTED       => 'HTTP Version Not Supported',
			self::VARIANT_ALSO_NEGOTIATES          => 'Variant Also Negotiates',
			self::INSUFFICIENT_STORAGE             => 'Insufficient Storage',
			self::LOOP_DETECTED                    => 'Loop Detected',
			self::NOT_EXTENDED                     => 'Not Extended',
			self::NETWORK_AUTHENTICATEION_REQUIRED => 'Network Authentication Required'
		];



	}

	class Response {
		/**
		 * HTTP code for response.
		 *
		 * @var HttpStatusCodes
		 */
		protected $code = null;
		/**
		 * Data string for response.
		 *
		 * @var string
		 */
		protected $data = '';


		public static function fromError(string $message, $code = HttpStatusCodes::INTERNAL_SERVER_ERROR) : Response {
			return new Response($code, $message);
		}


		public function __construct($code = null, $data = null) {
			if ($code !== null) {
				/** @var HttpStatusCodes $code */
				$code = EnumBase::tryGetEnum($code, HttpStatusCodes::class);

				if ($code->getValue() === null) {
					throw new \InvalidArgumentException("Invalid status code supplied to Stoic\Web\Response class");
				}

				$this->code = $code;
				$this->setData($data);
			} else {
				$this->code = new HttpStatusCodes();
			}

			return;
		}

		public function getCode() : HttpStatusCodes {
			return $this->code;
		}

		public function getData() {
			return $this->data;
		}

		/**
		 * Gets the response status code.
		 * The status code is a 3-digit integer result code of the server's attempt
		 * to understand and satisfy the request.
		 *
		 * @return int Status code.
		 */
		public function getStatusCode() {
			// TODO: implement the function Psr\Http\Message\ResponseInterface::getStatusCode
		}

		/**
		 * Gets the response reason phrase associated with the status code.
		 * Because a reason phrase is not a required element in a response
		 * status line, the reason phrase value MAY be null. Implementations MAY
		 * choose to return the default RFC 7231 recommended reason phrase (or those
		 * listed in the IANA HTTP Status Code Registry) for the response's
		 * status code.
		 *
		 * @return string Reason phrase; must return an empty string if none present.
		 */
		public function getReasonPhrase() {
			// TODO: implement the function Psr\Http\Message\ResponseInterface::getReasonPhrase
		}

		/**
		 * Retrieves the HTTP protocol version as a string.
		 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
		 *
		 * @return string HTTP protocol version.
		 */
		public function getProtocolVersion() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getProtocolVersion
		}

		/**
		 * Retrieves all message header values.
		 * The keys represent the header name as it will be sent over the wire, and
		 * each value is an array of strings associated with the header.
		 *
		 * // Represent the headers as a string
		 * foreach ($message->getHeaders() as $name => $values) {
		 * echo $name . ": " . implode(", ", $values);
		 * }
		 *
		 * // Emit headers iteratively:
		 * foreach ($message->getHeaders() as $name => $values) {
		 * foreach ($values as $value) {
		 * header(sprintf('%s: %s', $name, $value), false);
		 * }
		 * }
		 *
		 * While header names are not case-sensitive, getHeaders() will preserve the
		 * exact case in which headers were originally specified.
		 *
		 * @return string[][] Returns an associative array of the message's headers. Each
		key MUST be a header name, and each value MUST be an array of strings
		for that header.
		 */
		public function getHeaders() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeaders
		}

		/**
		 * Checks if a header exists by the given case-insensitive name.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return bool Returns true if any header names match the given header
		name using a case-insensitive string comparison. Returns false if
		no matching header name is found in the message.
		 */
		public function hasHeader($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::hasHeader
		}

		/**
		 * Retrieves a message header value by the given case-insensitive name.
		 * This method returns an array of all the header values of the given
		 * case-insensitive header name.
		 *
		 * If the header does not appear in the message, this method MUST return an
		 * empty array.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return string[] An array of string values as provided for the given
		header. If the header does not appear in the message, this method MUST
		return an empty array.
		 */
		public function getHeader($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeader
		}

		/**
		 * Retrieves a comma-separated string of the values for a single header.
		 * This method returns all of the header values of the given
		 * case-insensitive header name as a string concatenated together using
		 * a comma.
		 *
		 * NOTE: Not all header values may be appropriately represented using
		 * comma concatenation. For such headers, use getHeader() instead
		 * and supply your own delimiter when concatenating.
		 *
		 * If the header does not appear in the message, this method MUST return
		 * an empty string.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return string A string of values as provided for the given header
		concatenated together using a comma. If the header does not appear in
		the message, this method MUST return an empty string.
		 */
		public function getHeaderLine($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeaderLine
		}

		/**
		 * Gets the body of the message.
		 *
		 * @return StreamInterface Returns the body as a stream.
		 */
		public function getBody() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getBody
		}
	}
