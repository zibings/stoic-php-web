<?php

	namespace Stoic\Tests\Web\Api;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Api\BaseDbApi;
	use Stoic\Web\Api\Request;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Resources\HttpStatusCodes;
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
			self::assertTrue($tst->hasVars(new Request(['REQUEST_METHOD' => 'GET'], null, ['test1' => 'val1']), ['test1']));
			self::assertFalse($tst->hasVars(new Request(['REQUEST_METHOD' => 'GET'], null, ['test1' => 'val1']), ['test2']));

			return;
		}
	}
