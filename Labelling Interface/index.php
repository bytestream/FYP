<?php

  // Initialise
  require_once("./init.php");

  // Check if they're logged in or not
  if (!isset($_SESSION['session_id']) || !isset($_SESSION['volunteer_id'])) {
    // Redirect them to the login page
    header("Location: login.php");
    die("");
  }

  // If its been longer than 30 mins time them out
  if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Redirect
    header("Location: logout.php?timeout=1");
    die("");
  }

  // Update last activity time stamp
  $_SESSION['last_activity'] = time();
  $DatabaseEngine->query(
    "UPDATE `volunteers` SET `last_active` = NOW() WHERE `volunteer_id` = :volunteer_id",
    array(
      ':volunteer_id' => $_SESSION['volunteer_id']
    )
  );

  // Check whether we have a form submission
  if (isset($_POST['verdict']) && isset($_POST['twitter_id'])) {

    // Check that we haven't already had 3 people rate this account
    $res = $DatabaseEngine->arrayquery(
      "SELECT `total_checks`, `who_checks` FROM `rater_queue` WHERE `twitter_id` = :twitterid",
      array(
        ':twitterid'  => $_POST['twitter_id']
      )
    );

    // Check that we haven't already had 3 people rate this account
    if (isset($res[0]['total_checks']) && $res[0]['total_checks'] < 3) {
      // Set the verdict
      $DatabaseEngine->query(
        "INSERT INTO `labels` (`twitter_id`, `volunteer_id`, `verdict`)
         VALUES (:twitterid, :volunteerid, :verdict)",
         array(
          ":twitterid"     => $_POST['twitter_id'],
          ":volunteerid"   => $_SESSION['volunteer_id'],
          ":verdict"       => $_POST['verdict']
        )
      );

      // Update who has rated the account
      $checks = @unserialize($res[0]['who_checks']);
      $checks[$_SESSION['volunteer_id']] = '1';
      $DatabaseEngine->query(
        "UPDATE `rater_queue` SET `who_checks` = :who_checks, `total_checks` = :total_checks
         WHERE `twitter_id` = :userid",
         array(
          ":who_checks"   => serialize($checks),
          ":total_checks" => $res[0]['total_checks'] + 1,
          ":userid"       => $_POST['twitter_id']
        )
      );
    }

    // Continue loading another user
  }

  // Get a user from the queue
  $queue = $DatabaseEngine->arrayquery("SELECT `twitter_id`, `who_checks` FROM `rater_queue` WHERE `total_checks` < 3 ORDER BY RAND()");

  foreach ($queue as $user) {
    $checks = @unserialize($user['who_checks']);

    // We found a suitable user
    if ($user['who_checks'] == '0' || !isset($checks[$_SESSION['volunteer_id']])) {
      $queue[0]['twitter_id'] = $user['twitter_id'];
      break;
    }
  }

  // Check that we found a user to display
  if (isset($queue[0]['twitter_id'])) {
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
  } else {
    // No users are left in the queue
    echo '<div class="alert alert-danger">
            <strong>Oh Snap!</strong>
            It looks like we\'ve completed all the accounts that need rating! <strong>Thank you for your efforts!</strong>
          </div>';

    exit("");
  }

?>

<?php require_once("header.html"); ?>

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

        <header style="padding: 15px; width: 100%">
        <?php
          if (isset($_GET['reg'])) {
            echo "<script type='text/javascript'>
                    $(window).load(function(){
                      $('#myModal').modal('show');
                    });
                  </script>";
          }
        ?>
          <!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#myModal">
            Help I'm Stuck!
          </button>

          <!-- Modal -->
          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title" id="myModalLabel">Twitter Account Labelling</h4>
                </div>
                <div class="modal-body">
                  <strong>Hey! How's it going?</strong>
                  <p>I'd like to start by quickly thanking you for participating in this study. Over the past few weeks we've been busy trawlling
                  Twitter for accounts with the aim to build up a data set which we can label as bots and humans. Here's where you come in! We need
                  you to mark whether you believe a given account is either a bot (automated) or human.</p>

                  <h3>But, how!? I hear you cry.</h3>
                  <p>Everytime you login to the system you will be presented with a random Twitter account and a subset of their tweets. At the bottom
                  of the page you will have 3 options:</p>
                  <ol>
                    <li>Bot: A bot is an account that you believe to be automated, exhibiting a pattern or producing spam.</li>
                    <li>Discard: Some accounts have foreign tweets so please use this option where appropriate.</li>
                    <li>Human: An account that has clear social interactions and exhibits irregular patterns.</li>
                  </ol>

                  <h3>What determines a bot?</h3>
                  <p>Whilst we don't want to give you set criteria for determing a bot, as it should depend on your own opinions, we deal feel it would
                  be beneficial to point you in the right direction. Some things you might want to look out for are:</p>
                  <ul>
                    <li>Posting malicious or spam links (please be careful if checking these)</li>
                    <li>Regular intervals between tweets</li>
                    <li>Very few friends or interactions with other users</li>
                    <li>Repeat posts from the API or other unknown sources (check the 'Source' of tweets)</li>
                    <li>Excessive use of hash tags in a given tweet.</li>
                  </ul>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->

          <div class="pull-right">
            <a href="logout.php">Logout</a>
          </div>
        </header>

        <section id="account" style="width: 80%; height: 80%; margin: 0 auto;">
          <!-- Basic account details -->
          <header id="account-details" style="height: 109px; margin-bottom: 2%; display: block">
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
          </header>

          <!-- Account tweets -->
          <h2>Tweets</h2>
          <style>
            .tweet:nth-child(odd) { background-color: #f9f9f9; }
            .tweet:nth-child(even) { background-color:#fff; }
          </style>
          <script type="text/javascript">
          $(function(){
              $('#tweets').slimScroll({
                  height: '68%'
              });
          });
          </script>
          <section id="tweets" style="height: 100% !important; overflow: auto">
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
          </section>

          <section id="verdict" style="margin: 10px auto; width: 80%; text-align: center;">
            <form action="index.php" method="POST">
              <input type="hidden" name="twitter_id" value="<?php echo $user[0]['user_id']; ?>" />
              <input type="submit" name="verdict" value="Human" class="btn btn-success" style="float: left; width: 70px" />
              <input type="submit" name="verdict" value="Discard" class="btn btn-default" style="width: 70px" />
              <input type="submit" name="verdict" value="Bot" class="btn btn-warning" style="float: right; width: 70px" />
            </form>
          </section>
        </section>

<?php require_once("footer.html"); ?>
