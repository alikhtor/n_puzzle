<?php

require "Neighbours.php";

class Board extends Neighbours {

	private $blocks;
	private $final;
	private $zeroX;
	private $zeroY;
	private $heuristic;
	private $g;
	private $zero;

	public function __construct($array, $final) {
		$this->zero = 0 ;
		$this->blocks = $array;
		$this->heuristic = $this->zero;
		$this->g = $this->zero;
		$this->final = $final;

		$this->fillBoard($array, $final);
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

	public function onPlace() {
		return $this->heuristic != 0;
	}

	public function equals(Board $o) {
		if ($this == $o) {
			return true;
		} else if (count($o->blocks) != count($this->blocks)) {
			return false;
		} else {
			foreach ($o as $key => $value) {
				foreach ($value as $k => $v) {
					if ($v != $this->blocks[$key][$k])
						return false;
				}
			}
			return true;
		}
	}
}

?>
