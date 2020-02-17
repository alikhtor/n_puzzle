<?php
/**
 * Created by PhpStorm.
 * User: dmitry-mac
 * Date: 4/3/19
 * Time: 1:33 AM
 */

class Board {

	private $blocks;
	private $zeroX;
	private $zeroY;
	private $h;
	private $g;
	private $final;

	public function __construct($array, $final) {
		$tmp = $array;
		$this->blocks = $tmp;
		$this->h = 0;
		$this->g = 0;
		$this->final = $final;
		global $heruistic;

		foreach ($array as $key => $value) {
			foreach ($value as $k => $v) {
				if ($v != ($this->final[$key][$k])) {
					$this->h += abs($this->getXY($final, $v, 'x') - $key) + abs($this->getXY($final, $v, 'y') - $k);
				} if ($v == 0) {
					$this->zeroX = (int)$key;
					$this->zeroY = (int)$k;
				}
			}
			if ($heruistic == "-lc")
                $this->h += $this->LinearConflict($value, $key);
		}
		if ($heruistic == "-wp") {
		    $this->h = 0;
            foreach ($array as $key => $value) {
                foreach ($value as $k =>$v)
                    if ($v != $final[$key][$k])
                        $this->h++;
		    }
        }
	}

	private function LinearConflict($row, $numRow) {
		$n = 0;
		foreach ($row as $key => $value)
			for ($k = $key; $k < $this->dimension(); $k++)
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

	public function dimension() {
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

	public function isGoal() {
		return $this->h == 0;
	}

	public function equals(Board $o) {
		if ($this == $o)
			return true;

		if ($o->dimension() != $this->dimension())
			return false;

		foreach ($o as $key => $value) {
			foreach ($value as $k => $v) {
				if ($v != $this->blocks[$key][$k])
					return false;
			}
		}
		return true;
	}

	public function neighbors() {

		$brds = array();
		array_push($brds, $this->chng($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY + 1));
		array_push($brds, $this->chng($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX, $this->zeroY - 1));
		array_push($brds, $this->chng($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX - 1, $this->zeroY));
		array_push($brds, $this->chng($this->blocks, $this->zeroX, $this->zeroY, $this->zeroX + 1, $this->zeroY));

		return $brds;
	}

	private function chng($board, $x1, $y1, $x2, $y2) {
		if ($x2 > -1 && $x2 < $this->dimension() && $y2 > -1 && $y2 < $this->dimension()) {
			$t = $board[$x2][$y2];
			$board[$x2][$y2] = $board[$x1][$y1];
			$board[$x1][$y1] = $t;

			return new Board($board, $this->final);
		} else
			return null;
	}


	public function toString() {
		foreach ($this->blocks as $key => $value) {
			foreach ($value as $k => $v) {
				print "$v ";
			}
			print "\n";
		}
	}

}
