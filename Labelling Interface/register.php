<?php

  // Initialise
  require_once("./init.php");

  // Check if they're logged in
  if (isset($_SESSION['session_id']) && isset($_SESSION['volunteer_id'])) {
    header("Location: index.php");
    die("");
  }
  
  // Was the form submitted?
  if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm'])) {

    if ($_POST['password'] !== $_POST['confirm']) {

      // Passwords don't match
      header("Location: register.php?error=1");
    }

    // Hash the password
    $password = md5($_POST['password']);

    // Add the user to the DB
    $res = $DatabaseEngine->query(
      "INSERT INTO `volunteers` (`email`, `password`) 
       VALUES(:email, :password)",
       array(
        ":email"        => $_POST['email'],
        ":password"     => $password
      )
    );

    // Everything okay
    if ($res !== FALSE) {

      // Set the session details
      $_SESSION['session_id']       = session_id();
      $_SESSION['volunteer_id']     = $DatabaseEngine->dbh->lastInsertId();

      // Redirect the user to the main area
      header("Location: index.php?reg=1");
    } else {

      // Redirect the user, an error occurred
      header("Location: register.php?error=2");
    }
  } 

?>

<?php require_once("header.html"); ?>

        <div id="center">
          <form id="register" class="form-signin" action="register.php" method="POST" autocomplete="off">
            <h2 class="form-signin-heading">Register</h2>
            <?php if (isset($_GET['error'])) { ?>
              <div class="alert alert-danger">
                <strong>Oh snap!</strong> 
                <?php 
                  if ($_GET['error'] == '1') 
                    echo "Please ensure that you're passwords correctly match.";
                  else
                    echo "I'm sorry there was a problem reaching the database - please try again soon."; ?>
              </div>
            <?php } ?>
            <input type="text" name="email" class="form-control" placeholder="E-mail Address" autofocus required />
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required />
            <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required />
            <input type="submit" value="Register" class="btn btn-lg btn-primary btn-block">
          </form>

          <script>
            $("#register").validate({
              rules: {
                email: {
                  required: true,
                  email: true
                },
                passsword: {
                  required: true,
                  minlength: 3
                },
                confirm: {
                  required: true,
                  equalTo: "#password"
                }
              }
            });
          </script>
        </div>      
      </div>
    
<?php require_once("footer.html"); ?>