<?php

	// Initialise
	require_once("init.php");

	// Store a list of the users we've added to the DB
	$users_to_delete = array();
	
	/**
	 * Kill the app and exit cleanly
	 * @param  object $TwitterEngine
	 * @param  string $message Error to print to the client
	 * @param  boolean $fatal Whether to terminate execution or not
	 * @return void            App will die.
	 */
	function ohNoes($TwitterEngine = null, $message = "", $fatal = false) {
		if (isset($TwitterEngine)) {
			// Output errors
			echo "<pre>";
			$n = $TwitterEngine->getVerboseOutput();
			var_dump($TwitterEngine->getErrors());
			var_dump($TwitterEngine->getVerboseOutput(count($n) - 1));
		}

		if ($fatal === TRUE) {
			die($message);
		}
	}

	// Enable verbose output
	$TwitterEngine->setVerbose(true);

	// Get data from the queue
	$queue = $DatabaseEngine->arrayquery(
		"SELECT `user_id` FROM `queue` ORDER BY `user_id` ASC LIMIT 300"
	);
	// Now immediately delete the data
    $ids = array_map(function($a) {return $a['user_id'];}, $queue);
    $DatabaseEngine->query(
        "DELETE FROM `queue` WHERE `user_id` IN ('" . implode("','", $ids) . "');"
    );


	// Check we have some data
	if ($queue !== FALSE) {
		// Loop over each item
		while (list($key, $user) = each($queue)) { 
			// Get the user timeline
			$data = $TwitterEngine->getUserTimeline($user['user_id'], $TwitterEngine::APP_AUTH, 200);

			if ($data !== FALSE && isset($data[0])) {
				// Get user details
				if (($user = getUserDetails($data[0]['user'])) !== FALSE) {
					// Add the user to the DB
					$DatabaseEngine->query(
						"INSERT IGNORE INTO `users` (user_id, screen_name, description, 
							creation_date, location, total_followers, total_friends) 
							VALUES (:user_id, :screen_name, :description, :created_at, :location, :followers_count, :friends_count);",
						$user
					);

					// Add user to the delete list
					$users_to_delete[] = $user['user_id'];
				} else {
					ohNoes($TwitterEngine, "ERROR GETTING USER DETAILS");
				}

				// Loop over each tweet for the user
				$insert_values = $question_marks = array();
				foreach ($data as $tweet) {
					if (($tweet_data = getTweetData($tweet)) !== FALSE) {
						// Create the SQL
						$question_marks[] = '(?, ?, ?, ?, ?, ?, ?)';
						$insert_values = array_merge($insert_values, array_values($tweet_data));
					} else {
						// Error getting user tweets
						ohNoes($TwitterEngine, "ERROR GETTING TWEET");
					}
				}

				// Send all the tweets to the DB
				$sql = "INSERT IGNORE INTO `tweets` (tweet_id, user_id, tweet, creation_date, source, retweeted, retweet_count) 
						VALUES " . implode(',', $question_marks) . ";";
				$DatabaseEngine->query($sql, $insert_values);
			} else {
				// Check if we were rate limited
				if (false !== stripos(implode("\n", $TwitterEngine->getErrors()), "rate limit")) {
					ohNoes($TwitterEngine, "Rate limit exceeded.", true);
				} else {
					// Skip the user and move on
					continue;
				}
			}
		}
	} else {
		die("Error collecting data from the queue.");
	}

?>