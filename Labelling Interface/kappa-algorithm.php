<?php

// Initialise
require_once("./init.php");

$volunteers = $DatabaseEngine->arrayquery(
    "SELECT `volunteer_id` FROM `volunteers`"
);

$results = array();
$log = "";
for ($i = 0; $i < count($volunteers); $i++) {
    // Set user 1
    $user1 = $volunteers[$i]['volunteer_id'];

    // Foreach user calc kappa
    for ($k = $i+1; $k < count($volunteers); $k++) {
        $user2 = $volunteers[$k]['volunteer_id'];

        $dom = new DOMDocument();
        $dom->loadHTMLfile("http://miami-nice.co.uk/botornot/kappa.php?user1={$user1}&user2={$user2}");
        $dom->validateOnParse = true;
        $kappa = $dom->getElementById('kappa')->textContent;
        $total = $dom->getElementById('total')->textContent;
        if (is_numeric($kappa)) {
            $kappa = round($kappa, 3);
            $average = ($total/2992) * $kappa;
            $log .= "(({$total}/2992) * {$kappa}) + ";
        } else {
            $average = 0;
        }

        $results[$user1][$user2] = array($kappa, $total, $average);
    }
}

echo "<strong>Cell Format: </strong>KAPPA | TOTAL-INTERSECT-SIZE<br />";
echo "<table><tr><th>&nbsp;</th>";
foreach ($results as $id => $data) {
    echo "<th width='80px'>$id</th>";
}
echo "</tr>";
foreach ($results as $id => $data) {
    echo "<tr><th>$id</th>";

    // Create disabled cells
    $numDisabled = 28 - count($data);
    for ($i = 0; $i < $numDisabled; $i++) {
        echo "<td bgcolor='grey'>&nbsp;</td>";
    }

    // Add the data
    foreach ($data as $subuser) {
        if (is_numeric($subuser[1])) {
            $weight = $subuser[1] * $subuser[0];
        }
        echo "<td>{$subuser[0]} | {$subuser[1]}</td>";
    }
    echo "</tr>";
}
echo "</table>";

$bigtotal = 0;
$averageKappa = 0;
foreach ($results as $id => $data) {
    foreach ($data as $subuser) {
        if (is_numeric($subuser[0])) {
            $bigtotal += $subuser[1];

            $averageKappa += $subuser[2];
        }
    }
}
echo "<strong>Total intersect size: </strong>{$bigtotal}<br />";
echo "<strong>Average Kappa: </strong>{$averageKappa}</br />";
echo "<strong>Calculation to get average kappa: </strong><br />" . rtrim($log, " + ") . "<br />";
//var_dump($results);

?>