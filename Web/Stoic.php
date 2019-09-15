<?php

	namespace Stoic\Web;

	use Stoic\Log\Logger;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;

	/**
	 * Singleton class in the Stoic framework.  Helps orchestrate common page-
	 * level operations.
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
		 * Local instance of 'predefined' global variables.
		 *
		 * @var PageVariables
		 */
		protected $variables = null;


		/**
		 * Static singleton instance.
		 *
		 * @var Stoic[]
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
			if (count(static::$instances) || ($log !== null && $variables !== null)) {
				static::$instances[] = new Stoic($variables ?? PageVariables::fromGlobals(), $log ?? new Logger());
			}

			return static::$instances[count(static::$instances) - 1];
		}

		/**
		 * Returns a clone of the entire instance stack.
		 *
		 * @return Stoic[]
		 */
		public static function getInstanceStack() {
			if (count(static::$instances) < 1) {
				return [];
			}

			$ret = [];

			foreach (array_values(static::$instances) as $inst) {
				$ret[] = clone $inst;
			}

			return $ret;
		}


		/**
		 * Instantiates a new Stoic object.
		 *
		 * @param PageVariables $variables Collection of 'predefined' variables.
		 * @param Logger $log Logger instance for use by instance.
		 */
		protected function __construct(PageVariables $variables, Logger $log) {
			$this->log = $log;
			$this->variables = $variables;

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
		 * Retrieves the contents of the provided $_POST collection.
		 *
		 * @return ParameterHelper
		 */
		public function getPost() : ParameterHelper {
			return new ParameterHelper($this->variables->post);
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
		 * Retrieves the contents of the provided $_SERVER collection.
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
