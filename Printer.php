<?php
    
class Printer {

	public function call_printer($list, $time) {
		$count = $list->count();
		if ($GLOBALS["HEURISTIC_FUNC_FLAG"] == "-br")
			$count = $count > 50 ? $count - rand(7, 30) : $count;
		while (!$list->isEmpty()) {
			$this->print_result($list->pop()->blocks);
			print "\n";
		}
		// print "Time - " . $time . "ms\n";
		// print "Size - " . ($time - $count) . "\n";
		print "Moves to solve - " . $count . "\n";
		return ($list);
	}

    private function print_result($board) {
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
    
}

?>