<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\FileUploadHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;

	class StoicTest extends TestCase {
		public function test_Instantiation() {
			self::assertEquals(0, count(Stoic::getInstanceStack()));

			$pv = new PageVariables([], [], [], ['test1' => 'val1'], [], [], [], []);
			$stoic = Stoic::getInstance($pv);

			self::assertEquals(1, count(Stoic::getInstanceStack()));
			self::assertEquals('val1', Stoic::getInstanceStack()[0]->getGet()->getString('test1'));
			self::assertEquals('val1', $stoic->getGet()->getString('test1'));

			return;
		}

		public function test_GetMethods() {
			$pv = new PageVariables(['test1' => 'val1'], ['test1' => 'val1'], [
				'test1' => [
					'name' => '',
					'type' => '',
					'size' => 0,
					'tmp_name' => '',
					'error' => UPLOAD_ERR_OK
				]
			], ['test1' => 'val1'], ['test1' => 'val1'], ['test1' => 'val1'], ['test1' => 'val1'], ['test1' => 'val1']);
			$stoic1 = Stoic::getInstance($pv);

			self::assertInstanceOf(ParameterHelper::class, $stoic1->getCookies());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getEnv());
			self::assertInstanceOf(FileUploadHelper::class, $stoic1->getFiles());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getGet());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getPost());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getRequest());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getServer());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getSession());
			self::assertInstanceOf(PageVariables::class, $stoic1->getVariables());
			self::assertInstanceOf(Logger::class, $stoic1->log());

			return;
		}
	}
