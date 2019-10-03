<?php

	namespace Stoic\Web;

	use Stoic\Log\Logger;
	use Stoic\Utilities\FileHelper;
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
		 * Relative filesystem path for application's 'core' folder.
		 *
		 * @var string
		 */
		protected $corePath = null;
		/**
		 * Local FileHelper instance.
		 *
		 * @var FileHelper
		 */
		protected $fh = null;
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
		 * @param null|string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param PageVariables $variables Collection of 'predefined' variables, if not supplied an instance is created from globals.
		 * @param Logger $log Logger instance for use by instance, if not supplied a new instance is used.
		 * @return Stoic
		 */
		public static function getInstance(?string $corePath = null, PageVariables $variables = null, Logger $log = null) {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false) {
				static::$instances[$class] = [];
			}

			if (count(static::$instances[$class]) < 1 || ($corePath !== null && !empty($corePath) && $variables !== null && $log !== null)) {
				$tmp = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger());

				// TODO: Add config loading & core loading

				static::$instances[$class][] = $tmp;
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
		 * @param string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param PageVariables $variables Collection of 'predefined' variables.
		 * @param Logger $log Logger instance for use by instance.
		 */
		protected function __construct(string $corePath, PageVariables $variables, Logger $log, $input = null) {
			$this->log = $log;
			$this->corePath = $corePath;
			$this->request = new Request($variables ?? PageVariables::fromGlobals(), $input);

			$this->setFileHelper(new FileHelper($this->corePath));

			return;
		}

		/**
		 * Returns the currently configured relative filesystem path for the 'core'
		 * folder.
		 *
		 * @return string
		 */
		public function getCorePath() : string {
			return $this->corePath;
		}

		/**
		 * Returns the local FileHelper instance.
		 *
		 * @return FileHelper
		 */
		public function getFileHelper() : FileHelper {
			return $this->fh;
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
		 * Loads any files in the provided path if they have the given extension.
		 * Returns an array of any files that were loaded.
		 *
		 * @param string $path Path for folder to look for files within.
		 * @param string $extension Extension to use when searching possible files.
		 * @param boolean $caseInsensitive Whether or not to perform a case-insensitive extension comparison, defaults to `false`.
		 * @param boolean $allowReloads Whether or not to allow loaded files to be reloaded, defaults to `false`.
		 * @return string[]
		 */
		public function loadFilesByExtension(string $path, string $extension, bool $caseInsensitive = false, bool $allowReloads = false) {
			$ret = [];
			$extLen = -1 * strlen($extension);
			$files = $this->fh->getFolderFiles($path);

			if ($files !== null && count($files) > 0) {
				foreach (array_values($files) as $file) {
					$ext = substr($file, $extLen);

					if ($caseInsensitive) {
						$ext = strtolower($ext);
					}

					if ($ext == $extension) {
						$ret[] = $file;
						$this->fh->load($file, $allowReloads);
					}
				}
			}

			return $ret;
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
		 * Used to set the local FileHelper instance.
		 *
		 * @param FileHelper $fh FileHelper object to use internally.
		 * @return void
		 */
		public function setFileHelper(FileHelper $fh) : void {
			$this->fh = $fh;

			return;
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
