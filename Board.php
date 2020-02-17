<?php

class Board {

	private $blocks;
	private $zeroX;
	private $zeroY;
	private $heuristic;
	private $g;
	private $final;

	public function __construct($array, $final) {
		global $HEURISTIC_FUNC_FLAG;

		$tmp = $array;
		$this->blocks = $tmp;
		$this->heuristic = 0;
		$this->g = 0;
		$this->final = $final;

		$this->fillBoard($array, $final);
	}

	private function fillBoard($array, $final) {
		foreach ($array as $key => $value) {
			foreach ($value as $k => $v) {
				if ($v != ($this->final[$key][$k])) {
					$this->heuristic += abs($this->getXY($final, $v, 'x') - $key) + abs($this->getXY($final, $v, 'y') - $k);
				} if ($v == 0) {
					$this->zeroX = (int)$key;
					$this->zeroY = (int)$k;
				}
			}
			if ($HEURISTIC_FUNC_FLAG == "-lc")
								$this->heuristic += $this->linearConflict($value, $key);
		}
		if ($HEURISTIC_FUNC_FLAG == "-wp") {
				$this->heuristic = 0;
						foreach ($array as $key => $value) {
								foreach ($value as $k =>$v)
										if ($v != $final[$key][$k])
												$this->heuristic++;
				}
		}
	}

	private function linearConflict($row, $numRow) {
		$n = 0;
		foreach ($row as $key => $value)
			for ($k = $key; $k < $this->getDimencion(); $k++)
				if (!$this->checkOrder($value, $row[$k], $key, $k, $numRow))
					$n += 1;
		return $n;
	}

	private function checkOrder($x1, $x2, $k1, $k2, $i) {
		if (in_array($x1, $this->final[$i])) {
			$fk1 = array_search($x1, $this->final[$i]);
			if (in_array($x2, $this->final[$i])) {
				$fk2 = array_search($x2, $this->final[$i]);
				if ($fk1 > $fk2) {
					if ($k1 < $k2)
						return 0;
				} else if ($fk2 > $fk1) {
					if ($k2 < $k1)
						return 0;
				}
			}
		}
		return 1;
	}

	private function getXY($final, $c, $what) {
		foreach ($final as $key => $item) {
			foreach ($item as $k => $val) {
				if ($val == $c) {
					if ($what == 'x')
						return $key;
					if ($what == 'y')
						return $k;
				}
			}
		}
	}

	public function getDimencion() {
		return count($this->blocks);
	}

	public function __get($name) {
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}

	public function __set($name, $value)
	{
		if (property_exists($this, $name) && $value != null) {
			$this->$name = $value;
		}
	}

	public function amOnPlace() {
		return $this->heuristic != 0;
	}

	public function equals(Board $o) {
		if ($this == $o)
			return true;

		if ($o->getDimencion() != $this->getDimencion())
			return false;

		foreach ($o as $key => $value) {
			foreach ($value as $k => $v) {
				if ($v != $this->blocks[$key][$k])
					return false;
			}
		}
		return true;
	}

	public function findNeighbors() {

		$brds = array();
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY + 1));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY - 1));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX - 1, $this->zeroY));
		array_push($brds, $this->makeChange($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX + 1, $this->zeroY));

		return $brds;
	}

	private function makeChange($board, $x1, $y1, $x2, $y2) {
		if ($x2 > -1 && $x2 < $this->getDimencion() && $y2 > -1 && $y2 < $this->getDimencion()) {
			$t = $board[$x2][$y2];
			$board[$x2][$y2] = $board[$x1][$y1];
			$board[$x1][$y1] = $t;

			return new Board($board, $this->final);
		} else
			return null;
	}
}

?>
