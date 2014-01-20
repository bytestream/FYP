<?php

// Initialise
require_once("./init.php");

$labels = $DatabaseEngine->arrayquery(
  "SELECT `twitter_id`,
          group_concat(`volunteer_id` SEPARATOR ' ') as volunteer,
          group_concat(`verdict` SEPARATOR ' ') as verdict
   FROM `labels`
   GROUP BY `twitter_id`"
);

$consensus = 0;
$con2 = 0;
$error = 0;
$bot = 0;
$human = 0;
$discard = 0;
$wrong = array();

function inc($verdict) {
  global $bot, $human, $discard;
  if ($verdict[0] == "Bot") $bot++;
  elseif ($verdict[0] == "Human") $human++;
  elseif ($verdict[0] == "Discard") $discard++;
}

echo '<h3>Account Statistics</h3><ul>';
foreach ($labels as $label) {
  $verdict = explode(" ", $label['verdict']);

  // All raters agreed
  if (count(array_unique($verdict)) === 1) {
    $consensus++;
    inc($verdict);
  } else if (count(array_unique($verdict)) === 2) {
    $con2++;
    inc($verdict);

    $volunteers = explode(" ", $label['volunteer']);
    // Get the person who got it wrong
    $unique = array_unique($verdict);
    $duplicates = array_diff_assoc($verdict, $unique);
    $result = array_keys(array_diff($unique, $duplicates));
    $wrong[$volunteers[$result[0]]]++;
  } else {
    $error++;
  }
}
$total = $bot + $human + $discard;
echo '<li><strong>Total accounts where all raters agreed:</strong> ' . $consensus . '</li>';
echo '<li><strong>Total accounts with 2 raters in agreement:</strong> ' . $con2 . '</li>';
echo '<li><strong>Total accounts with no agreement:</strong> ' . $error . '</li><br />';

echo '<li><strong>Total based on concensus:</strong> ' . $total . '</li>';
echo '<li><strong>Total bots:</strong> ' . $bot . ' ( ' . round(($bot/$total)*100,2) . '% )</li>';
echo '<li><strong>Total humans:</strong> ' . $human . ' ( ' . round(($human/$total)*100,2) . '% )</li>';
echo '<li><strong>Total discarded:</strong> ' . $discard . ' ( ' . round(($discard/$total)*100,2) . '% )</li><br />';
echo '</ul>';

echo '<h3>Volunteers</h3><table><tr><th>E-mail Address</th><th>Total rated</th><th>Total wrong (not in agreement)</th><th>Percentage wrong</th></tr>';
$accounts = $DatabaseEngine->arrayquery("SELECT `volunteer_id`, COUNT(*) as count FROM `labels` GROUP BY `volunteer_id` ORDER BY count DESC");
foreach ($accounts as $account) {
  $res = $DatabaseEngine->arrayquery("SELECT `email` FROM `volunteers` WHERE `volunteer_id` = '" . $account['volunteer_id'] . "'");
  echo '<tr><td>' . $res[0]['email'] . '</td><td>' . $account['count'] . '</td><td>' . $wrong[$account['volunteer_id']] . '</td><td>' . round(($wrong[$account['volunteer_id']]/$account['count'])*100,2) . '%</td></tr>';
}
echo '</table>';