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
		protected static array $instances = [];
		protected static null|ConfigContainer $config = null;


		/**
		 * Attempts to retrieve a PdoHelper instance for the specified key.
		 *
		 * @param string $key
		 * @param ConfigContainer|null $config
		 * @throws \Exception
		 * @return PdoHelper
		 */
		public static function getDatabase(string $key, null|ConfigContainer $config = null) : PdoHelper {
			$conf = self::$config;

			if ($config !== null) {
				$conf = $config;

				if (self::$config === null) {
					self::$config = $config;
				}
			}

			if ($conf === null) {
				throw new \Exception("No configuration provided for database connection");
			}

			if (!array_key_exists($key, self::$instances)) {
				self::$instances[$key] = new PdoHelper(
					$conf->get(SettingsStrings::DB_DSN_DEFAULT, ''),
					$conf->get(SettingsStrings::DB_USER_DEFAULT, ''),
					$conf->get(SettingsStrings::DB_PASS_DEFAULT, '')
				);
			}

			return self::$instances[$key];
		}
	}
