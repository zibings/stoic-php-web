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
		protected static array $instances             = [];
		protected static ConfigContainer|null $config = null;


		/**
		 * Attempts to retrieve a PdoHelper instance for the specified key.
		 *
		 * @param string $key
		 * @param ConfigContainer|null $config
		 * @throws \Exception
		 * @return PdoHelper
		 */
		public static function getDatabase(string $key, ConfigContainer|null $config = null) : PdoHelper {
			if ($config === null && self::$config === null) {
				throw new \Exception("No configuration provided for database connection");
			}

			if ($config !== null) {
				self::$config = $config;
			}

			if (!array_key_exists($key, self::$instances)) {
				self::$instances[$key] = new PdoHelper(
					self::$config->get(SettingsStrings::DB_DSN_DEFAULT, ''),
					self::$config->get(SettingsStrings::DB_USER_DEFAULT, ''),
					self::$config->get(SettingsStrings::DB_PASS_DEFAULT, '')
				);
			}

			return self::$instances[$key];
		}
	}
