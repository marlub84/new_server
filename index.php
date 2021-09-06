<?php

include_once ('./library/session.php');
/*
if (!isset($_COOKIE['time_expire'])) {
	session_start();
	$_SESSION['session_active'] = session_id();
	setcookie('time_expire', $_SESSION['session_active'], time() + 10);
	echo 'time start, ' + time();
	echo "";
	echo 'time end, ' + time()+10;
} else {
	echo 'else condition';
	session_start();
}
*/

$mysession = new mySession();
$mysession->startSession();
// test set param
$mysession->prepareParam('myparam', 'myval');
$mysession->setSessParam();

// test function 
//echo session_decode($_SESSION);

// insert html header include doctype
include ("./head.php");
?>
<body>
	<div class="main_content">
		<div class="toprow">
			<p> Top row</p>
		</div>
		<div class="row">
			<div id="menu_panel" class="column side">
				<a href="index.php">Home</a>
				<p> Menu panel </p>
				<?php
				// left side panel contain the menu navigation
				// if display is too small change to roller menu 
				
				?>
			</div>
			<div class="column middle">
				<?php
					echo 'session - ';
					print_r($_SESSION);
					echo '<br>';
					print_r($_COOKIE);
					echo '<br>';
					
				?>
			</div>
			<div class="column side">
				<p> right </p>
			</div>
			
		</div>
		
	</div>

</body>

// end html file from head
</html>
