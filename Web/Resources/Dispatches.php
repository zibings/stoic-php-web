<?php

	namespace Stoic\Web\Resources;

	use Stoic\Chain\DispatchBase;
	use Stoic\Utilities\ParameterHelper;

	/**
	 * Dispatch to allow processing of request authorization.
	 *
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class ApiAuthorizationDispatch extends DispatchBase {
		/**
		 * Internal ParameterHelper instance containing the request input.
		 *
		 * @var ParameterHelper
		 */
		protected $input = null;
		/**
		 * Internal state determining if request is authorized.
		 *
		 * @var boolean
		 */
		protected $isAuthorized = false;
		/**
		 * The required roles (if any) that were required by the handler.
		 *
		 * @var null|boolean|string[]
		 */
		protected $requiredRoles = null;


		/**
		 * Authorizes the dispatch, setting internal state to `true`.
		 *
		 * @return void
		 */
		public function authorize() : void {
			$this->isAuthorized = true;

			return;
		}

		/**
		 * Retrieves the ParameterHelper instance containing the request input.
		 *
		 * @return ParameterHelper
		 */
		public function getInput() : ParameterHelper {
			return $this->input;
		}

		/**
		 * Retrieves roles (if any) required by the handler.
		 *
		 * @return boolean|null|string[]
		 */
		public function getRequiredRoles() {
			return $this->requiredRoles;
		}

		/**
		 * Initializes the dispatch so it can be processed.  Requires at minimum
		 * the `AuthorizationDispatchStrings::INDEX_INPUT` and
		 * `AuthorizationDispatchStrings::INDEX_ROLES` array values, with the
		 * `AuthorizationDispatchStrings::INDEX_CONSUMABLE` value being optional to
		 * specify if the dispatch should be marked as 'consumable'.
		 *
		 * @param mixed $input Input array variable, fails gracefully if in wrong format.
		 * @return void
		 */
		public function initialize($input) {
			if (!is_array($input) || count($input) < 2) {
				return;
			}

			if (array_key_exists(AuthorizationDispatchStrings::INDEX_INPUT, $input) === false || array_key_exists(AuthorizationDispatchStrings::INDEX_ROLES, $input) === false) {
				return;
			}

			if (!($input[AuthorizationDispatchStrings::INDEX_INPUT] instanceof ParameterHelper)) {
				return;
			}

			$this->input = $input[AuthorizationDispatchStrings::INDEX_INPUT];
			$this->requiredRoles = $input[AuthorizationDispatchStrings::INDEX_ROLES];

			if (array_key_exists(AuthorizationDispatchStrings::INDEX_CONSUMABLE, $input) !== false && $input[AuthorizationDispatchStrings::INDEX_CONSUMABLE]) {
				$this->makeConsumable();
			}

			$this->makeValid();

			return;
		}

		/**
		 * Returns the internal authorized state.
		 *
		 * @return boolean
		 */
		public function isAuthorized() : bool {
			return $this->isAuthorized;
		}
	}

	/**
	 * Dispatch to facilitate collection of base variables during an API request.
	 *
	 * @package Stoic\Web
	 * @version 1.0.0
	 */
	class ApiBaseVarDispatch extends DispatchBase {
		/**
		 * Internal stack of ParameterHelper states which stored base variables.
		 *
		 * @var \SplStack
		 */
		protected $baseVars = null;


		/**
		 * Adds a variable to the internal stack.
		 *
		 * @param string $key String value for the variable key.
		 * @param mixed $value Mixed value for the variable.
		 * @return void
		 */
		public function addVar(string $key, $value) : void {
			$this->baseVars->push($this->baseVars->top()->withParameter($key, $value));

			return;
		}

		/**
		 * Adds a collection of variables to the internal stack.  The array must be
		 * in the following format:
		 *
		 * [ "key" => value ]
		 *
		 * Where `value` is any type of data.
		 *
		 * @param array $vars Collection of variables to add to the stack.
		 * @return void
		 */
		public function addVars(array $vars) : void {
			$this->baseVars->push($this->baseVars->top()->withParameters($vars));

			return;
		}

		/**
		 * Returns the current collection of variables from the stack.
		 *
		 * @return ParameterHelper
		 */
		public function getVars() : ParameterHelper {
			return $this->baseVars->top();
		}

		/**
		 * Returns a copy of the internal stack for historical purposes.
		 *
		 * @return \SplStack
		 */
		public function getVarStack() : \SplStack {
			return clone $this->baseVars;
		}

		/**
		 * Initializes the dispatch so it can be processed. Accepts no data.
		 *
		 * @param mixed $input Unused initialization data.
		 * @return void
		 */
		public function initialize($input) {
			$this->makeValid();
			$this->baseVars = new \SplStack();
			$this->baseVars->push(new ParameterHelper());

			return;
		}
	}
