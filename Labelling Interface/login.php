<?php

  // Initialise
  require_once("./init.php");

  // Check if they're logged in
  if (isset($_SESSION['session_id']) && isset($_SESSION['volunteer_id'])) {
    header("Location: index.php");
    die("");
  }

  // Was the form submitted?
  if ((isset($_POST['email']) && isset($_POST['password']))) {

    // Get the user
    $res = $DatabaseEngine->arrayquery(
        "SELECT * FROM volunteers WHERE email = :email",
        array(
            ':email'    => $_POST['email']
        )
    );

    // Check if we found a valid user
    if (isset($res[0]['volunteer_id'])) {

      // Verify that the passwords match
      if (md5($_POST['password']) == $res[0]['password']) {

        // Allow them to login
        $_SESSION['session_id']       = session_id();
        $_SESSION['volunteer_id']     = $res[0]['volunteer_id'];

        // Redirect back to request page
        header("Location: index.php");
      } else {

        // Redirect the user, password mismatch
        header("Location: login.php?error=1");
      }
    } else {

      // Redirect the user, no such user exists
      header("Location: login.php?error=3");
    }
  }

?>

<?php require_once("header.html"); ?>

        <div id="center">
          <form class="form-signin" action="login.php" method="POST">
            <h2 class="form-signin-heading">Please sign in</h2>
            <?php if (isset($_GET['error'])) { ?>
              <div class="alert alert-danger">
                <strong>Oh snap!</strong>
                <?php
                  if ($_GET['error'] == '1') echo "I'm sorry the provided password doesn't match our records - <a href='forgot.php'>Reset your password?</a>";
                  else echo "I'm unable to locate the provided e-mail address.";
                ?>
              </div>
            <?php } else if (isset($_GET['reset'])) { ?>
              <div class="alert alert-success">
                <strong>Woohoo!</strong>
                Your password has been reset, please check your e-mail.
              </div>
            <?php } else if (isset($_GET['logout'])) { ?>
              <div class="alert alert-success">
                <strong>Woohoo!</strong>
                You have successfully logged out.
              </div>
            <?php } else if (isset($_GET['timeout'])) { ?>
              <div class="alert alert-info">
                <strong>Warning!</strong>
                You were idle for longer than 30 minutes so we've logged you out for security purposes.
              </div>
            <?php } ?>
            <input type="text" name="email" class="form-control" placeholder="Email address" autofocus />
            <input type="password" name="password" class="form-control" placeholder="Password" />
            <input type="submit" value="Sign in" class="btn btn-lg btn-primary btn-block">
          </form>
          <strong style="float: right">Need an Account? <a href="register.php">Register!</a></strong>
        </div>
      </div>

<?php require_once("footer.html"); ?>