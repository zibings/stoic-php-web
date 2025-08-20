<?php

	const STOIC_CORE_PATH = '{$corePath}';
	require(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Utilities\LogFileAppender;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Resources\SettingsStrings;

	global $Api, $Db, $Log, $Settings;

	/**
	 * @var \Stoic\Web\Api\Stoic $Api
	 * @var \Stoic\Pdo\PdoHelper $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 */

	$Api = Stoic::getInstance(STOIC_CORE_PATH, null, $Log);

	if ($Settings->get(SettingsStrings::ENABLE_LOGGING, false) !== false) {
		$fh = $Api->getFileHelper();

		try {
			if (!$fh->folderExists('~/logs')) {
				$fh->makeFolder('~/logs', 0644, true);
			}

			$Log->addAppender(new LogFileAppender($fh, '~/logs/api.log'));
		} catch (\Exception $e) {
			http_response_code(500);

			echo(json_encode("Error setting up logging: " . $e->getMessage()));
		}
	}

	$endpoints         = [];
	$endpointNamespace = '{$namespace}';
	$endpointExtension = '.api.php';
	$loadedFiles       = $Api->loadFilesByExtension('{$apiFolder}', $endpointExtension);

	foreach ($loadedFiles as $file) {
		$f           = str_replace($endpointExtension, '', basename($file));
		$cls         = "\\{$endpointNamespace}\\{$f}";
		$endpoints[] = new $cls($Api, $Db, $Log);
	}

	try {
		$Api->handle();;
	} catch (\Exception $e) {
		$Log->error("API Error: " . $e->getMessage());
		http_response_code(500);
		echo json_encode(['error' => 'Internal Server Error']);
	}
