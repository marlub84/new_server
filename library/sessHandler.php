<?php
/*
 * Interface to handler session 
 * storage : 
 * - file 
 * - db mysql - TODO
 */
 
 include ('./library/db-mysql.php');
 
 class objSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface {
	 
	 // methods
	 public function validateId($key) {
		 
	 }
	 public function updateTimestamp($key, $val) {
		 
	 }
	 
	 public function create_sid() {
		//echo 'create id';
		 $tab = 'abcdefghijklmnoprstuvwxyz1234567890+ABCDEFGHIJKLMNOPRSTUVWXYZ.';
		$range = strlen($tab) - 1;
		$string = '';
		
		for ($a = 0; $a <= 32; $a++) {
			$ret = rand(0, $range);
			$string .= $tab[$ret];
		}
		 return $string;//parent::create_sid();
	 }
	 
	 public function open($sessPath, $sessName) {

		 return true;
	 }
	 
	 public function close() {
		 
		 global $_SESSION;
		 
		 return true;
	 }
	 
	 public function read($id) {
		 // use global session variable 
		 global $_SESSION;

		 // check if id exist in DB  
		 if (!$ret = sessCheckRow($id)) {
			// insert new session
			$_SESSION['sess_id'] = $id;
			$_SESSION['expire_time'] = time();
			$_SESSION['user'] = 'guest';
			$_SESSION['old_id'] = '';
			sessInRow($_SESSION);
			$data = session_encode();
		 }else {
			 sessReadData($id, $_SESSION);
			 $data = session_encode();
		 }
		 
		 if (!isset($data)) $data = '';
		 return $data;
	 }
	 
	 public function write($id, $data) {
		 // use global variable
		 global $_SESSION;
		 session_decode($data);
		 // check if session id dosn`t in database
		 $ret = sessCheckRow($id);
		 if ($ret) {
			 // update expire time
			 sessUpTm($_SESSION);
		 }
		 if (isset($_SESSION['old_id'])) {
			 sessUpId($_SESSION);
			 unset($_SESSION['old_id']);
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
