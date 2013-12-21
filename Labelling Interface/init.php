<?php

	// Start the session
   	session_start();

   	// Start output buffering
   	ob_start();

	// Include the required files
	require_once('./includes/DatabaseEngine.php');
	require_once('./includes/functions.php');

	// Initialise the database
	$DatabaseEngine = new DatabaseEngine(
		'mysql:host=87.76.31.107;dbname=findchri_twitter',
		'findchri_twitter',
		'*-H4^1b4*$P9|[h'
	);

?>