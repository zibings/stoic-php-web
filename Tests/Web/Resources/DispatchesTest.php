<?php

	namespace Stoic\Tests\Web\Resources;

	use PHPUnit\Framework\TestCase;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\ApiAuthorizationDispatch;
	use Stoic\Web\Resources\ApiBaseVarDispatch;
	use Stoic\Web\Resources\AuthorizationDispatchStrings;

	class DispatchesTest extends TestCase {
		public function test_AuthDispatch() {
			$disp = new ApiAuthorizationDispatch();
			
			$disp->initialize(null);
			self::assertFalse($disp->isValid());

			$disp->initialize([]);
			self::assertFalse($disp->isValid());

			$disp->initialize(['test', 'test2']);
			self::assertFalse($disp->isValid());

			$disp->initialize([
				AuthorizationDispatchStrings::INDEX_INPUT => null,
				AuthorizationDispatchStrings::INDEX_ROLES => true
			]);

			self::assertFalse($disp->isValid());

			$disp->initialize([
				AuthorizationDispatchStrings::INDEX_INPUT => new ParameterHelper(),
				AuthorizationDispatchStrings::INDEX_ROLES => true,
				AuthorizationDispatchStrings::INDEX_CONSUMABLE => true
			]);

			self::assertTrue($disp->isValid());
			self::assertTrue($disp->isConsumable());
			self::assertTrue($disp->getRequiredRoles());
			self::assertTrue($disp->getInput() !== null);
			self::assertFalse($disp->isAuthorized());

			$disp->authorize();
			self::assertTrue($disp->isAuthorized());

			return;
		}

		public function test_BaseVarDispatch() {
			$disp = new ApiBaseVarDispatch();
			$disp->initialize(null);

			$disp->addVar('test1', 'test1');
			self::assertEquals('test1', $disp->getVars()->getString('test1'));

			$disp->addVars(['test2' => 'test2']);
			self::assertEquals('test2', $disp->getVars()->getString('test2'));

			$stack = $disp->getVarStack();
			self::assertEquals('test1', $stack->top()->getString('test1'));

			$stack->push($stack->top()->withParameter('test3', 'test3'));
			self::assertNull($disp->getVars()->getString('test3'));

			return;
		}
	}
