<?php

	echo "<pre>";

	// Initialise
	require_once("init.php");

	// Enable verbose output
	$TwitterEngine->setVerbose(true);

	// Get the current trending topics for the UK - rate limit = 1
	if (($trending = $TwitterEngine->getTrendingTopics($TwitterEngine::UK_WOEID)) !== FALSE) {

		// Initialise array of users
		$users = array();

		// Loop over each trending topic (maximum of 10)
		foreach ($trending[0]['trends'] as $topic) {

			// Search Twitter for tweets under that topic
			$params = array(
				"q"			=> $topic['query'], // hash tag
				"count"		=> 100	// return upto 100 tweets
			);
			// Rate limit = 1 * 10 for each trending topic (180 max)
			if (($search = $TwitterEngine->search($params)) !== FALSE) {

				// Loop over the returned tweets (should be 100 of these)
				foreach ($search['statuses'] as $tweet) {

					// Add the user to the DB
					if (($user = getUserDetails($tweet['user'])) !== FALSE) {
						// Add the user to the DB
						$DatabaseEngine->query(
							"INSERT IGNORE INTO `users` (user_id, screen_name, description, 
								creation_date, location, total_followers, total_friends) 
								VALUES (:user_id, :screen_name, :description, :created_at, :location, :followers_count, :friends_count);",
							$user
						);
						// Add the user to the queue for the next program to pull their followers and tweets
						$DatabaseEngine->query(
							"INSERT IGNORE INTO `queue` (user_id) VALUES ( ? );",
							array($user['user_id'])
						);
						// Store an array of the users so we can lookup their followers
						$users[] = $user['user_id'];

						// Add the tweet to the DB
						if (($tweet_data = getTweetData($tweet)) !== FALSE) {
							$DatabaseEngine->query(
								"INSERT IGNORE INTO `tweets` (tweet_id, user_id, tweet, creation_date, source, retweeted, retweet_count)
								 VALUES (:tweet_id, :user_id, :tweet, :created_at, :source, :retweeted, :retweet_count);",
								 $tweet_data
							);
						} else {

							// Error getting user details from response
							$n = $TwitterEngine->getVerboseOutput();
							var_dump(getTweetData($tweet));
							var_dump($TwitterEngine->getErrors());
							var_dump($TwitterEngine->getVerboseOutput(count($n) - 1));
							die("ERROR GETTING TWEET DETAILS");
						}
					} else {

						// Error getting user details from response
						$n = $TwitterEngine->getVerboseOutput();
						var_dump(getUserDetails($tweet['user']));
						var_dump($TwitterEngine->getErrors());
						var_dump($TwitterEngine->getVerboseOutput(count($n) - 1));
						die("ERROR GETTING USER DETAILS");
					}
				}
			} else {

				// Error searching the API with the hash tag
				var_dump($TwitterEngine->getErrors());
				var_dump($TwitterEngine->getVerboseOutput());
				die("ERROR SEARCHING API");
			} 
		}

		// Loop over the array of users we collected from each trending topic (10 topics * 100 tweets per topic)
		// Note we can only do this 15 times as per the UserFollowers() API
		foreach ($users as $user) {

			// Lookup the given users follower
			if (($followers = $TwitterEngine->getUserFollowers($user)) != FALSE) {

				// Add all the followers to the DB (maximum of 5000 per user, thus potential for 5000 * 15 = 75,000)
				$followers = $followers['ids'];
				$DatabaseEngine->query(
					"INSERT IGNORE INTO `queue` (user_id) 
					 VALUES " . rtrim(str_repeat("( ? ), ", count($followers)), ', ') . ";",
					$followers
				);
			} else {

				// Error getting followers
				var_dump($TwitterEngine->getErrors());
				var_dump($TwitterEngine->getVerboseOutput());
				die("FAILED AT FOLLOWERS");
			}
		}
	} else {

		// Error looking up trending topics
		var_dump($TwitterEngine->getErrors());
		var_dump($TwitterEngine->getVerboseOutput());
		die("ERROR LOOKING FOR TRENDING TOPICS");
	}

?>