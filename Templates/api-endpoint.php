<?php

	namespace %NAMESPACE%;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbClass;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\PageHelper;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\HttpStatusCodes;

	global $Api;

	class %CLASS_NAME% extends StoicDbClass {
		public function __construct(
			protected Stoic $stoic,
			protected PdoHelper $db,
			protected Logger $log      = null,
			protected PageHelper $page = null
		) {
			parent::__construct($db, $log);

			if ($this->page === null) {
				$this->page = PageHelper::getPage('api/index.php');
			}

			return;
		}

		public function get(Request $request, array $matches = null) : Response {
			$ret = new Response(HttpStatusCodes::OK);

			return $ret;
		}
	}

	$Api->registerEndpoint('GET', '/\/?%CLASS_NAME%\/?/i', 'get');
