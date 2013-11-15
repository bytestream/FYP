<?php

	/**
	 * Dissect the required data from the JSON decoded API response
	 * @param  array $array JSON decoded API response
	 * @return mixed        array on success, FALSE on error
	 */
	function getUserDetails($array = null) {
		if (is_array($array)) {
			if (isset($array['screen_name']) && isset($array['id_str']) &&
				isset($array['location']) && isset($array['followers_count']) &&
				isset($array['friends_count']) && isset($array['created_at']) &&
				isset($array['description'])
			) {

				// Format and return the data
				return array(
					"user_id"			=> (int) $array['id_str'],
					"screen_name" 		=> $array['screen_name'],
					"created_at"		=> convertToDateTime($array['created_at']),
					"description"		=> $array['description'],
					"location"			=> $array['location'],
					"friends_count"		=> (int) $array['friends_count'],
					"followers_count"   => (int) $array['followers_count']
				);
			} else {

				// Missing data
				return false;
			}
		} else {

			// Invalid data type passed
			return false;
		}
	}

	/**
	 * Dissect the required data from the JSON decoded API response
	 * @param  array $array JSON decoded API response
	 * @return mixed        array on success, FALSE on error
	 */
	function getTweetData($array = null) {
		if (is_array($array)) {
			if (isset($array['text']) && isset($array['created_at']) && 
				isset($array['retweeted']) && isset($array['retweet_count']) &&
				isset($array['user']['id_str']) && isset($array['source'])
			) {

				$source = $array['source'];
				if (preg_match("/<.*?>(.*?)<\/.*?>/", $array['source'], $match)) {
					$source = $match[1];
				}

				// Format and return the data
				// tweet_id, user_id, tweet, creation_date, source, retweeted, retweet_count
				return array(
					"tweet_id"		=> (int) $array['id_str'],
					"user_id"	 	=> (int) $array['user']['id_str'],
					"tweet"		 	=> $array['text'],
					"created_at" 	=> convertToDateTime($array['created_at']),
					"source"		=> $source,
					"retweeted"  	=> (int) $array['retweeted'],
					"retweet_count"	=> (int) $array['retweet_count']
				);
			} else {

				// Missing data
				return false;
			}
		} else {

			// Invalid data type passed
			return false;
		}
	}

	/**
	 * Convert a date from the API to the MySQL data type
	 * @param  string $date Date from the API
	 * @return string       MySQL formated DATETIME string
	 */
	function convertToDateTime($date) {
		$timestamp = strtotime((new DateTime($date))->format('c'));

		return date( 'Y-m-d H:i:s', $timestamp );
	}

?>