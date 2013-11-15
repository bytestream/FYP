<?php

	// Disable timeout
	set_time_limit(0);

	// Disable memory limit
	ini_set('memory_limit', '-1');

	// Include the required files
	require_once('TwitterEngine.php');
	require_once('DatabaseEngine.php');
	require_once('functions.php');

	// Initialise the Twitter engine
	$TwitterEngine = new TwitterEngine(
		array(
			'consumer_key' 		=> '6jwZPWYF2msLozMx9zvsQ',
            'consumer_secret' 	=> 'Q9Y53UCMc4nnlB9ReTiX8LDCZZ7BODiPi8gZkAJe0s',
			'curl_timeout' 		=> 30
		)
	);

	// Initialise the database
	$DatabaseEngine = new DatabaseEngine(
		'mysql:host=87.76.31.107;dbname=findchri_twitter',
		'findchri_twitter',
		'*-H4^1b4*$P9|[h'
	);

?>