<?php

require "Printer.php";

class Copy extends Printer {

	public function copy_list($new, $curr) {
		$curr->rewind();
		while ($curr->valid()) {
			$new->push($curr->current());
			$curr->next();
		}
		return $new;
    }
        
}

?>