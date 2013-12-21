<?php

	// Initialise
  	require_once("./init.php");

	// Check if they're logged in
	if (isset($_SESSION['session_id']) && isset($_SESSION['volunteer_id'])) {
	 	header("Location: index.php");
	    die("");
  	}

  	// Reset password
  	if (isset($_POST['email'])) {

  		// Get the user
	    $res = $DatabaseEngine->arrayquery(
	        "SELECT * FROM volunteers WHERE email = :email",
	        array(
	            ':email'    => $_POST['email']
	        )
	    );

	    // Check if we found a valid user
	    if (isset($res[0]['volunteer_id'])) {

	    	// Generate a new password
            $password = generateRandomString();
	    	$hashpasswd = md5($password);

	    	// Reset the password
	    	$res = $DatabaseEngine->query(
	    		"UPDATE `volunteers` SET `password` = :password WHERE email = :email",
	    		array(
	    			':email'	=> $_POST['email'],
	    			':password'	=> $hashpasswd
	    		)
	    	);

	    	// E-mail the password
	    	sendMail(
	    		$_POST['email'],
	    		"Reset password",
	    		"Your password has been reset.\r\nE-mail address: {$_POST['email']}\r\nPassword: {$password}\r\nKind regards."
	    	);

	    	header("Location: login.php?reset=1");
	    } else {

	    	// User doesn't exist
	    	header('Location: forgot.php?error=1');
	    }
  	}

?>

<?php require_once("header.html"); ?>

        <div id="center">
        	<form class="form-signin" action="forgot.php" method="POST">
            <h2 class="form-signin-heading">Reset Password</h2>
            <?php if (isset($_GET['error'])) { ?>
              <div class="alert alert-danger">
                <strong>Oh snap!</strong>
                <?php
                  echo "I'm unable to locate the provided e-mail address.";
                ?>
              </div>
            <?php } ?>
            <input type="text" name="email" class="form-control" placeholder="Email address" autofocus />
            <input type="submit" value="Reset" class="btn btn-lg btn-primary btn-block">
          </form>
          <strong style="float: right">Need an Account? <a href="register.php">Register!</a></strong>
        </div>
      </div>

<?php require_once("footer.html"); ?>