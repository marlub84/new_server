<?php
/*
 * Interface to handler session 
 * storage : 
 * - file 
 * - db mysql - TODO
 */
 
 class objSessionHandler extends SessionHandler {
	 
	 // methods
	 public function create_sid() {
		 print 'session';
		 return parent::create_sid();
	 }
	 
	 public function open($sessPath, $sessName) {
		 print "Open \n ";
		 echo '<br>';
		 print "sessPath $sessPath \n";
		 echo '<br>';
		 print "sessName $sessName \n";
		 echo '<br>';
		 
		 return true;
	 }
	 
	 public function close() {
		 print "Close \n";
		 echo '<br>';
		 
		 return true;
	 }
	 
	 public function read($id) {
		 print "Read id $id \n";
		 echo '<br>';
		 
		 return '';
	 }
	 
	 public function write($id, $data) {
		 print "Write :\n";
		 echo '<br>';
		 print "id $id \n";
		 echo '<br>';
		 print "data $data \n";
		 echo '<br>';
		 
		 return true;
	 }
	 
	 public function destroy($id) {
		 print "destroy id $id \n";
		 echo '<br>';
		 
		 return true;
	 }
	 
	 public function gc($max_lifetime) {
		 print "GC lifetime $max_lifetime ";
		 echo '<br>';
	 }
	 
 } // end class

?>

