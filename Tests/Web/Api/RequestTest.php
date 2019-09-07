<?php

	namespace Stoic\Tests\Web\Api;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Api\Request;
	use Stoic\Web\Resources\InvalidRequestException;
	use Stoic\Web\Resources\NonJsonInputException;
	use Stoic\Web\Resources\RequestType;

	class RequestTest extends TestCase {
		public function test_Initialization() {
			try {
				$req = new Request();
				self::assertTrue(false);
			} catch (InvalidRequestException $ex) {
				self::assertEquals("Server collection was missing 'REQUEST_METHOD' value", $ex->getMessage());
			}

			try {
				$req = new Request(['REQUEST_METHOD' => 'JIM']);
				self::assertTrue(false);
			} catch (InvalidRequestException $ex) {
				self::assertEquals("Invalid request method provided: JIM", $ex->getMessage());
			}

			$req = new Request(['REQUEST_METHOD' => 'GET']);
			self::assertTrue($req->isValid());

			$req = new Request(['REQUEST_METHOD' => 'POST'], 'true');
			self::assertTrue($req->isValid());
			self::assertEquals('true', $req->getRawInput());

			try {
				self::assertEquals(true, $req->getParameterizedInput());
			} catch (NonJsonInputException $ex) {
				self::assertEquals("Can't get parameterized input for non-json payload", $ex->getMessage());
			}

			$req = new Request(['REQUEST_METHOD' => 'POST']);
			self::assertFalse($req->isValid());
			self::assertTrue($req->getRequestType()->is(RequestType::POST));

			return;
		}

		public function test_Parameters() {
			$req = new Request(['REQUEST_METHOD' => 'POST'], 'true', ['test1' => 'val1'], ['test2' => 'val2']);
			self::assertEquals('val2', $req->getParameterizedCookie()->getString('test2'));
			self::assertEquals('val1', $req->getParameterizedGet()->getString('test1'));

			$req = new Request(['REQUEST_METHOD' => 'GET'], null, ['test1' => 'val1']);
			self::assertEquals('val1', $req->getParameterizedInput()->getString('test1'));

			return;
		}
	}
