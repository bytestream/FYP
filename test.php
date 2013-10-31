<?php

	require_once('TwitterEngine.php');

	$TwitterEngine = new TwitterEngine(
		array(
			'consumer_key' 		=> '6jwZPWYF2msLozMx9zvsQ',
            'consumer_secret' 	=> 'Q9Y53UCMc4nnlB9ReTiX8LDCZZ7BODiPi8gZkAJe0s',
            'token' 			=> '621068140-dF8VbcrPc4elrobv9dICkNMAPLQtNtPt8Bt2d89v',
            'secret' 			=> 'ThLnRIyNW15ISf2PhWIwCrOnZuOUgMW6e04h14JtLgxug',
			'curl_timeout' 		=> 30
		)
	);

	// Enable verbose output
	$TwitterEngine->setVerbose(true);

	var_dump($TwitterEngine->getUserFollowers(160926944));
	var_dump($TwitterEngine->getUserTimeline(160926944));
	var_dump($TwitterEngine->getErrors());
	var_dump($TwitterEngine->getVerboseOutput());

?>