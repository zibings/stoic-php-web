<?php

	namespace Stoic\Web;

	use AndyM84\Config\ConfigContainer;

	use Stoic\Pdo\PdoHelper;
	use Stoic\Web\Resources\SettingsStrings;

	/**
	 * Class that manages database connections for the application.
	 *
	 * @package Stoic\Web
	 */
	class DatabaseManager {
		/** @var PdoHelper[] */
		protected array $instances = [];
		protected null|ConfigContainer $config = null;


		/**
		 * Instantiates a new DatabaseManager object.
		 *
		 * @param ConfigContainer $config
		 */
		public function __construct(null|ConfigContainer $config = null) {
			$this->config = $config;

			return;
		}

		/**
		 * Attempts to retrieve a PdoHelper instance for the specified key.
		 *
		 * @param string $key
		 * @param ConfigContainer|null $config
		 * @throws \Exception
		 * @return PdoHelper
		 */
		public function getDatabase(string $key, null|ConfigContainer $config = null) : PdoHelper {
			$conf = $this->config;

			if ($config !== null) {
				$conf = $config;

				if ($this->config === null) {
					$this->config = $config;
				}
			}

			if ($conf === null) {
				throw new \Exception("No configuration provided for database connection");
			}

			if (!array_key_exists($key, $this->instances)) {
				$this->instances[$key] = new PdoHelper(
					$conf->get(SettingsStrings::DB_DSN_DEFAULT, ''),
					$conf->get(SettingsStrings::DB_USER_DEFAULT, ''),
					$conf->get(SettingsStrings::DB_PASS_DEFAULT, '')
				);
			}

			return $this->instances[$key];
		}
	}
