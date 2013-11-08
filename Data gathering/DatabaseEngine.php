<?php

/**
 * Provides wrapper functions for interacting with database
 * @category   Twitter
 * @package    Core
 * @copyright  Copyright (c) 2013 Kieran Brahney
 * @version    1.0.0
 */
class DatabaseEngine extends PDO {

	/**
	 * PDO connection object
	 * @var object
	 */
	private $dbh;

	/**
	 * PDOStatement object
	 * @var object
	 */
	private $stmt;

	/**
	 * Result of the previous execute() 
	 * @var boolean
	 */
	private $_result;

	/**
	 * All of the queries made for a given request
	 * @var array
	 */
	private $_queries = array();

	/**
	 * Initialise the DB connection
	 * @param string $dsn     The Data Source Name, or DSN, contains the information required to connect to the database.
	 * @param string $user    The user name for the DSN string. This parameter is optional for some PDO drivers.
	 * @param string $passwd  The password for the DSN string. This parameter is optional for some PDO drivers.
	 * @param array  $options A key=>value array of driver-specific connection options.
	 */
	public function __construct($dsn, $user, $passwd, $options = array()) {
		try {
			// Merge the options
			$opts = array_merge(
				$options, 
				array(
					PDO::ATTR_EMULATE_PREPARES 		=> false,
					PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION
				)
			);

			// Initialise the connection
			parent::__construct($dsn, $user, $passwd, $opts);
			$this->dbh = $this;
		} catch (PDOException $e) {
			die("Error connecting to DB.");
		}
	}

	/**
	 * Perform a query against the database with optional bind parameters
	 * @param  string  $query        Raw query with placeholders
	 * @param  array   $bind         Placeholder and values, placeholder=>values mapping
	 * @param  boolean $supressError Whether to print SQL errors or not
	 * @return PDOStatement          PDOStatement to execute further commands on
	 */
	public function query($query, $bind = array(), $supressError = false) {
		// Perform the query and get the time
		$start_time = microtime(true);
		try {
			// Prepare the query
			$this->stmt = $this->dbh->prepare($query);
			// Send the placeholder data and execute the query
			if (count($bind) == 0)
				$this->_result = $this->stmt->execute();
			else
				$this->_result = $this->stmt->execute($bind);
		} catch (PDOException $e) {
			if (!$this->_result && !$supressError)
				echo $e->getMessage();
		}
		$end_time = microtime(true);
		$total_time = substr(($end_time - $start_time), 0, 8);

		// Build the query for logging
		$this->_queries = $this->printQueryAsString($query, $bind) . " - " . $total_time;

		// Return PDOStatement for execution with $res->fetchAll() etc
		return $this->stmt;
	}

	/**
	 * Perform a fetchAll on the query returning an array
	 * @param  string  $query        [description]
	 * @param  array   $bind         [description]
	 * @param  boolean $supressError [description]
	 * @return array                 Array of results or EMPTY array on failure
	 */
	public function arrayquery($query, $bind = array(), $supressError = false) {
		// Execute the query
		$this->query($query, $bind, $supressError);

		// Check if we have any results
		if ($this->_result !== false && $this->stmt->rowCount() > 0) {
			// Return the array of results
			return $this->stmt->fetchAll();
		} else {
			// Return an empty array
			return array();
		}
	}

	/**
	 * Fetch a single row from a PDOStatement
	 * @param  PDOStatement $stmt       Optionally execute on a given PDOStatement
	 * @param  integer      $fetchStyle Controls how the next row will be returned to the caller
	 * @return The return value of this function on success depends on the fetch type. In all cases, FALSE is returned on failure.
	 */
	public function single(PDOStatement &$stmt, $fetchStyle = PDO::FETCH_BOTH) {
		if (isset($stmt)) {
			return $stmt->fetch($fetchStyle);
		} else {
			$this->stmt->fetch($fetchStyle);
		}
	}

	/**
	 * Build the SQL query that will ultimately be executed by replacing placeholders
	 * with their binded parameters
	 * @param  string $rawQuery Raw query with placeholders still active
	 * @param  array  $bind     Array of bind params (0 => 'Hey', ':name' => 'Kieran')
	 * @return string           Complete array with bind params substitued for values
	 */
	private function printQueryAsString($rawQuery, $bind = array()) {
		$regex = $replacements = array();

		foreach ($bind as $key => $value) {
			if (is_string($key)) 
				$regex[] = '/:' . $key . '/';
			else 
				$regex[] = '/[?]/';

			if (is_numeric($value)) 
				$replacements[] = intval($value);
			else 
				$replacements[] = '"' . $value . '"';
		}

		return preg_replace($regex, $replacements, $rawQuery, 1);
	}
	
}

?>