<?php

// Initialise
require_once("./init.php");

$labels = array(
  array('id' => '1455','twitter_id' => '136736006','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1456','twitter_id' => '844218553','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1457','twitter_id' => '974813083','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1458','twitter_id' => '213186050','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1459','twitter_id' => '516691166','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1460','twitter_id' => '374231525','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1461','twitter_id' => '108874808','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1462','twitter_id' => '36643588','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1463','twitter_id' => '1434695436','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1464','twitter_id' => '552899436','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1465','twitter_id' => '263677945','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1466','twitter_id' => '167827324','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1467','twitter_id' => '618456423','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1468','twitter_id' => '805203918','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1469','twitter_id' => '288697268','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1470','twitter_id' => '1703809190','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1471','twitter_id' => '1325830897','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1472','twitter_id' => '1706451553','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1473','twitter_id' => '21182145','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1474','twitter_id' => '1486790214','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1475','twitter_id' => '162675854','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1476','twitter_id' => '560852550','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1477','twitter_id' => '211449886','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1478','twitter_id' => '367929375','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1479','twitter_id' => '363807168','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1480','twitter_id' => '1038213092','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1481','twitter_id' => '716650439','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1482','twitter_id' => '232568766','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1483','twitter_id' => '20937750','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1484','twitter_id' => '830555316','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1485','twitter_id' => '338814382','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1486','twitter_id' => '1904280308','volunteer_id' => '4','verdict' => 'Discard'),
  array('id' => '1487','twitter_id' => '220633070','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1488','twitter_id' => '565510363','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1489','twitter_id' => '1633192412','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1490','twitter_id' => '348313732','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1491','twitter_id' => '392505931','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1492','twitter_id' => '1057380416','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1493','twitter_id' => '527409776','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1494','twitter_id' => '187092919','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1495','twitter_id' => '1734786296','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1496','twitter_id' => '470107281','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1497','twitter_id' => '97939816','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1498','twitter_id' => '17707364','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1499','twitter_id' => '161615327','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1500','twitter_id' => '1468808095','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1501','twitter_id' => '394766406','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1502','twitter_id' => '471838048','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1503','twitter_id' => '1059188276','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1504','twitter_id' => '635704837','volunteer_id' => '4','verdict' => 'Discard'),
  array('id' => '1505','twitter_id' => '924619730','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1506','twitter_id' => '745649654','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1507','twitter_id' => '80589178','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1508','twitter_id' => '106214850','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1509','twitter_id' => '38865971','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1510','twitter_id' => '18733336','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1511','twitter_id' => '1468762236','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1512','twitter_id' => '200996642','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1513','twitter_id' => '525019312','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1514','twitter_id' => '1142084582','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1515','twitter_id' => '1656012288','volunteer_id' => '4','verdict' => 'Bot'),
  array('id' => '1516','twitter_id' => '183980896','volunteer_id' => '4','verdict' => 'Human'),
  array('id' => '1517','twitter_id' => '951669642','volunteer_id' => '4','verdict' => 'Discard'),
  array('id' => '1518','twitter_id' => '14695795','volunteer_id' => '4','verdict' => 'Discard')
);

foreach ($labels as $label) {
  // Lookup the labels
  $res = $DatabaseEngine->arrayquery(
    "SELECT `volunteer_id` FROM `labels` WHERE `twitter_id` = {$label['twitter_id']}"
  );

  $arr = array(4 => '1');
  foreach ($res as $volunteer) {
    // Serialize the result
    $arr[$volunteer['volunteer_id']] = '1';
  }

  // Update the queue
  $DatabaseEngine->query(
    "UPDATE `rater_queue` SET `who_checks` = '" . serialize($arr) . "' WHERE `twitter_id` = {$label['twitter_id']}"
  );
}