<?php

class Board {

	private $blocks;
	private $zeroX;
	private $zeroY;
	private $heuristic;
	private $g;
	private $final;
	private $zero;

	public function __construct($array, $final) {
		$this->zero = 0 ;
		$this->blocks = $array;
		$this->heuristic = $this->zero;
		$this->g = $this->zero;
		$this->final = $final;

		$this->fillBoard($array, $final);
	}

	private function fillBoard($array, $final) {
		foreach ($array as $key => $value) {
			foreach ($value as $k => $v) {
				if ($v != ($this->final[$key][$k]))
					$this->heuristic += $this->countABS($final, $v, 'x', $key) + $this->countABS($final, $v, 'y', $k);
				if ($v == 0) {
					$this->zeroX = (int)$key;
					$this->zeroY = (int)$k;
				}
			}
			if ($GLOBALS["HEURISTIC_FUNC_FLAG"] == "-lc")
				$this->heuristic += $this->linearConflict($value, $key);
		}
	}

	private function countABS($final, $value, $axis, $key) {
		return abs($this->getXY($final, $value, $axis) - $key);
	}

	private function linearConflict($row, $numRow) {
		$n = 0;
		foreach ($row as $key => $value)
			for ($k = $key; $k < count($this->blocks); $k++)
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

	private function getXY($final, $value, $axis) {
		foreach ($final as $key => $item) {
			foreach ($item as $k => $val) {
				if ($val == $value && $axis == 'x')
					return $key;
				if ($val == $value && $axis == 'y')
					return $k;
			}
		}
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

		if (count($o->blocks) != count($this->blocks))
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
