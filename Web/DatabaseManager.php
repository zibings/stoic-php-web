<?php

	namespace Stoic\Web;

	use AndyM84\Config\ConfigContainer;

	use Stoic\Pdo\PdoHelper;

	/**
	 * Class that manages database connections for the application.
	 *
	 * @package Stoic\Web
	 */
	class DatabaseManager {
		/** @var PdoHelper[] */
		protected array $instances = [];


		/**
		 * Instantiates a new DatabaseManager object.
		 *
		 * @param ConfigContainer $config
		 */
		public function __construct() {
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
		public function getDatabase(string $key) : PdoHelper {
			if (!array_key_exists($key, $this->instances)) {
				return new PdoHelper('');
			}

			return $this->instances[$key];
		}

		/**
		 * Sets a PdoHelper instance for the specified key.
		 *
		 * @param string $key
		 * @param PdoHelper $db
		 * @return void
		 */
		public function setDatabase(string $key, PdoHelper $db) : void {
			$this->instances[$key] = $db;

			return;
		}
	}
