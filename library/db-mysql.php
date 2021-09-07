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
		printf ("Success... %s\n", $conn->host_info);
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
 * | id | session_id | user (logged) | 
 * -----------------------------------
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
function createTbl ($db_conn ) {
	$sql_query = "CREATE TABLE session (id int auto_increment PRIMARY KEY, sess_id char(64) NULL, user char(32);";
	if ($db_conn->query($sql_query)) {
		printf ("Table 'session' was created...");
	} else {
		printf ("Someting was wrong...");
	}
	
}

/**
 * Use this function to insert session_id and user to database
 * 
 * @param mysqli $conn 		The database connection
 * @param mixed $sess_var 	The session variable 
 * @return bool 			true if insert was success
 * */ 
function sessInRow ($conn, $sess_var) {
	sql_query = $conn->prepare ("INSERT INTO session (session_id, user) VALUE (?, ?)");
	sql_query->bind_param('ss', $sess_var['sess_id'], $sess_var['user']);
	if (!sql_query->execute()) {
		// insert dat was`t successfull
		return false;
	} else return true;
		
}

/**
 * Use this function to delete session_id from database
 * 
 * @param mysqli $conn 		The connection to database
 * @param mixed $sess_var	Session variable
 * @return: int 			Number of deleted row(s)
 * */
function sessRmRow ($conn, $sess_var) {
	// check if sess_id exist in DB 
	// remove the row from DB
	if ($sql_del = $conn->prepare("DELETE FROM session WHERE session_id = ?")) {
		$sql_del->bind_param('i', $sess_var['sess_id');
		$sql_del->execute();
		// controll how meny row was delete
		return $sql_del->affected_row;
	}
}

/**
 * Use this function to check if session id exist in database
 * 
 * @param mysqli $conn 				The connection to database
 * @param mixed $sess_var 			The session parameters
 * @param reference array(int) &$result 	Number of row 
 * */
function sessCheckRow ($conn, $sess_var, &$result){
	// check if exist in DB sess_id 
	// if exist more than one use the array 
	if ($sql_query = $conn->prepare("SELECT id IN raspberry.session WHERE session_id = ?")) {
		
		$sql_query->bind_param('s̈́', $sess_var['sess_id']);
		if (!sql_query->execute()) {
			// bad query ?
		}
		$sql_query->bind_result($id_sel);
		$sql_query->store_result();
		
		if ($sql_query->num_rows == 1) {
			$sql_query->fetch();
			$result = $id_sel;
			return true;
		} elseif ($sql_query->num_rows >= 1) {
			$inc = 0;
			while ($sql_query->fetch()) {
				$result[$inc++] = $id_sel;
			}
			return true;
		} else {
			// row not exist
			return false;
		}	
	} else {
		return false;
	}
	
}

/* check for working function */
						    
$conn = openDB();

createTbl ($conn, 'raspberry');

if ($conn) {
	$conn->close();
}
	
?>

