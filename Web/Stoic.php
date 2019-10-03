<?php

	namespace Stoic\Web;

	use Stoic\Log\Logger;
	use Stoic\Web\Resources\PageVariables;

	/**
	 * Singleton-ish class in the Stoic framework.  Helps orchestrate common page
	 * -level operations.
	 *
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class Stoic {
		/**
		 * Local Logger instance.
		 *
		 * @var Logger
		 */
		protected $log = null;
		/**
		 * Local instance of current request information.
		 *
		 * @var Request
		 */
		protected $request = null;


		/**
		 * Static singleton instance.
		 *
		 * @var array
		 */
		protected static $instances = [];


		/**
		 * Static method to retrieve the most recent singleton instance for the
		 * system.  If instance exists but the Logger and PageVariables arguments
		 * are provided, a new instance is created and returned from the stack. If
		 * the instance doesn't exist, one is created.
		 *
		 * @param PageVariables $variables Collection of 'predefined' variables, if not supplied an instance is created from globals.
		 * @param Logger $log Logger instance for use by instance, if not supplied a new instance is used.
		 * @return Stoic
		 */
		public static function getInstance(PageVariables $variables = null, Logger $log = null) {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false) {
				static::$instances[$class] = [];
			}

			if (count(static::$instances[$class]) < 1 || ($log !== null && $variables !== null)) {
				static::$instances[$class][] = new $class($variables ?? PageVariables::fromGlobals(), $log ?? new Logger());
			}

			return static::$instances[$class][count(static::$instances[$class]) - 1];
		}

		/**
		 * Returns a clone of the entire instance stack.
		 *
		 * @return Stoic[]
		 */
		public static function getInstanceStack() {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false || count(static::$instances[$class]) < 1) {
				return [];
			}

			$ret = [];

			foreach (array_values(static::$instances[$class]) as $inst) {
				$ret[] = $inst;
			}

			return $ret;
		}


		/**
		 * Instantiates a new Stoic object.
		 *
		 * @param PageVariables $variables Collection of 'predefined' variables.
		 * @param Logger $log Logger instance for use by instance.
		 */
		protected function __construct(PageVariables $variables, Logger $log, $input = null) {
			$this->log = $log;
			$this->request = new Request($variables ?? PageVariables::fromGlobals(), $input);

			return;
		}

		/**
		 * Returns the Request instance for this Stoic instance.
		 *
		 * @return Request
		 */
		public function getRequest() : Request {
			return $this->request;
		}

		/**
		 * Returns the Logger instance in use by this Stoic instance.
		 *
		 * @return Logger
		 */
		public function log() : Logger {
			return $this->log;
		}

		/**
		 * Attempts to set a header for the current request.  If any output has
		 * occurred prior to this attempt, the method will log the attempt and
		 * silently fail.
		 *
		 * @codeCoverageIgnore
		 * @param string $name String value of header name.
		 * @param string $value String value of header value.
		 * @param boolean $replace Optional toggle to replace vs duplicate a header, default behavior is to replace.
		 * @param null|integer $code Optional HTTP response code to set as response value.
		 * @return void
		 */
		public function setHeader(string $name, string $value, bool $replace = true, ?int $code = null) : void {
			if (headers_sent()) {
				$this->log->warning("Attempted to send the `{$name}` header with value `{$value}` after headers were already sent");

				return;
			}

			$this->log->info("Attempting to set the `{$name}` header with value `{$value}`");

			// @codeCoverageIgnoreStart
			if ($code !== null) {
				header("{$name}: {$value}", $replace, $code);
			} else {
				header("{$name}: {$value}", $replace);
			}
			// @codeCoverageIgnoreEnd

			return;
		}
	}
