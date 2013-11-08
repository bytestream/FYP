<?php

	// Disable timeout
	set_time_limit(0);

	// Set the file name
	$filename = "test.txt";

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
	// $TwitterEngine->setVerbose(true);
	
	$start_time = microtime(true);
	$followers = $TwitterEngine->getUserFollowers(160926944);
	foreach ($followers['ids'] as $key => $follower) {
		// Get the users tweets
		$res = $TwitterEngine->getUserTimeline($follower);

		// Rate limited exceeded or other API error
		if ($res === false) {
			var_dump($TwitterEngine->getErrors()); 
			break;
		} else {
			// Check we have some good data from the API
			// Users with no tweets are ignored, similar for protected users
			if (isset($res[0])) {
				// Write the username
				file_put_contents($filename, "\n\nUsername: {$res[0]['user']['name']}\n", FILE_APPEND); 
				// Write each tweet
				foreach ($res as $tweet) {
					file_put_contents($filename, "\t{$tweet['text']}\n", FILE_APPEND);
				}
			}
		}
	}
	$end_time = microtime(true);
	$total_time = substr(($end_time - $start_time), 0, 8);
	echo "Total running time " . date("H:i:s", $total_time) . " reaching a total of " . $key . " accounts."; 

?>