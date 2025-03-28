<?php

	$server = 'localhost';
	$user = 'root';
	$pass = '';
	$database = 'db_sql';

	$conn = mysqli_connect($server, $user, $pass, $database);

	if(!$conn)
		die("Error while connecting...!").mysqli_connect_error($conn);
	else{
		mysqli_query($conn, "SET NAMES 'utf8'");
		// echo "Connected successfully...!";
	}
		
 ?>