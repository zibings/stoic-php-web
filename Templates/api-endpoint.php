<?php

	namespace {$namespace};

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbClass;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\PageHelper;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\HttpStatusCodes;

	global $Api;

	class {$className} extends StoicDbClass {
		protected null|PageHelper $page = null;


		public function __construct(
			protected Stoic $stoic,
			PdoHelper $db,
			Logger $log      = null,
			PageHelper $page = null
		) {
			parent::__construct($db, $log);

			if ($page === null) {
				$this->page = PageHelper::getPage('api/index.php');
			}

			return;
		}

		public function get(Request $request, array $matches = null) : Response {
			$ret = new Response(HttpStatusCodes::OK);

			return $ret;
		}
	}

	$Api->registerEndpoint('GET', '/\/?{$className}\/?/i', 'get');
