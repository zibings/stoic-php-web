<?php

	namespace Stoic\Web;

	/**
	 * Performs common pagination math operations.
	 *
	 * @package Stoic\Web
	 * @version 1.1.0
	 */
	class PaginateHelper {
		/**
		 * Number of the currently active page.
		 *
		 * @var int
		 */
		public int $currentPage;
		/**
		 * Number of entries to count per page.
		 *
		 * @var int
		 */
		public int $entriesPerPage;
		/**
		 * Entry offset based on current page.
		 *
		 * @var int
		 */
		public int $entryOffset;
		/**
		 * Number of the previous page.
		 *
		 * @var int
		 */
		public int $lastPage;
		/**
		 * Number of the next page.
		 *
		 * @var int
		 */
		public int $nextPage;
		/**
		 * Total number of entries in set.
		 *
		 * @var int
		 */
		public int $totalEntries;
		/**
		 * Total number of pages in set.
		 *
		 * @var int
		 */
		public int $totalPages;


		/**
		 * Instantiates a new PaginateHelper object, calculating offset and total/next/last pages.
		 *
		 * @param int $currentPage Current page number for set.
		 * @param int $totalEntries Total number of entries in set.
		 * @param int $entriesPerPage Number of entries per page in set.
		 */
		public function __construct(int $currentPage, int $totalEntries, int $entriesPerPage) {
			$this->currentPage = $currentPage;
			$this->entriesPerPage = $entriesPerPage;
			$this->totalEntries = $totalEntries;

			$this->calculate();

			return;
		}

		/**
		 * Calculates all pagination metrics based on supplied data.
		 *
		 * @return void
		 */
		private function calculate() : void {
			if ($this->totalEntries < 1) {
				$this->totalPages = 1;
				$this->nextPage = 0;
				$this->lastPage = 0;
				$this->entryOffset = 0;

				return;
			}

			if ($this->currentPage < 1) {
				$this->currentPage = 1;
			}

			if ($this->entriesPerPage > $this->totalEntries) {
				$this->totalPages = 1;
			} else {
				if (($this->totalEntries % $this->entriesPerPage) == 0) {
					$this->totalPages = floor($this->totalEntries / $this->entriesPerPage);
				} else {
					$this->totalPages = floor(($this->totalEntries / $this->entriesPerPage) + 1);
				}
			}

			if ($this->currentPage > $this->totalPages) {
				$this->currentPage = $this->totalPages;
			}

			$this->entryOffset = (($this->currentPage - 1) * $this->entriesPerPage);
			$this->lastPage = ($this->currentPage < 2) ? 0 : ($this->currentPage - 1);
			$this->nextPage = (($this->totalPages - $this->currentPage) < 1) ? 0 : ($this->currentPage + 1);

			return;
		}

		/**
		 * Returns array of page numbers based on current metrics.
		 *
		 * @param int $numPages Optional number of page indices to produce.
		 * @return int[]
		 */
		public function getPages(int $numPages = 5) : array {
			$st  = 0;
			$ret = [];

			if ($this->currentPage > 0 && $this->totalPages > 0 && $numPages > 0) {
				if ($this->totalPages < $numPages) {
					$st = 1;
				} else {
					$st = floor($this->currentPage - (($numPages / 2) - 1));

					if (($this->totalPages - $st) < $numPages) {
						$st -= (($numPages - ($this->totalPages - $st)) - 1);

						if ($st < 1) {
							// @codeCoverageIgnoreStart
							$st = 1;
							// @codeCoverageIgnoreEnd
						}
					}
				}
			}

			for (; $st <= $this->totalPages; $st++) {
				if (count($ret) == $numPages) {
					break;
				}

				$ret[] = $st;
			}

			return $ret;
		}
	}
