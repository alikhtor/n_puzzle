<?php
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
    
    public function dimension() {
		return count($this->blocks);
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
