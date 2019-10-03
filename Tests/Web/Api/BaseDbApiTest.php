<?php

	namespace Stoic\Tests\Web\Api;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Api\BaseDbApi;
	use Stoic\Web\Request;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Resources\HttpStatusCodes;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\RequestType;

	class TestApiClass extends BaseDbApi {
		public function getResponse() : Response {
			return $this->newResponse();
		}

		public function hasVars(Request $request, array $keys) : bool {
			return $this->requestHasInputVars($request, $keys);
		}
	}

	class BaseDbApiTest extends TestCase {
		public function test_TestClass() {
			$tst = new TestApiClass(new \Pseudo\Pdo());

			self::assertTrue($tst->getResponse()->getStatus()->is(HttpStatusCodes::OK));

			$req = new Request(new PageVariables([], [], [], ['test1' => 'val1'], [], [], ['REQUEST_METHOD' => 'GET'], []));
			self::assertTrue($tst->hasVars($req, ['test1']));
			self::assertFalse($tst->hasVars($req, ['test2']));

			return;
		}
	}