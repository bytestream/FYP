<?php

// Initialise
require_once("./init.php");

$keys = array(4, 5, 7, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);

$volunteers = $DatabaseEngine->arrayquery("SELECT `volunteer_id` FROM `volunteers`");

// Create an array
$data = array();
$labels = $DatabaseEngine->arrayquery("SELECT * FROM `labels`");
foreach ($labels as $label) {
    $data[$label['twitter_id']] = array_fill_keys($keys, "NA");
}
unset($labels);

// Overwrite NA values
foreach ($volunteers as $volunteer) {
    $res = $DatabaseEngine->arrayquery(
        "SELECT `twitter_id`, `verdict` FROM `labels` WHERE `volunteer_id` = :user",
        array(
            ":user"     => $volunteer['volunteer_id']
        )
    );

    foreach ($res as $row) {
        $data[$row['twitter_id']][$volunteer['volunteer_id']] = $row['verdict'];
    }
}

$i = 0;
echo "<table>";
foreach ($data as $twitter_id => $verdicts) {
    echo "<tr>";
    if ($i === 0)
        echo "<th>Twitter_ID</th>";
    else
        echo "<td>{$twitter_id}</td>";
    foreach ($verdicts as $volunteer_id => $verdict) {
        if ($i === 0)
            echo "<th>{$volunteer_id}</th>";
        else
            echo "<td>{$verdict}</td>";
    }
    echo "</tr>";
    $i = 1;
}
echo "</table>";

?>