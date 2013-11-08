<?php

if (file_exists("./includes/tmhOAuth/tmhOAuth.php")) {

	// Include the OAuth library
	require_once("./includes/tmhOAuth/tmhOAuth.php"); 
} else {

	die("Unable to locate Twitter OAuth library, expected location 
		 './includes/tmhOAuth/tmhOAuth.php'.");
}

/**
 * Provides wrapper functions for interacting with the Twitter
 * OAuth library
 * @category   Twitter
 * @package    Core
 * @copyright  Copyright (c) 2013 Kieran Brahney
 * @see 	   https://github.com/themattharris/tmhOAuth
 * @version    1.0.0
 */
class TwitterEngine extends tmhOAuth {

	/**
	 * Array of all the errors captured during script execution
	 * @var array
	 * @access  private
	 */
	private $errors = array();

	/**
	 * Whether to log API output or not
	 * @var boolean
	 */
	public $verbose = false;

	/**
	 * Array of verbose output from each API request
	 * @var array
	 * @access  private
	 */
	private $verboseOutput = array();

	/**
	 * Ensure API credentials are valid
	 * @var boolean
	 */
	private $validCredentials = false;

	/**
	 * Reference to the UK WOEID value
	 * @var integer
	 */
	public static $UK_WOEID = 23424975;

	/**
	 * Reference to the USA WOEID value
	 * @var integer
	 */
	public static $USA_WOEID = 23424977;

	/**
	 * Initialise tmhOAuth with the API authentication details
	 * @param array $config Array consisting of the following keys:
	 *                      consumer_key, consumer_secret, token (user_token),
	 *                      secret (user_secret).
	 * @see   tmhOAuth::reconfigure() for other overrideable keys
	 */
	public function __construct($config = array()) {

		// Override $config variables with our authentication details
		$this->config = array_merge(
			array(
				// Fix for tmhOAuth missing index
				'streaming_callback' => '',
				'user_agent'		 => 'Lancaster University Web Crawler'
	        ), 
	        $config
	    );

		// Initialise tmhOAuth
	    parent::__construct($this->config);

	    // Verify credentials
	    $this->validCredentials = $this->isValidAPIUser();
	}

	/**
	 * Returns a collection of the most recent Tweets posted by the user 
	 * indicated by the screen_name or user_id parameters.
	 * @param  string  $user_id   ID of the user for whom to return results for
	 * @param  integer $numTweets Number of tweets to get from the timeline
	 * @return mixed              JSON decoded array on success or FALSE on error
	 */
	public function getUserTimeline($user_id = null, $numTweets = 20) {
		if (isset($user_id) && $this->validCredentials === TRUE) {

			$res = $this->request(
				'GET', 
				$this->url('1.1/statuses/user_timeline'), 
				array(
					'user_id'		=> $user_id,
					'count'		 	=> $numTweets
				)
			);

			return $this->returnAPIResult($res, $this->response);
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserTimeline() or 
								unable to connect to API.");
			return false;
		}
	}

	/**
	 * Return user IDs for every user following the specified user.
	 * @param  integer $user_id      User ID to lookup
	 * @param  integer $numFollowers Number to return, maximum 5000
	 * @return mixed                 JSON decoded array on success or FALSE on error
	 */
	public function getUserFollowers($user_id = null, $numFollowers = 5000) {
		if (isset($user_id) && $this->validCredentials === TRUE) {

			$res = $this->request(
				'GET',
				$this->url('1.1/followers/ids'),
				array(
					'user_id'		=> $user_id,
					'count'			=> $numFollowers
				)
			);

			return $this->returnAPIResult($res, $this->response);
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserTimeline() or 
								unable to connect to API.");
			return false;
		}
	}

	/**
	 * Returns user IDs for every user the specified user is following
	 * @param  integer $user_id    User ID to lookup
	 * @param  integer $numFriends Number to return, maximum 5000
	 * @return mixed               JSON decoded array on success or FALSE on error
	 */
	public function getUserFriends($user_id = null, $numFriends = 5000) {
		if (isset($user_id) && $this->validCredentials === TRUE) {

			$res = $this->request(
				'GET',
				$this->url('1.1/friends/ids'),
				array(
					'user_id' 		=> $user_id,
					'count'			=> $numFriends
				)
			);

			return $this->returnAPIResult($res, $this->response);			
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserTimeline() or 
								unable to connect to API.");
			return false;
		}
	}

	/**
	 * Returns a small random sample of all public statuses
	 * @todo   FIX IT 
	 * @return mixed JSON decoded array on success or FALSE on error
	 */
	public function getPublicTimeline() {
		if ($this->validCredentials === TRUE) {

			$res = $this->request(
				'GET',
				'https://stream.twitter.com/1.1/statuses/sample.json'
			);

			return $this->returnAPIResult($res, $this->response);
		} else {

			$this->logError("Unable to connect to API.");
			return false;
		}
	}

	/**
	 * Returns the top 10 trending topics for a specific WOEID, if trending information is available for it.
	 * @param  integer $WOEID Where on earth identifier
	 * @return mixed          JSON decoded array on success or FALSE on error
	 */
	public function getTrendingTopics($WOEID = 1) {
		if ($this->validCredentials === TRUE) {

			$res = $this->request(
				'GET',
				$this->url('1.1/trends/place'),
				array(
					'id' => $WOEID
				)
			);

			return $this->returnAPIResult($res, $this->response);
		} else {

			$this->logError("Unable to connect to API.");
			return false;
		}
	}

	/**
	 * Search the Twitter API under a given criteria, defaults to english only tweets
	 * @see    https://dev.twitter.com/docs/api/1.1/get/search/tweets
	 * @param  array $query Array of criteria as per twitter API parameters
	 * @return mixed        JSON decoed array on success or FALSE on error
	 */
	public function search($query = null) {
		if (is_array($query) && $this->validCredentials === TRUE) {

			$res = $this->request(
				'GET',
				$this->url('1.1/search/tweets'),
				array_merge(
					array(
						'lang'		=> 'en'
					),
					$query
				)
			);

			return $this->returnAPIResult($res, $this->response);
		} else {

			$this->logError("Unable to connect to API or no search criteria provided.");
			return false;
		}
	}

	/**
	 * Enable or disable verbose output from cURL requests
	 * @param boolean $enable TRUE to enable, FALSE to disable
	 */
	public function setVerbose($enable) {
		if (!is_bool($enable))
			throw new Exception("Boolean argument expected.");
		else
			$this->verbose = $enable;
	} 

	/**
	 * Return verbose output of each API call
	 * @param  int $index Index to access in the array
	 * @return array Nested array of each API call
	 */
	public function getVerboseOutput($index = null) {
		if (isset($index) && $index < count($this->verboseOutput))
			return $this->verboseOutput[$index];
		else
			return $this->verboseOutput;
	}

	/**
	 * Return all the errors captured during script execution
	 * @return array Array of errors
	 */
	public function getErrors() {

		return $this->errors;
	}

	/**
	 * Validate whether the provided API authentication details are correct
	 * @access  private
	 * @return  boolean TRUE on success, FALSE on error
	 */
	private function isValidAPIUser() {
		$res = $this->request('GET', $this->url('1.1/account/verify_credentials'));

		if ($res == 200) {

			// Log verbose
			$this->logVerbose($this->response);
			return true;
		} else {

			// Log the error
			$this->logError($this->response);
			return false;
		}
	}

	/**
	 * Log the most recent cURL error, or provided error
	 * @param  mixed $error Optionally provide either a string for your own error
	 *                      message or pass the array returned by cURL API calls 
	 * @return boolean      TRUE on success, FALSE on error
	 */
	private function logError($error = null) {
		if (isset($error)) {

			// cURL error
			if (isset($error['error']) && isset($error['errno'])) {

				// In some cases this is empty and the error is in the response
				if (empty($error['error'])) {

					$json = json_decode($error['response'], true);
					$error['error'] = $json['errors'][0]['message'];
				}

				// Log error
				$this->errors[] = "[{$error['errno']}] {$error['error']} - " . time();

				// Unset them
				unset($this->response['error']);
				unset($this->response['errno']);
			} else {

				$this->errors[] = $error . " - " . time();
			}

			return true;
		} else {

			return false;
		}
	}

	/**
	 * Log all returned cURL parameters of given API call
	 * @see    tmhOAuth::curlit() for cURL response data array
	 * @param  array $curlResponse Array of cURL response data
	 * @return boolean             TRUE on success, FALSE on error
	 */
	private function logVerbose($curlResponse = null) {
		if ($this->verbose === TRUE && isset($curlResponse)) {

			$this->verboseOutput[] = $curlResponse;
			return true;
		} else {

			return false;
		}
	}

	/**
	 * Return and log the result of an API call
	 * @param  integer $res      HTTP Response code
	 * @param  array   $response API response data
	 * @return mixed             array on success, FALSE on error
	 */
	private function returnAPIResult($res = null, $response = null) {
		if (isset($res) && isset($response)) {

			// Log verbose
			$this->logVerbose($response);

			// Check if there was an error
			if (!empty($response['response'])) {

				// Decode the result
				$json = json_decode($response['response'], true);

				// API returned an error
				if (isset($json['errors'][0]['message'])) {

					$this->logError($response);
					return false;
				} 

				// Return the data
				return $json;
			} else {

				$this->logError($response);
				return false;
			}
		} else {

			$this->logError("Invalid data passed to returnAPIResult (no arguments.)");
			return false;
		}
	}

}

?>