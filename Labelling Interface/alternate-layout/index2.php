<?php

	// Check if they're logged in or not
	/*if (!isset($_SESSION['session_id']) || !isset($_SESSION['user_id'])) {
		// Redirect them to the login page
		header("Location: login.php");
		die("");
	}*/

  // Initialise
  require_once("./init.php");

  // Check whether we have a form submission
  if (isset($_POST['submit'])) {
    // Set the verdict
    $DatabaseEngine->query(
      "INSERT INTO `labels` (`twitter_id`, `volunteer_id`, `verdict`) 
       VALUES (:twitterid, :volunteerid, :verdict)",
       array(
        ":twitterid"     => $_POST['twitter_id'],
        ":volunteerid"   => $_SESSION['user_id'],
        ":verdict"       => $_POST['submit']
      )
    );

    // Update the total viewing and total rated
    $DatabaseEngine->query(
      "UPDATE `rater_queue` SET `total_viewing` = `total_viewing` - 1, `total_checks` = `total_checks` + 1
       WHERE `twitter_id` = :userid",
       array(
        ":userid"       => $_POST['twitter_id']
      )
    );

    // Continue loading another user 
  }

  // Get a user from the queue
  $queue = $DatabaseEngine->arrayquery(
    "SELECT `twitter_id` FROM `rater_queue` WHERE `total_viewing` < 3 AND `total_checks` < 3 LIMIT 1"
  );

  // Get that users details
  $user = $DatabaseEngine->arrayquery(
    "SELECT * FROM `users` WHERE `user_id` = :userid",
    array(
      ':userid'       => $queue[0]['twitter_id']
    )
  );

  // Get 10 random tweets made by that user
  $tweets = $DatabaseEngine->arrayquery(
    "SELECT * FROM `tweets` WHERE `user_id` = :userid ORDER BY RAND() LIMIT 10",
    array(
      ':userid'    => $user[0]['user_id']
    )
  );

  // Update the viewing count 
  /*$res = $DatabaseEngine->query(
    "UPDATE `rater_queue` SET `total_viewing` = `total_viewing` + 1 WHERE `twitter_id` = :userid",
    array(
      ':userid'        => $user[0]['user_id']
    )
  );*/

?>

<?php require_once("header2.html"); ?>

		<?php
	      	if (!isset($user[0]) || !isset($tweets[0])) {
	        	echo '<div class="alert alert-danger">
		              <strong>Oh Snap!</strong> 
		              Something went wrong, let\'s try and get another user - <strong>refreshing now.</strong>
		              </div>';

	        	header("refresh:3;url=index.php");
	        	exit("");
	      	}
	    ?>

		<div id="header">
			<div class="pull-left" style="width: 60%; float: left">
              	<h1 id="screen_name" style="margin-top: 0px">
            		<a href="http://twitter.com/<?php echo $user[0]['screen_name']; ?>">
                  		@<?php echo $user[0]['screen_name']; ?>
            		</a>
              	</h1>
          		<p id="description"><?php echo $user[0]['description']; ?></p>
            </div>

            <div class="pull-right" style="width: 40%; float: right; text-align: right">
              	<p id="creation_date">
                	<strong>Creation Date: </strong>
                	<?php echo date("j M Y", strtotime($user[0]['creation_date'])); ?>
              	</p>
              	<p id="location">
                	<strong>Location: </strong>
                	<?php echo $user[0]['location']; ?>
              	</p>
              	<p id="total_followers" style="width: 100%">
              	  	<strong>Total Followers: </strong>
              	  	<?php echo $user[0]['total_followers']; ?>
              	</p>
              	<p id="total_friends" style="width: 100%">
                	<strong>Total Friends: </strong>
                	<?php echo $user[0]['total_friends']; ?>
              	</p>
            </div>
		</div>

		<style>
            .tweet:nth-child(odd) { background-color: #f9f9f9; }
            .tweet:nth-child(even) { background-color:#fff; }
      	</style>
      	<script type="text/javascript">
      		$(function(){
          		$('#tweets').slimScroll({
              		height: '100%'
      			});
      		});
      	</script>
      	<div id="outerTweets" style="width: 80%; margin: 0 auto; height: 68%">
	      	<h2>Tweets</h2>
			<div id="tweets">
	      	<?php
	      	  	foreach ($tweets as $tweet) {
	      	?>

	        	<article class="tweet" style="border-bottom: 1px solid #e9e9e9; padding: 10px">
	            	<?php if ($tweet['retweet_count'] > 0) { ?>
	              	<p class="pull-right" style="margin-left: 20px; width: 60px">
	                	<strong style="display: block"><?php echo $tweet['retweet_count']; ?></strong>
	                	<small class="details" style="font-size: 11px; color: #999">RETWEET<?php if ($tweet['retweet_count'] > 1) { echo "S"; } ?></small>
	          		</p>
	            	<?php } ?>

	              	<p style="margin: 0"><?php echo $tweet['tweet']; ?></p>
	              	<aside id="tweet-details" class="details" style=" line-height: 22px; height: 20px;font-size: 11px; color: #999">
	                	<p>
	                  		<?php echo date("g:i A - n M y", strtotime($tweet['creation_date'])); ?> · 
	                  		<strong>Source: </strong><?php echo $tweet['source']; ?> 
	                	</p>
	              	</aside>
	        	</article>

	      	<?php } // end of tweet loop ?>
			</div>
		</div>

		<div id="actions" style="text-align: center">
			<form action="index.php" method="POST">
              	<input type="submit" value="Human" class="btn btn-success" style="float: left; width: 70px" />
              	<input type="submit" value="Discard" class="btn btn-default" style="width: 70px" />
              	<input type="submit" value="Bot" class="btn btn-warning" style="float: right; width: 70px" />
        	</form>
		</div>

<?php require_once("footer2.html"); ?>