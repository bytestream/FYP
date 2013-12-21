<?php

	// Initialise
  require_once("./init.php");

	// Check if they're logged in
 	if (isset($_SESSION['session_id']) && isset($_SESSION['volunteer_id'])) {
    session_unset();
  	session_destroy();

    // Redirect them to the login page
    if (isset($_GET['timeout'])) {
      header("Location: login.php?timeout");
    } else {
	    header("Location: login.php?logout=1");
    }
	} else {

		// Not logged in
		header('Location: login.php');
 	}


?>