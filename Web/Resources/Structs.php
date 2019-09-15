<?php

	namespace Stoic\Web\Resources;

	/**
	 * Struct for holding the 'predefined' global variables for a page request.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class PageVariables {
		/**
		 * HTTP cookies.
		 *
		 * @var array
		 */
		public $cookie;
		/**
		 * Environment variables.
		 *
		 * @var array
		 */
		public $env;
		/**
		 * HTTP file upload variables.
		 *
		 * @var array
		 */
		public $files;
		/**
		 * HTTP GET variables.
		 *
		 * @var array
		 */
		public $get;
		/**
		 * HTTP POST variables.
		 *
		 * @var array
		 */
		public $post;
		/**
		 * HTTP request variables.
		 *
		 * @var array
		 */
		public $request;
		/**
		 * Server and execution environment information.
		 *
		 * @var array
		 */
		public $server;
		/**
		 * Session variables.
		 *
		 * @var array
		 */
		public $session;


		/**
		 * Static method to return the 'predefined' global variables assigned to
		 * a struct instance.
		 *
		 * @return PageVariables
		 */
		public static function fromGlobals() {
			return new PageVariables(
				$_COOKIE,
				$_ENV,
				$_FILES,
				$_GET,
				$_POST,
				$_REQUEST,
				$_SERVER,
				$_SESSION
			);
		}


		/**
		 * Instantiates a new PageVariables instance using the provided arrays for
		 * the 'predefined' variables.
		 *
		 * @param array $cookie HTTP cookies.
		 * @param array $env Environment variables.
		 * @param array $files HTTP file upload variables.
		 * @param array $get HTTP GET variables.
		 * @param array $post HTTP POST variables.
		 * @param array $request HTTP request variables.
		 * @param array $server Server and execution environment information.
		 * @param array $session Session variables.
		 */
		public function __construct(array $cookie, array $env, array $files, array $get, array $post, array $request, array $server, array $session) {
			$this->cookie = $cookie;
			$this->env = $env;
			$this->files = $files;
			$this->get = $get;
			$this->post = $post;
			$this->request = $request;
			$this->server = $server;
			$this->session = $session;

			return;
		}
	}
