<?php

require "Queue.php";
require "Copy.php";

class Solver extends Copy {
	private $initial;
	private $priorityQueue;
	private $close;
	private $list;
	private $complexityInTime;
	private $neighbours_iterator;
	private $zero = 0;

	function __construct($initial) {

		$this->initial = $initial;
		$this->priorityQueue = new Queue();
		$this->close = array();
		$this->list = new splDoublyLinkedList();
		$this->complexityInTime = $this->zero;
	}

	public function solve() {
		$this->list->unshift($this->initial);
		$this->list->rewind();
		$this->priorityQueue->insert($this->list, 1000000);
		return $this->createResultArray();
	}

	private function findNeighboursToIterate() {
		$this->neighbours_iterator = $this->list->current()->findNeighbours();
	}

	private function iterateNeighbours($current) {
		foreach ($this->neighbours_iterator as $key => $value) {
			if ($value != null) {
				if (array_search($value->blocks, $this->close) == false) {
					$new = new splDoublyLinkedList();
					$new->unshift($value);
					$new = $this->copy_list($new, $current);
					$new->rewind();
					$new->current()->g += 1;
					$this->priorityQueue->insert($new, $new->current()->g + $new->current()->heuristic);
						$this->complexityInTime++;
				}
			}
		}
	}

	private function createResultArray() {
		while (true) {
			array_push($this->close, $this->priorityQueue->top()->current()->blocks);
			$current = $this->priorityQueue->top();
			$this->list = $this->priorityQueue->extract();
			if (!$this->list->current()->onPlace()) {
				$this->list = $this->call_printer($this->list, $this->complexityInTime);
				return ($this->list);
			}
			$this->findNeighboursToIterate();
			$this->iterateNeighbours($current);
		}
		return $this->priorityQueue->top();
	}
}

?>
