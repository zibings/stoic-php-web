<?php

	namespace Stoic\Web\Resources;

	/**
	 * Struct for holding the information comprising an endpoint used by the API subsystem.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class ApiEndpoint {
		/**
		 * Value that determines required authentication 'roles'.  Can be boolean, a string, or an array of strings.
		 *
		 * @var null|bool|string|string[]
		 */
		public null|bool|string|array $authRoles = null;
		/**
		 * Callback to use when the endpoint is the given route.
		 *
		 * @var null|callable
		 */
		public mixed $callback = null;
		/**
		 * String pattern for the URL matching.
		 *
		 * @var null|string
		 */
		public ?string $pattern = null;


		/**
		 * Instantiates a new ApiEndpoint object using the given optional values.
		 *
		 * @param mixed $authRoles String, array of string values, or boolean representing role(s) or a basic authorized/not-authorized requirement for the request.
		 * @param null|callable $callback Endpoint callback to use when the pattern matches the request.
		 * @param null|string $pattern String of URL pattern for callback routing.
		 */
		public function __construct(mixed $authRoles = false, ?callable $callback = null, ?string $pattern = null) {
			$this->authRoles = $authRoles;
			$this->callback = $callback;
			$this->pattern = $pattern;

			return;
		}
	}

	/**
	 * Struct for holding the 'predefined' global variables for a page request.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class PageVariables {
		/**
		 * HTTP cookies.
		 *
		 * @var array
		 */
		public array $cookie;
		/**
		 * Environment variables.
		 *
		 * @var array
		 */
		public array $env;
		/**
		 * HTTP file upload variables.
		 *
		 * @var array
		 */
		public array $files;
		/**
		 * HTTP GET variables.
		 *
		 * @var array
		 */
		public array $get;
		/**
		 * HTTP POST variables.
		 *
		 * @var array
		 */
		public array $post;
		/**
		 * HTTP request variables.
		 *
		 * @var array
		 */
		public array $request;
		/**
		 * Server and execution environment information.
		 *
		 * @var array
		 */
		public array $server;
		/**
		 * Session variables.
		 *
		 * @var array
		 */
		public array $session;


		/**
		 * Static method to return the 'predefined' global variables assigned to a struct instance.
		 *
		 * @return PageVariables
		 */
		public static function fromGlobals() : PageVariables {
			return new PageVariables(
				$_COOKIE  ?? [],
				$_ENV     ?? [],
				$_FILES   ?? [],
				$_GET     ?? [],
				$_POST    ?? [],
				$_REQUEST ?? [],
				$_SERVER  ?? [],
				$_SESSION ?? []
			);
		}


		/**
		 * Instantiates a new PageVariables instance using the provided arrays for the 'predefined' variables.
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

	/**
	 * Struct for holding information on an uploaded file.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class UploadedFile {
		/**
		 * The error code associated with the file upload.
		 *
		 * @var int
		 */
		public int $error;
		/**
		 * Original name of the file on the client machine.
		 *
		 * @var string
		 */
		public string $name;
		/**
		 * The size, in bytes, of the uploaded file.
		 *
		 * @var int
		 */
		public int $size;
		/**
		 * The temporary file name of the file on the server.
		 *
		 * @var string
		 */
		public string $tmpName;
		/**
		 * The MIME type of the file, if provided by the browser.
		 *
		 * @var string
		 */
		public string $type;


		/**
		 * Instantiates a new UploadedFile instance using the provided information.
		 *
		 * @param int $error Error code for file upload.
		 * @param string $name Original name of the file.
		 * @param int $size Size of uploaded file in bytes.
		 * @param string $tmpName Temporary file name on server.
		 * @param string $type MIME type of file.
		 */
		public function __construct(int $error, string $name, int $size, string $tmpName, string $type) {
			$this->error = $error;
			$this->name = $name;
			$this->size = $size;
			$this->tmpName = $tmpName;
			$this->type = $type;

			return;
		}

		/**
		 * Returns a string explaining the uploaded file's error code.
		 *
		 * @return string
		 */
		public function getError() : string {
			return match ($this->error) {
				UPLOAD_ERR_OK         => "Upload completed successfully.",
				UPLOAD_ERR_INI_SIZE   => "Upload exceeded maximum file size on server",
				UPLOAD_ERR_FORM_SIZE  => "Upload exceeded maximum file size in browser",
				UPLOAD_ERR_PARTIAL    => "Upload didn't complete",
				UPLOAD_ERR_NO_FILE    => "No file was uploaded",
				UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder on server",
				UPLOAD_ERR_CANT_WRITE => "Failed to write upload to disk",
				UPLOAD_ERR_EXTENSION  => "A server extension stopped the upload",
				default               => "Unknown error code during file upload",
			};
		}

		/**
		 * Determines if the file was uploaded successfully.
		 *
		 * @return bool
		 */
		public function isValid() : bool {
			return $this->error == 0;
		}
	}
