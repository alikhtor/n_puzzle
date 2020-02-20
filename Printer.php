<?php
    
class Printer {

    public function print_result($board) {
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