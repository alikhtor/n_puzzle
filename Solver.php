<?php

require "Queue.php";

class Solver {
	private $initial;
	private $priorityQueue;
	private $close;
	private $list;
	private $complexityInTime;
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

	private function createResultArray() {
		while (true) {
			array_push($this->close, $this->priorityQueue->top()->current()->blocks);
			$current = $this->priorityQueue->top();
			$this->list = $this->priorityQueue->extract();
			if (!$this->list->current()->amOnPlace()) {
					$count = $this->list->count();
				while (!$this->list->isEmpty()) {
					$this->printBoard($this->list->pop()->blocks);
					print "\n";
				}
				print "Moves need to win - " . $count . "\n";
				print "Complexity in time - " . $this->complexityInTime . "\n";
				print "Complexity in size - " . ($this->complexityInTime - $count) . "\n";
				return ($this->list);
			}

			$iterrator = $this->list->current()->findNeighbors();

			foreach ($iterrator as $key => $value) {
				if ($value != null) {
					if (array_search($value->blocks, $this->close) == false) {
						$new = new splDoublyLinkedList();
						$new->unshift($value);
						$new = $this->copyList2($new, $current);
						$new->rewind();
						$new->current()->g += 1;
						$this->priorityQueue->insert($new, $new->current()->g + $new->current()->heuristic);
							$this->complexityInTime++;
					}
				}
			}
		}
		return $this->priorityQueue->top();
	}

	private function printBoard($board) {
		foreach ($board as $key => $item) {
			foreach ($item as $val) {
			    if ($val == 0)
	    			print "\33[90m" . $val . "\33[0m";
			    else
			        print "\33[35m" . $val . "\33[0m";
				if ($val < 10)
				    print "  ";
				else
				    print " ";
			}
			print " \n";
		}
	}

	private function copyList($dll, $elem) {
		$result = new splDoublyLinkedList();
		foreach ($dll as $key => $value) {
			$result->unshift($value);
		}
		if ($elem != null)
			$result->unshift($elem);
		return $result;
	}

	private function copyList2($new, $curr) {
		$curr->rewind();
		while ($curr->valid()) {
			$new->push($curr->current());
			$curr->next();
		}
		return $new;
	}

}

?>
