<?php

	namespace Stoic\Web\Api;

	class Response extends \Stoic\Web\Response {
		public function setData($data) {
			if ($data === null) {
				$this->data = '';
			} else if (!is_string($data)) {
				$this->data = json_encode($data);
			} else {
				$this->data = $data;
			}

			return;
		}
	}
