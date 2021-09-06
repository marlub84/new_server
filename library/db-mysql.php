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

/* SQL query to create database */
function createDB ($db_conn, $dbname ) {
	$sql_query = "CREATE DATABASE raspberry;";
	if ($db_conn->query($sql_query)) {
		printf ("Database 'raspberry' was created...");
	} else {
		printf ("Someting was wrong...");
	}
	
}

/* SQL query to create table session */
function createTbl ($db_conn, $dbname ) {
	$sql_query = "CREATE TABLE session (id int auto_increment PRIMARY KEY, sess_id char(128) NULL);";
	if ($db_conn->query($sql_query)) {
		printf ("Table 'session' was created...");
	} else {
		printf ("Someting was wrong...");
	}
	
}

$conn = openDB();

createTbl ($conn, 'raspberry');

if ($conn) {
	$conn->close();
}
	
?>

