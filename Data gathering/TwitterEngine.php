<?php

chdir(dirname(__FILE__));
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
	 * WOEIDs
	 * @see  http://woeid.rosselliot.co.nz/
	 */
	const UK_WOEID  = 23424975;
	const USA_WOEID = 23424977;

	/**
	 * Type of API authentication
	 */
	const USER_AUTH = 1;
	const APP_AUTH  = 2;

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

	    // Setup application-only authentication in the configuration just in case they want to use it
	    $bearerToken = $this->getBearerToken();
	    if ($bearerToken !== FALSE) {
		    $this->reconfigure(array_merge(
		    	$this->config,
		    	array(
		    		'bearer' => $bearerToken
		    	)
		    ));
		}

	    // Verify credentials if they provided user-authentication
	    if (isset($config['token']) && isset($config['secret'])) {
	    	$this->validCredentials = $this->isValidAPIUser();
	    }
	}

	/**
	 * Returns a collection of the most recent Tweets posted by the user 
	 * indicated by the screen_name or user_id parameters.
	 * @param  string  $user_id   ID of the user for whom to return results for
	 * @param  integer $numTweets Number of tweets to get from the timeline
	 * @return mixed              JSON decoded array on success or FALSE on error
	 */
	public function getUserTimeline($user_id = null, $authType = self::USER_AUTH, $numTweets = 20) {
		if (isset($user_id)) {

			$req = array(
				'method'	=> 'GET', 
				'url' 		=> $this->url('1.1/statuses/user_timeline'), 
				'params'	=> array(
					'user_id'		=> $user_id,
					'count'		 	=> $numTweets
				)
			);

			// Check we're able to connect 
			if ($authType == self::USER_AUTH) {

				if ($this->validCredentials === TRUE) {

					// Authenticate as a user
					$res = $this->user_request($req);
				} else {

					$this->logError("Unable to connect to API.");
					return false;
				}
			} else {

				// Authenticate as an app
				$res = $this->apponly_request($req);
			}

			// Return the result
			return $this->returnAPIResult($res, $this->response);
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserTimeline() or" . 
								"unable to connect to API.");
			return false;
		}
	}

	/**
	 * Return user IDs for every user following the specified user.
	 * @param  integer $user_id      User ID to lookup
	 * @param  integer $numFollowers Number to return, maximum 5000
	 * @return mixed                 JSON decoded array on success or FALSE on error
	 */
	public function getUserFollowers($user_id = null, $authType = self::USER_AUTH, $numFollowers = 5000) {
		if (isset($user_id)) {

			$req = array(
				'method'	=> 'GET', 
				'url' 		=> $this->url('1.1/followers/ids'), 
				'params'	=> array(
					'user_id'		=> $user_id,
					'count'		 	=> $numFollowers
				)
			);

			// Check we're able to connect 
			if ($authType == self::USER_AUTH) {

				if ($this->validCredentials === TRUE) {

					// Authenticate as a user
					$res = $this->user_request($req);
				} else {

					$this->logError("Unable to connect to API.");
					return false;
				}
			} else {

				// Authenticate as an app
				$res = $this->apponly_request($req);
			}

			// Return the result
			return $this->returnAPIResult($res, $this->response);
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserFollowers() or " .
								"unable to connect to API.");
			return false;
		}
	}

	/**
	 * Returns user IDs for every user the specified user is following
	 * @param  integer $user_id    User ID to lookup
	 * @param  integer $numFriends Number to return, maximum 5000
	 * @return mixed               JSON decoded array on success or FALSE on error
	 */
	public function getUserFriends($user_id = null, $authType = self::USER_AUTH, $numFriends = 5000) {
		if (isset($user_id)) {

			$req = array(
				'method'	=> 'GET', 
				'url' 		=> $this->url('1.1/friends/ids'), 
				'params'	=> array(
					'user_id'		=> $user_id,
					'count'		 	=> $numFriends
				)
			);

			// Check we're able to connect 
			if ($authType == self::USER_AUTH) {

				if ($this->validCredentials === TRUE) {

					// Authenticate as a user
					$res = $this->user_request($req);
				} else {

					$this->logError("Unable to connect to API.");
					return false;
				}
			} else {

				// Authenticate as an app
				$res = $this->apponly_request($req);
			}

			// Return the result
			return $this->returnAPIResult($res, $this->response);			
		} else {

			// No screen name provided or unable to connect to API
			$this->logError("No user_id provided to getUserFriends() or " . 
								"unable to connect to API.");
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
	public function getTrendingTopics($WOEID = 1, $authType = self::USER_AUTH) {
		$req = array(
			'method'	=> 'GET', 
			'url' 		=> $this->url('1.1/trends/place'), 
			'params'	=> array(
				'id' => $WOEID
			)
		);

		// Check we're able to connect 
		if ($authType == self::USER_AUTH) {

				if ($this->validCredentials === TRUE) {

					// Authenticate as a user
					$res = $this->user_request($req);
				} else {

					$this->logError("Unable to connect to API.");
					return false;
				}
		} else {

			// Authenticate as an app
			$res = $this->apponly_request($req);
		}

		// Return the result
		return $this->returnAPIResult($res, $this->response);
	}

	/**
	 * Search the Twitter API under a given criteria, defaults to english only tweets
	 * @see    https://dev.twitter.com/docs/api/1.1/get/search/tweets
	 * @param  array $query Array of criteria as per twitter API parameters
	 * @return mixed        JSON decoed array on success or FALSE on error
	 */
	public function search($query = null, $authType = self::USER_AUTH) {
		if (is_array($query)) {

			$req = array(
				'method'	=> 'GET', 
				'url' 		=> $this->url('1.1/search/tweets'), 
				'params'	=> array_merge(
					array(
						'lang'		=> 'en'
					),
					$query
				)
			);

			// Check we're able to connect 
			if ($authType == self::USER_AUTH) {

				if ($this->validCredentials === TRUE) {

					// Authenticate as a user
					$res = $this->user_request($req);
				} else {

					$this->logError("Unable to connect to API.");
					return false;
				}
			} else {

				// Authenticate as an app
				$res = $this->apponly_request($req);
			}

			// Return the result
			return $this->returnAPIResult($res, $this->response);
		} else {

			$this->logError("No search criteria provided.");
			return false;
		}
	}

	/**
	 * Fetch your bearer token from the Twitter API based on the consumer token and secret
	 * @return mixed Bearer token required to access the API in application-only authentication or
	 *                      FALSE on error
	 */
	public function getBearerToken() {
		$bearer = $this->bearer_token_credentials();
		$params = array(
	  		'grant_type' => 'client_credentials',
		);

		// Get the bearer token
		$code = $this->request(
		  	'POST',
		  	$this->url('/oauth2/token', null),
		  	$params,
		  	false,
		  	false,
		  	array(
		    	'Authorization' => "Basic ${bearer}"
		  	)	
		);

		// Everything ok
		if ($code == 200) {

			// Log the response
			$this->logVerbose($this->response);
		  	$data = json_decode($this->response['response']);

		  	if (isset($data->token_type) && strcasecmp($data->token_type, 'bearer') === 0) {
		    	
		    	// Return the bearer token
		    	return $data->access_token;
		  	}
	  	} else {

	  		// Log the error
	  		$this->logError($this->response['response']);
	  		return false;
	  	}
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
	 * Log the most recent cURL error, or provided error
	 * @param  mixed $error Optionally provide either a string for your own error
	 *                      message or pass the array returned by cURL API calls 
	 * @return boolean      TRUE on success, FALSE on error
	 */
	private function logError($error = null) {
		if (isset($error)) {

			// Error is user-provided string
			if (!is_array($error)) {

				// Write string to file
				$this->errors[] = $error . " - " . time();
			} else if (isset($error['error']) && isset($error['errno'])) {
				// cURL error

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