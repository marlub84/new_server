<?php

function openDB () {
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		
	$host = 'localhost';
	$username = 'raspberry';
	$passwd = 'raspberry';
	$dbname = 'raspberry';
	try{
		$conn = new mysqli($host, $username, $passwd, $dbname);
		
		// set the desired charset after estabishing a connection 
		$conn->set_charset('utf8mb4');
		//printf ("Success open DB... %s\n", $conn->host_info);
		return $conn;
		
	} catch (Exception $excep) {
		echo 'Exception ', $excep->getMessage(), "\n";
	}

	return 0;
	 
 }


/**
 * Create database 'raspberry' if not exist 
 * if exist drop database and create new 
 * create new table 'session' if exist drop and create new
 * 
 * Table 'session' :
 * ----------------------------------------------------------------------
 * | id | sess_id | user (logged) | expire_time | old_id |
 * ------------------------------------------------------
 */

/* SQL query to create database 
 * Database must create root user 
 * */
//function createDB ($db_conn, $dbname ) {
	//$sql_query = "CREATE DATABASE raspberry;";
	//if ($db_conn->query($sql_query)) {
		//printf ("Database 'raspberry' was created...");
	//} else {
		//printf ("Someting was wrong...");
	//}
	
//}

/* SQL query to create table session */
function createTbl () {
	$conn = openDB();
	$sql_query = "CREATE TABLE session (id int auto_increment PRIMARY KEY, sess_id TINYTEXT, user TINYTEXT, expire_time INT,
										old_id TINYTEXT);";
	if ($conn->query($sql_query)) {
		printf ("Table 'session' was created...");
	} else {
		printf ("Someting was wrong...");
	}
	$conn->close();
}

/**
 * Use this function to insert session_id and user to database
 * 
 * @param mixed $sess_var 	The session variable 
 * @return bool 			true if insert was success
 * */ 
function sessInRow ($sess_var) {
	$conn = openDB();
	if ($sql_query = $conn->prepare ("INSERT INTO session (sess_id, user, expire_time, old_id) VALUE (?, ?, ?, ?)")) {
		$sql_query->bind_param('ssis', $sess_var['sess_id'], $sess_var['user'], $sess_var['expire_time'], $sess_var['old_id']);
		if (!$sql_query->execute()) {
			// insert dat was`t successfull
			$conn->close();
			return false;
		} else {
			$sql_query->close;
			$conn->close();
			return true;
		}
	}
}

/**
 * Use this function to delete session_id from database
 * 
 * @param mixed $sess_var	Session variable
 * @return: int 			Number of deleted row(s)
 * */
function sessRmRow ($sess_var) {
	// remove the row from DB
	$conn = openDB();
	if ($sql_del = $conn->prepare("DELETE FROM session WHERE old_id = ?")) {
		$sql_del->bind_param('s', $sess_var['old_id']);
		$sql_del->execute();
		$sql_query->close;
		$conn->close();
		// controll how meny row was delete
		return $sql_del->affected_row;
	}

	$conn->close();
}

/** 
 * Use this function to delete older session
 * 
 * @param no parametrs need 
 * @return int 				Number of deleted row(s)
 * */
function sessRmOld () {
	$conn = openDB();
	$current_time = time() - 200;
	if ($sql_query = $conn->prepare("DELETE FROM session WHERE expire_time <= ?")) {
		$sql_query->bind_param('i', $current_time);
		$sql_query->execute();
		$sql_query->close();
		$conn->close();
		return $sql_query->affected_rows;
	}
	$conn->close();
}

/**
 * Use this function to check if session id exist in database
 * 
 * @param mixed $sess_var 	The session parameters
 * @return int 				return id from session table 
 * */
function sessCheckRow ($sess_id){
	// check if exist in DB sess_id 
	// if exist more than one use the array 
	$conn = openDB();
	$result;
	if ($sql_query = $conn->prepare("SELECT id FROM session WHERE sess_id = ?")) {
		$sql_query->bind_param('s', $sess_id);
		if (!$sql_query->execute()) {
			// bad query ?
		}
		$sql_query->bind_result($id_sel);
		$sql_query->store_result();
		
		if ($sql_query->num_rows == 1) {
			$sql_query->fetch();
			$result = $id_sel;
			$sql_query->close;
			$conn->close();
		} elseif ($sql_query->num_rows >= 1) {
			$inc = 0;
			while ($sql_query->fetch()) {
				$result[$inc++] = $id_sel;
			}
			$sql_query->close;
			$conn->close();
		} else {
			// row not exist
			//$sql_query->close;
			$conn->close();
			$result = 0;
		}	
	} else {
		$sql_query->close;
		$conn->close();
		$result = 0;
	}
	return $result;
}

/**
 * Function to read session data 
 * 
 * @param int $id		The id in session table 
 * @param mixed &$data 	Reference to data 
 * @
 * */
function sessReadData($id, &$data) {
	$conn = openDB();
	if ($sql_query = $conn->prepare("SELECT sess_id, user, expire_time, old_id FROM session WHERE id = ?")) {
		$sql_query->bind_param('s', $id);
		$sql_query->execute();
		$sql_query->bind_result($sess_id, $username, $sess_expire, $sess_old);
		$sql_query->store_result();
		if ($sql_query->num_rows) {
			$sql_query->fetch();
			$data['sess_id'] = $sess_id;
			$data['username'] = $username;
			$data['expite_time'] = $sess_expire;
			$data['old_id'] = $sess_old;
			$sql_query->close();
		} else {
			return false;
		}
		
		$conn->close();
		return true;
	}
	$conn->close();
	return false;
}
/*
$conn = openDB();

createTbl()	;
//sessCheckRow("mysession");

if ($conn) {
	$conn->close();
}
*/
?>

