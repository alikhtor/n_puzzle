<?php

ini_set('memory_limit', '-1');

require_once "Board.php";
require_once "Solver.php";

function readFromFile($filename){
	$file = file($filename);
	if (!$file) error_handler(2);

	$map = [];
	$stringMap = [];
	$map_size = 0;
	foreach ($file as $string) {
		if ($string[0] == "#") {
			continue;
		}
		$string = trim(preg_replace('/\#.*/i', '', preg_replace('/\s+/', ' ', trim($string))));
		$data = explode(' ', $string);

		if (count($data)) {
			$digitsCount = count($data);
		}
		if ($digitsCount == 1 && $data[0]) {
			$map_size = $data[0];
			continue;
		}

		if ($digitsCount != $map_size){
			error_handler(3);
		}
		$map[] = $data;
		$stringMap[] = $string;
	}

	return [
		"map_size"   => $map_size,
		"map" 		 => $map,
		"string_map" => $stringMap
	];
}

function solveExist($map) {
	$zeroCount = 0;
	foreach ($map as $row => $digit) {
		if (array_search("0", $digit) !== false)
	    	$zeroCount += (($row + 1) % 2) ? $row + 1 : $row;
	}
	$wayLength = 0;
	$direct = [];
	foreach ($map as $i => $row) {
		foreach ($row as $j => $elem) {
            $tmp = 0;
            foreach ($direct as $value) {
                if ($map[$i][$j] > $value) {
                    $tmp += 1;
                }
            }
            $direct[] = $map[$i][$j];
            $wayLength += $map[$i][$j] - $tmp + $zeroCount;
            $tmp = 0;
		}
	}
	if (($wayLength % 2) != 0) {
		error_handler(4);
	}
}

function	findSmaller($map_size, &$map) {
	$smallerNum = $map_size;
	$cord = [0, 0];
	foreach ($map as $key1 => $row) {
		foreach ($row as $key2 => $el) {
			if ($el < $smallerNum && $el != 0 ) {
				$cord[0] = $key1;
				$cord[1] = $key2;
				$smallerNum = $el;
			}
		}
	}
	unset($map[$cord[0]][$cord[1]]);
	if ($smallerNum == $map_size)
		return 0;

	return $smallerNum;
}

function	findWay($map_size, $map) {
	$result = [];

	$sideCnt = 1;
	$gpCnt = 1;
	$pos = -1;

	$side = $map_size;
	$totalCount = $map_size * $map_size;
	for ($i = 0; $i < $totalCount;) {
		for ($j = 0; $side > $j; $j++) {
			if ($sideCnt == 1) {
				$pos++;
				$result[$pos] = findSmaller($totalCount, $map);
			} elseif($sideCnt == 2) {
				$pos += $map_size;
				$result[$pos] = findSmaller($totalCount, $map);
			} elseif($sideCnt == 3) {
				$pos--;
				$result[$pos] = findSmaller($totalCount, $map);
			} elseif($sideCnt == 4) {
				$pos -= $map_size;
				$result[$pos] = findSmaller($totalCount, $map);
			}
			$i++;
		}

		if ($gpCnt == 3 || ($side == $map_size && $gpCnt == 1)) {
			$gpCnt = 1;
			$side--;
		}

		$sideCnt++;
		$gpCnt++;

		if ($sideCnt == 5)
			$sideCnt = 1;
		if ($side == -1)
			break;
	}
	ksort($result);

	return $result;
}

function validate($argc){
	if ($argc !== 3) {
		error_handler(1);
	}
}

function newWay($map_size, $way){
	for ($i = 0; $i < count($way); $i++) {
	    if (empty($new[$i / $map_size])) {
	        $new[$i / $map_size] = array();
	    }
	    array_push($new[$i / $map_size], $way[$i]);
	}

	return $new;
}

function    error_handler($num = 0) {
  $msg = "
 		to execute this program use:
		php npuzzle.php -md map.txt
		Heuristic available modes:
		-lc (linear conflict)
		-md (manhattan distanse)
		-b (both of them)";

	if ($num == 1){
        error_log("\nWrong count of argument");
	} elseif ($num == 2){
        error_log("\nFile does't exist");
	} elseif ($num == 3){
        error_log("\nInvalid map");
    	exit(1);
	} elseif ($num == 4){
		error_log("This puzzle is unsolvable!");
		exit(1);
	}
    error_log($msg);
    exit(1);
}

function main($argc, $argv){
	validate($argc);
	$data = readFromFile($argv[2]);
	solveExist($data['map']);
	$HEURISTIC_FUNC_FLAG = $argv[1];
	$newWay = newWay($data["map_size"], findWay($data["map_size"], $data["map"]));
	// error_log(print_r($newWay ,1));
    $board = new Board($data['map'], $newWay);

    $solution = new Solver($board);
    $test = $solution->solve();
		// error_log(print_r($test ,1));
}

main($argc, $argv);
?>
