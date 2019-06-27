<?php

	namespace Stoic\Web;

	use Stoic\Utilities\EnumBase;

	class HttpStatusCodes extends EnumBase {
		// 100's
		const CONTINU = 100;
		const PROTO_SWITCH = 101;
		const PROCESSING = 102;
		// 200's
		const OK = 200;
		const CREATED = 201;
		const ACCEPTED = 202;
		const NON_AUTH_INFO = 203;
		const NO_CONTENT = 204;
		const RESET_CONTENT = 205;
		const PARTIAL_CONTENT = 206;
		const MULTI_STATUS = 207;
		const ALREADY_REPORTED = 208;
		const IM_USED = 226;
		// 300's
		const MULTIPLE_CHOICES = 300;
		const MOVED_PERMANENTLY = 301;
		const FOUND = 302;
		const SEE_OTHER = 303;
		const NOT_MODIFIED = 304;
		const USE_PROXY = 305;
		const SWITCH_PROXY = 306;
		const TEMPORARY_REDIRECT = 307;
		const PERMANENT_REDIRECT = 308;
		// 400's
		const BAD_REQUEST = 400;
		const UNAUTHORIZED = 401;
		const PAYMENT_REQUIRED = 402;
		const FORBIDDEN = 403;
		const NOT_FOUND = 404;
		const METHOD_NOT_ALLOWED = 405;
		const NOT_ACCEPTABLE = 406;
		const PROXY_AUTH_REQUIRED = 407;
		const REQUEST_TIMEOUT = 408;
		const CONFLICT = 409;
		const GONE = 410;
		const LENGTH_REQUIRED = 411;
		const PRECONDITION_FAILED = 412;
		const PAYLOAD_TOO_LARGE = 413;
		const URI_TOO_LONG = 414;
		const UNSUPPORTED_MEDIA_TYPE = 415;
		const RANGE_NOT_SATISFIABLE = 416;
		const EXPECTATION_FAILED = 417;
		const IM_A_TEAPOT = 418;
		const MISDIRECTED_REQUEST = 421;
		const UNPROCESSABLE_ENTITY = 422;
		const LOCKED = 423;
		const FAILED_DEPENDENCY = 424;
		const UPGRADE_REQUIRED = 426;
		const PRECONDITION_REQUIRED = 428;
		const TOO_MANY_REQUESTS = 429;
		const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
		const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
		// 500's
		const INTERNAL_SERVER_ERROR = 500;
		const NOT_IMPLEMENTED = 501;
		const BAD_GATEWAY = 502;
		const SERVICE_UNAVAILABLE = 503;
		const GATEWAY_TIMEOUT = 504;
		const HTTP_VERSION_NOT_SUPPORTED = 505;
		const VARIANT_ALSO_NEGOTIATES = 506;
		const INSUFFICIENT_STORAGE = 507;
		const LOOP_DETECTED = 508;
		const NOT_EXTENDED = 510;
		const NETWORK_AUTHENTICATEION_REQUIRED = 511;
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

		public function setAsError(string $message, $code = HttpStatusCodes::INTERNAL_SERVER_ERROR) {
			/** @var HttpStatusCodes $code */
			$code = EnumBase::tryGetEnum($code, HttpStatusCodes::class);

			if ($code->getValue() === null) {
				throw new \InvalidArgumentException("Invalid status code supplied for Stoic\Web\Response class error");
			}

			$this->code = $code;
			$this->setData($message);

			return;
		}

		public function setCode($code) {
			/** @var HttpStatusCodes $code */
			$code = EnumBase::tryGetEnum($code, HttpStatusCodes::class);

			if ($code->getValue() === null) {
				throw new \InvalidArgumentException("Invalid status code supplied for Stoic\Web\Response class");
			}

			$this->code = $code;

			return;
		}

		public function setData($data = null) {
			$this->data = $data ?? '';

			return;
		}
	}
