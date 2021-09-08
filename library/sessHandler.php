<?php
/*
 * Interface to handler session 
 * storage : 
 * - file 
 * - db mysql - TODO
 */
 
 include ('./library/db-mysql.php');
 
 class objSessionHandler extends SessionHandler {
	 
	 // methods
	 public function create_sid() {
		 
		 return parent::create_sid();
	 }
	 
	 public function open($sessPath, $sessName) {
		 
		 return true;
	 }
	 
	 public function close() {
		 
		 global $_SESSION;
		 
		 // check if session id was changed
		 if (!empty($_SESSION['old_id'])) {
			sessRmRow($_SESSION);
			
		 }
		 //delete session from database older 5 min
		 sessRmOld();
		 return true;
	 }
	 
	 public function read($id) {
		 // use global session variable 
		 global $_SESSION;
		 
		 // check if id exist in DB  
		 $ret = sessCheckRow($id);
		 
		 // check if return more then one row - id from session 
		 if (is_array($ret)) {
			 //exist more then one session with the same id
			 // TODO what next action ?
		 } elseif ($ret == 0) {
			 // any row 
			 return '';
		 } else {
			 // one session id in DB
			 // ok read data from DB
			 if (sessReadData($ret, $_SESSION)) {
				 // data read successfully
			 }
		 }
		 
		 return '';
	 }
	 
	 public function write($id, $data) {
		 
		 // use global variable
		 global $_SESSION;
		 // check if session id dosn`t in database
		 $ret = sessCheckRow($id);
		 if (!$ret){
			 sessInRow($_SESSION);
		 }else {
			 //update data in DB
		 }
		 
		 return true;
	 }
	 
	 public function destroy($id) {

		 return true;
	 }
	 
	 public function gc($max_lifetime) {
		 
	 }
	 
 } // end class

?>

