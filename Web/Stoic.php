<?php

	namespace Stoic\Web;

	use AndyM84\Config\ConfigContainer;
	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\FileHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\SettingsStrings;
	use Stoic\Web\Resources\StoicStrings;

	/**
	 * Singleton-ish class in the Stoic framework.  Helps orchestrate common page-level operations.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class Stoic {
		protected null|ConfigContainer $config = null;
		protected null|string $corePath = null;
		protected null|DatabaseManager $dbm = null;
		protected null|FileHelper $fh = null;
		protected null|Logger $log = null;
		protected null|Request $request = null;
		protected null|ParameterHelper $session = null;


		/**
		 * Static singleton instance.
		 *
		 * @var array
		 */
		protected static array $instances = [];


		/**
		 * Static method to retrieve the most recent singleton instance for the system. If instance exists but the Logger
		 * and PageVariables arguments are provided, a new instance is created and returned from the stack. If the instance
		 * doesn't exist, one is created.
		 *
		 * @param null|string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param null|PageVariables $variables Collection of 'predefined' variables, if not supplied an instance is created from globals.
		 * @param null|Logger $log Logger instance for use by instance, if not supplied a new instance is used.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @return static
		 */
		public static function getInstance(?string $corePath = null, ?PageVariables $variables = null, ?Logger $log = null, mixed $input = null) : static {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false) {
				static::$instances[$class] = [];
			}

			if (count(static::$instances[$class]) < 1 || (!empty($corePath) && $variables !== null && $log !== null)) {
				if (count(static::$instances[$class]) < 1) {
					static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input);
				} else {
					$existingCore = false;
					$insts = $class::getInstanceStack();

					foreach ($insts as $i) {
						if ($i->getCorepath() == $corePath) {
							$existingCore = true;

							break;
						}
					}

					if ($existingCore) {
						static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input, false);
					} else {
						static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input);
					}
				}
			}

			return static::$instances[$class][count(static::$instances[$class]) - 1];
		}

		/**
		 * Returns a clone of the entire instance stack.
		 *
		 * @return Stoic[]
		 */
		public static function getInstanceStack() : array {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false || count(static::$instances[$class]) < 1) {
				return [];
			}

			$ret = [];

			foreach (static::$instances[$class] as $inst) {
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
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @param bool $loadFiles Whether to attempt loading auxiliary files while instantiating, defaults to true.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\ReflectionException
		 */
		protected function __construct(string $corePath, PageVariables $variables, Logger $log, mixed $input = null, bool $loadFiles = true) {
			$this->log      = $log;
			$this->corePath = $corePath;
			$this->config   = new ConfigContainer();
			$this->request  = new Request($variables, $input);

			$this->setFileHelper(new FileHelper($this->corePath));

			if ($this->fh->fileExists(StoicStrings::SETTINGS_FILE_PATH)) {
				// @codeCoverageIgnoreStart
				$this->config = new ConfigContainer($this->fh->getContents(StoicStrings::SETTINGS_FILE_PATH));
				// @codeCoverageIgnoreEnd
			}

			$this->dbm = new DatabaseManager($this->config);

			$incPath = $this->config->get(SettingsStrings::INCLUDE_PATH, '~/inc');
			
			if ($loadFiles) {
				$clsExt  = $this->config->get(SettingsStrings::CLASSES_EXTENSION, '.cls.php');
				$clsPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::CLASSES_PATH,   'classes'));

				$this->loadFilesByExtension($clsPath, $clsExt, true);
				
				$rpoExt  = $this->config->get(SettingsStrings::REPOS_EXTENSION, '.rpo.php');
				$rpoPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::REPOS_PATH, 'repositories'));

				$this->loadFilesByExtension($rpoPath, $rpoExt, true);
			}

			register_shutdown_function(function (Logger $log) {
				// @codeCoverageIgnoreStart
				$log->output();
				// @codeCoverageIgnoreEnd
			}, $this->log);

			if (!defined('STOIC_DISABLE_DATABASE') || !STOIC_DISABLE_DATABASE && $this->config !== null) {
				$settings = $this->config->getSettings();

				foreach ($settings as $settingName => $settingValue) {
					if (str_starts_with($settingName, 'dbDsns.') === false) {
						continue;
					}

					$keyName = substr($settingName, 7);
					$user    = $this->config->get("dbUsers.{$keyName}", '');
					$pass    = $this->config->get("dbPasses.{$keyName}", '');

					$db = new PdoHelper($settingValue, $user, $pass);

					if (!defined('STOIC_DISABLE_DB_EXCEPTIONS') || !STOIC_DISABLE_DB_EXCEPTIONS) {
						$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					}

					if ($db->isActive()) {
						$this->dbm->setDatabase($keyName, $db);
					}
				}
			}

			if ((!defined('STOIC_DISABLE_SESSION') || !STOIC_DISABLE_SESSION) && !headers_sent()) {
				// @codeCoverageIgnoreStart
				if (session_status() != PHP_SESSION_ACTIVE && session_status() != PHP_SESSION_DISABLED) {
					session_start();
				}
				// @codeCoverageIgnoreEnd
			}

			if ($loadFiles) {
				$utlExt = $this->config->get(SettingsStrings::UTILITIES_EXT, '.utl.php');
				$utlPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::UTILITIES_PATH, 'utilities'));
				$this->loadFilesByExtension($utlPath, $utlExt, true);
			}

			return;
		}

		/**
		 * Returns the current ConfigContainer the instance is using as its settings store.
		 *
		 * @return ConfigContainer
		 */
		public function getConfig() : ConfigContainer {
			return $this->config;
		}

		/**
		 * Returns the currently configured relative filesystem path for the 'core' folder.
		 *
		 * @return string
		 */
		public function getCorePath() : string {
			return $this->corePath;
		}

		/**
		 * Attempts to return a local PdoHelper instance.
		 *
		 * @param null|string $key Optional key to retrieve a specific database instance.
		 * @throws \Exception
		 * @return PdoHelper
		 */
		public function getDb(null|string $key = null) : PdoHelper {
			if ($key === null) {
				return $this->dbm->getDatabase('default');
			}

			return $this->dbm->getDatabase($key);
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
		 * Returns the local Logger instance.
		 *
		 * @return Logger
		 */
		public function getLog() : Logger {
			return $this->log;
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
		 * Returns the ParameterHelper instance of the $_SESSION data.
		 *
		 * @return ParameterHelper
		 */
		public function getSession() : ParameterHelper {
			return $this->session;
		}

		/**
		 * Loads any files in the provided path if they have the given extension. Returns an array of any files that were
		 * loaded.
		 *
		 * @codeCoverageIgnore
		 * @param string $path Path for folder to look for files within.
		 * @param string $extension Extension to use when searching possible files.
		 * @param boolean $caseInsensitive Whether to perform a case-insensitive extension comparison, defaults to `false`.
		 * @param boolean $allowReloads Whether to allow loaded files to be reloaded, defaults to `false`.
		 * @return string[]
		 */
		public function loadFilesByExtension(string $path, string $extension, bool $caseInsensitive = false, bool $allowReloads = false) : array {
			$ret    = [];
			$extLen = -1 * strlen($extension);
			$files  = $this->fh->getFolderFiles($path);

			if ($files !== null && count($files) > 0) {
				foreach ($files as $file) {
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
		 * Attempts to set a header for the current request.  If any output has occurred prior to this attempt, the method
		 * will log the attempt and silently fail.
		 *
		 * @codeCoverageIgnore
		 * @param string $name String value of header name.
		 * @param string $value String value of header value.
		 * @param bool $replace Optional toggle to replace vs duplicate a header, default behavior is to replace.
		 * @param null|integer $code Optional HTTP response code to set as response value.
		 * @return void
		 */
		public function setHeader(string $name, string $value, bool $replace = true, ?int $code = null) : void {
			if (headers_sent()) {
				$this->log->warning("Attempted to send the `{$name}` header with value `{$value}` after headers were already sent");

				return;
			}

			$this->log->info("Attempting to set the `{$name}` header with value `{$value}`");

			if ($code !== null) {
				header("{$name}: {$value}", $replace, $code);
			} else {
				header("{$name}: {$value}", $replace);
			}

			return;
		}

		/**
		 * Attempts to set a raw header string for the current request.  If any output has occurred prior to this attempt,
		 * the method will log the attempt and silently fail.
		 *
		 * @codeCoverageIgnore
		 * @param string $value String value of header to set.
		 * @return void
		 */
		public function setRawHeader(string $value) : void {
			if (headers_sent()) {
				$this->log->warning("Attempted to send the raw header `{$value}` after headers were already sent");

				return;
			}

			$this->log->info("Attempting to set the raw header `{$value}`");

			// @codeCoverageIgnoreStart
			header($value);
			// @codeCoverageIgnoreEnd

			return;
		}

		/**
		 * Used to set the local ParameterHelper instance with $_SESSION data.
		 *
		 * @param ParameterHelper $session ParameterHelper object to use internally.
		 * @return void
		 */
		public function setSession(ParameterHelper $session) : void {
			$this->session = $session;

			return;
		}
	}
