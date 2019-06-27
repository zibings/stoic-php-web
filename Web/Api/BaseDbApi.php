<?php

	namespace Stoic\Web\Api;

	use Stoic\Utilities\BaseDbClass;

	class BaseDbApi extends BaseDbClass {
		protected function newResponse() : Response {
			return new Response();
		}


	}
