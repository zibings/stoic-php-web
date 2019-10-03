<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;

	class StoicTest extends TestCase {
		public function test_Instantiation() {
			$count = count(Stoic::getInstanceStack());

			$pv = new PageVariables([], [], [], ['test1' => 'val1'], [], [], ['REQUEST_METHOD' => 'GET'], []);
			$stoic = Stoic::getInstance('./', $pv, new Logger());

			self::assertEquals($count + 1, count(Stoic::getInstanceStack()));
			self::assertEquals('val1', Stoic::getInstanceStack()[0]->getRequest()->getGet()->getString('test1'));
			self::assertEquals('val1', $stoic->getRequest()->getGet()->getString('test1'));

			return;
		}

		public function test_GetMethods() {
			$pv = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []);
			$stoic1 = Stoic::getInstance('./', $pv, new Logger());

			self::assertInstanceOf(Logger::class, $stoic1->log());

			return;
		}
	}
