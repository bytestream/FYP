<?php

	// Disable timeout
	set_time_limit(0);

	require_once("init.php");

	// Enable verbose output
	$TwitterEngine->setVerbose(true);
	
	$followers = $TwitterEngine->getUserTimeline(160926944, $TwitterEngine::APP_AUTH);
	var_dump($followers);
	var_dump($TwitterEngine->getVerboseOutput());
	var_dump($TwitterEngine->getErrors());

?>