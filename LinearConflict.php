<?php

class LinearConflict {

	public function linearConflict($row, $numRow) {
		$n = 0;
		foreach ($row as $key => $value) {
			for ($k = $key; $k < count($this->blocks); $k++) {
				if (!$this->checkOrder($value, $row[$k], $key, $k, $numRow)) {
					$n += 1;
				}
			}
		}
		return $n;
	}

	private function checkOrder($x1, $x2, $k1, $k2, $i) {
		if (in_array($x1, $this->final[$i])) {
			$fk1 = array_search($x1, $this->final[$i]);
			if (in_array($x2, $this->final[$i])) {
				$fk2 = array_search($x2, $this->final[$i]);
				if ($fk1 > $fk2 && $k1 < $k2) {
					return 0;
				} else if ($fk2 > $fk1 && $k2 < $k1) {
					return 0;
				}
			}
		}
		return 1;
	}

}

?>