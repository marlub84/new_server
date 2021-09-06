<?php
/* mySession class to manage session 
 * Session class use the cookie 
 */
include ('./library/sessHandler.php');

class mySession {
	
	/* session parametrs 
	 */
	// session id 
	private $sessid = 'empty';
	private $sess_param = array();
	private $sess_param_ready = false;
	
	private $gc_periode = 420;
	private $gc_time = './tmp/last_session_gc';
	
	// hash-ed parameters 
	private $hsessid = 'empty';
	private $hash_method = 'sha256';
	private $hash_key = '';
	private $key_use = 0;
	
	// expire time is set to 1 hour
	private $expire = 0;
	
	private function myHash($to_hash) {
			$ctx = hash_init($this->hash_method, $this->key_use, $this->hash_key);
			hash_update($ctx, $to_hash);
			$h_ctx = hash_final($ctx);
			return $h_ctx;
	}
	
	// destructor 
	function __destruct() {
		
	}
	
	// constructor
	function __construct () {
		
	}
	/* Function to prepare session variable 
	 * TODO :
	 * - controll valide name and value 
	 */
	public function prepareParam($param_name, $param_value) {
		// check if name or value is not empty 
		if (!empty($param_name) || !empty(param_value)) {
			$this->sess_param[$param_name] = $param_value;
			$this->sess_param_ready = true;
		}else {
			$this->sess_param_ready = false;
			echo 'Parametr or value not set!';
		}
	}
	
	/* Function called after prepare session variable
	 * It is use to set $_SESSION variable
	 */
	public function setSessParam() {
		// use $_SESSION globally
		global $_SESSION;
		//check if parametrs are ready
		if ($this->sess_param_ready) {
			$this->setSessionVar($this->sess_param);
			//foreach ($this->sess_param as $key => $val) {
				//$_SESSION[$key] = $val;
			//}
			unset ($this->sess_param);
			$this->sess_param_ready = false;
		}
	}
		
	/* Function to set importatn session variable 
	 * 
	 */
	private function setSessionVar($param, $value = 0) {
		global $_SESSION;
		// make shure that start session was begin
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		if (is_array($param)) {
			// set session variable
			foreach($param as $key => $val) {
			$_SESSION[$key] = $val;
			}
		
		}else {
			$_SESSION[$param] = $value;
		}
	}
	
	/* Function to change value of sessiono parameters
	 * 
	 */
	public function changeSessionParam($name, $val) {
			// use global session
			global $_SESSION;
			if (array_key_exists($name, $_SESSION)) {
				$_SESSION[$name] = $val;
			}
	}
	
	public function setMyCookies($name, $val, $cookie_expire = 0) {
		
		setcookie($name, $val, $cookie_expire - 3600);
		// set cookies
		if (!(setcookie($name, $val, $cookie_expire + 3600))) {
			echo 'cookies not set';
		}
		
	}
	/* Function to regenerate session id when :
	 * Certain period is passed
	 */
	
	private function myRegenerateId() {
		// make shure the start session was begin
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}

		$newid = session_create_id();
		
		// check if new id was created
		if (!$newid) {
			echo 'Error creating new id';
			return;
		}
		
		// clouse current session - save data
		session_commit();
		
		// set new session id
		session_id($newid);	
		ini_set('session.use_strict_mode', 0);
		// and start with new session id
		session_start();
		ini_set('session.use_strict_mode', 1);
		
	}
	
	
	/* Function create new session 
	 * and set cookies 
	 */
	public function startSession() {
		
		$handler = new objSessionHandler();
		session_set_save_handler ($handler, true);
		
		
		ini_set('session.use_strict_mode', 1);
		session_start();
		global $_SESSION;
		/* 
		 * calling GC for every request is waste of resources
		 */
		//touch($this->gc_time);
		if (file_exists($this->gc_time)) {

			if (filemtime($this->gc_time) < time() - $this->gc_periode) {
				session_gc();
				touch($this->gc_time);
			} else {
				
				//touch($this->gc_time);
			}
		}
		$this->sessid = session_id();

		//hash session id
		$this->hsessid = $this->myHash($this->sessid);
		
		// set expire time 
		$this->expire = time();
		
		// check if cookies and session 'sessid' variable are set 
		if ((!isset($_COOKIE['mysessid'])) || !(isset($_SESSION['sessid']))) {
			//prepare session parameters
			$this->prepareParam('sessid', $this->hsessid);
			$this->prepareParam('time_expire', $this->expire);
			
			// set session variable
			$this->setSessParam();
			//$this->setSessionVar($this->sess_param);
			
			//$this->setSessionVar('sessid', $this->hsessid);
			//$this->setSessionVar('time_expire', $this->expire);
			// set cookies 			
			$this->setMyCookies('mysessid', $this->hsessid, $this->expire);
			
		}else {
			// cookies is set
			// check if sessid and cookies is the same 
			if ((isset($_SESSION['sessid'])) && ($_SESSION['sessid'] == $_COOKIE['mysessid'])) {
				
				// check expire time 
				// if expire time is out create new session id 
				if (!empty($_SESSION['time_expire']) && $_SESSION['time_expire'] < time() - 300) {

					// set new session id
					$this->myRegenerateId();
					
					// set session id 
					$this->sessid = session_id();
					//hash session id
					$this->hsessid = $this->myHash($this->sessid);
					
					// set expire time 
					$this->expire = time();
					
					//prepare session parameters
					$this->prepareParam('sessid', $this->hsessid);
					$this->prepareParam('time_expire', $this->expire);
					
					// set session variable
					$this->setSessParam();
					//$this->setSessionVar();
					
					// set new cookies 
					$this->setMyCookies('mysessid', $this->hsessid, $this->expire);
				}
				// id is ok and expire time is ok
				// what next ?
			} else {
				// sessid and cookies are not equal
				// unexpected situation
				session_destroy();
				// delete cookies
				setcookie('mysessid', "", time() - 360);
				// and start new session
				session_start();
				
			}
			
						 
		} // end cookies set
				 
	 } // end startSession

	
	 /* function delete current session
	  * */
	 public function delSession() {
		 
		 session_destroy();
	 }
	
}


?>
