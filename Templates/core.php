<?php

	$stoicDefaultConstants = [
		'STOIC_CORE_PATH'             => './',
		'STOIC_API_AUTH_COOKIE'       => true,
		'STOIC_DISABLE_SESSION'       => false,
		'STOIC_DISABLE_DB_EXCEPTIONS' => false,
		'STOIC_ENABLE_DEBUG'          => false
	];

	foreach ($stoicDefaultConstants as $name => $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}

	$corePath = STOIC_CORE_PATH;

	if (!str_ends_with($corePath, '/')) {
		$corePath .= '/';
	}

	if (STOIC_ENABLE_DEBUG) {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	}

	require($corePath . 'vendor/autoload.php');

	use AndyM84\Config\ConfigContainer;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;

	global $Db, $Log, $Settings, $Stoic;

	/**
	 * @var PdoHelper $Db
	 * @var Logger $Log
	 * @var ConfigContainer $Settings
	 * @var Stoic $Stoic
	 */

	if (PHP_SAPI == 'cli') {
		$Stoic = Stoic::getInstance(STOIC_CORE_PATH, new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []));
	} else {
		$Stoic = Stoic::getInstance(STOIC_CORE_PATH);
	}

	$Log      = $Stoic->getLog();
	$Db       = $Stoic->getDb();
	$Session  = $Stoic->getSession();
	$Settings = $Stoic->getConfig();
