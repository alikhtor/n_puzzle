<?php

require "LinearConflict.php";

class Neighbours extends LinearConflict {

    public function findNeighbours() {
		$brds = array();
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY + 1));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY - 1));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX - 1, $this->zeroY));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX + 1, $this->zeroY));
		return $brds;
	}

	private function makeChange($board, $x1, $y1, $x2, $y2) {
		if ($x2 > -1 && $x2 < count($this->blocks) && $y2 > -1 && $y2 < count($this->blocks)) {
			$t = $board[$x2][$y2];
			$board[$x2][$y2] = $board[$x1][$y1];
			$board[$x1][$y1] = $t;
			return new Board($board, $this->final);
		} else
			return null;
	}
}

?>