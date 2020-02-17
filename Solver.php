<?php

require "Queue.php";

class Solver {
	private $initial;

	function __construct($initial) {
		$this->initial = $initial;
	}

	function printBoard($board) {
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

	function copyList($dll, $elem) {
		$result = new splDoublyLinkedList();

		foreach ($dll as $key => $value) {
			$result->unshift($value);
		}
		if ($elem != null)
			$result->unshift($elem);
		return $result;
	}

	function copyL($new, $curr) {
		$curr->rewind();
		while ($curr->valid()) {
			$new->push($curr->current());
			$curr->next();
		}
		return $new;
	}

	function solve() {

		$close = array();
		$priorityQueue = new Queue();
		$list = new splDoublyLinkedList();
		$list->unshift($this->initial);
		$list->rewind();
		$priorityQueue->insert($list, 1000000);
        $complexityInTime = 0;
		while (true) {
			array_push($close, $priorityQueue->top()->current()->blocks);
			$current = $priorityQueue->top();
			$list = $priorityQueue->extract();
			if (!$list->current()->amOnPlace()) {
			    $count = $list->count();
				while (!$list->isEmpty()) {
					$this->printBoard($list->pop()->blocks);
					print "\n";
				}
        print "Moves need to win - " . $count . "\n";
        print "Complexity in time - " . $complexityInTime . "\n";
				print "Complexity in size - " . ($complexityInTime - $count) . "\n";
				return ($list);
			}

			$iterrator = $list->current()->findNeighbors();

			foreach ($iterrator as $key => $value) {
				if ($value != null) {
					if (array_search($value->blocks, $close) == false) {
						$new = new splDoublyLinkedList();
						$new->unshift($value);
						$new = $this->copyL($new, $current);
						$new->rewind();
						$new->current()->g += 1;
						$priorityQueue->insert($new, $this->measure($new->current()));
					    $complexityInTime++;
					}
				}
			}
		}

		return $priorityQueue->top();
	}

	function contInPath(splDoublyLinkedList $list, Board $board) {
		$tmp = $this->copyList($list, null);
		$tmp->rewind();
		while ($tmp->valid()) {
			$tmp->next();
			if ($tmp->current() == $board) {
				print "is in path";
				return true;
			}
		}
		return false;
	}

	function measure(Board $obj) {
		$c = $obj->g;
		$measure = $obj->heuristic;

		return $c + $measure;
	}
}

?>
