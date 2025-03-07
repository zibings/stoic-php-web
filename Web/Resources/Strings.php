<?php

	namespace Stoic\Web\Resources;

	/**
	 * Collection of index constants for the PHP $_SERVER global variable.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class ServerIndices {
		const string AUTH_TYPE            = 'AUTH_TYPE';
		const string DOCUMENT_ROOT        = 'DOCUMENT_ROOT';
		const string GATEWAY_INTERFACE    = 'GATEWAY_INTERFACE';
		const string HTTP_ACCEPT          = 'HTTP_ACCEPT';
		const string HTTP_ACCEPT_CHARSET  = 'HTTP_ACCEPT_CHARSET';
		const string HTTP_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING';
		const string HTTP_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE';
		const string HTTP_CONNECTION      = 'HTTP_CONNECTION';
		const string HTTP_HOST            = 'HTTP_HOST';
		const string HTTP_ORIGIN          = 'HTTP_ORIGIN';
		const string HTTP_REFERER         = 'HTTP_REFERER';
		const string HTTP_USER_AGENT      = 'HTTP_USER_AGENT';
		const string HTTPS                = 'HTTPS';
		const string ORIG_PATH_INFO       = 'ORIG_PATH_INFO';
		const string PATH_INFO            = 'PATH_INFO';
		const string PATH_TRANSLATED      = 'PATH_TRANSLATED';
		const string PHP_AUTH_DIGEST      = 'PHP_AUTH_DIGEST';
		const string PHP_AUTH_USER        = 'PHP_AUTH_USER';
		const string PHP_AUTH_PW          = 'PHP_AUTH_PW';
		const string PHP_SELF             = 'PHP_SELF';
		const string QUERY_STRING         = 'QUERY_STRING';
		const string REDIRECT_REMOTE_USER = 'REDIRECT_REMOTE_USER';
		const string REMOTE_ADDR          = 'REMOTE_ADDR';
		const string REMOTE_HOST          = 'REMOTE_HOST';
		const string REMOTE_PORT          = 'REMOTE_PORT';
		const string REQUEST_METHOD       = 'REQUEST_METHOD';
		const string REQUEST_TIME         = 'REQUEST_TIME';
		const string REQUEST_TIME_FLOAT   = 'REQUEST_TIME_FLOAT';
		const string REQUEST_URI          = 'REQUEST_URI';
		const string SERVER_ADDR          = 'SERVER_ADDR';
		const string SERVER_ADMIN         = 'SERVER_ADMIN';
		const string SERVER_NAME          = 'SERVER_NAME';
		const string SERVER_PORT          = 'SERVER_PORT';
		const string SERVER_PROTOCOL      = 'SERVER_PROTOCOL';
		const string SERVER_SIGNATURE     = 'SERVER_SIGNATURE';
		const string SERVER_SOFTWARE      = 'SERVER_SOFTWARE';
		const string SCRIPT_FILENAME      = 'SCRIPT_FILENAME';
		const string SCRIPT_NAME          = 'SCRIPT_NAME';
	}

	/**
	 * Collection of index constants used for initializing an ApiAuthorizationDispatch object.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class AuthorizationDispatchStrings {
		const string INDEX_CONSUMABLE = 'consumable';
		const string INDEX_INPUT      = 'input';
		const string INDEX_ROLES      = 'roles';
	}

	/**
	 * Collection of various strings used for Stoic operation.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class StoicStrings {
		const string SETTINGS_FILE_PATH = '~/siteSettings.json';
	}

	/**
	 * Collection of various settings strings for Stoic.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class SettingsStrings {
		const string API_CACHE_CONTROL = 'api.cacheControl';
		const string API_CONTENT_TYPE  = 'api.contentType';
		const string CLASSES_EXTENSION = 'classesExt';
		const string CLASSES_PATH      = 'classesPath';
		const string CORS_HEADERS      = 'cors.headers';
		const string CORS_METHODS      = 'cors.methods';
		const string CORS_ORIGINS      = 'cors.origins';
		const string DB_DSNS           = 'dbDsns';
		const string DB_DSN_DEFAULT    = 'dbDsns.default';
		const string DB_PASSES         = 'dbPasses';
		const string DB_PASS_DEFAULT   = 'dbPasses.default';
		const string DB_USERS          = 'dbUsers';
		const string DB_USER_DEFAULT   = 'dbUsers.default';
		const string INCLUDE_PATH      = 'includePath';
		const string MIGRATE_CFG_PATH  = 'migrateCfg';
		const string MIGRATE_DB_PATH   = 'migrateDb';
		const string REPOS_EXTENSION   = 'reposExt';
		const string REPOS_PATH        = 'reposPath';
		const string UTILITIES_EXT     = 'utilitiesExt';
		const string UTILITIES_PATH    = 'utilitiesPath';
	}
