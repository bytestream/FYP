<?php

// Initialise
require_once("./init.php");

if (isset($_GET['user1']) && isset($_GET['user2'])) {

    // Lookup data
    $HumanAgree = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Human%" AND d2.verdict LIKE "%Human%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $BotAgree = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Bot%" AND d2.verdict LIKE "%Bot%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $DiscardAgree = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Discard%" AND d2.verdict LIKE "%Discard%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $HumanBot = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Human%" AND d2.verdict LIKE "%Bot%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $BotHuman = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Bot%" AND d2.verdict LIKE "%Human%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $HumanDiscard = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Human%" AND d2.verdict LIKE "%Discard%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $DiscardHuman = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Discard%" AND d2.verdict LIKE "%Human%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $DiscardBot = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Discard%" AND d2.verdict LIKE "%Bot%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    $BotDiscard = $DatabaseEngine->arrayquery(
        'SELECT d1.twitter_id, d1.verdict AS Kieran, d2.verdict AS Mum
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2 AND
         d1.verdict LIKE "%Bot%" AND d2.verdict LIKE "%Discard%"',
         array(
            ":user1"    => $_GET['user1'],
            ":user2"    => $_GET['user2']
        )
    );

    // Calc totals
    $totalHumanCol = count($HumanAgree) + count($BotHuman) + count($DiscardHuman);
    $totalBotCol   = count($HumanBot) + count($BotAgree) + count($DiscardBot);
    $totalDiscardCol = count($HumanDiscard) + count($BotDiscard) + count($DiscardAgree);

    $totalHumanRow = count($HumanAgree) + count($HumanBot) + count($HumanDiscard);
    $totalBotRow = count($BotHuman) + count($BotAgree) + count($BotDiscard);
    $totalDiscardRow = count($DiscardHuman) + count($DiscardBot) + count($DiscardAgree);

    $totalBoth = $totalHumanRow + $totalBotRow + $totalDiscardRow;

    // Create matrix
    $matrix = array(
        array(count($HumanAgree), count($HumanBot), count($HumanDiscard), $totalHumanRow),
        array(count($BotHuman), count($BotAgree), count($BotDiscard), $totalBotRow),
        array(count($DiscardHuman), count($DiscardBot), count($DiscardAgree), $totalDiscardRow),
        array($totalHumanCol, $totalBotCol, $totalDiscardCol, $totalBoth)
    );

    // agreements
    $agreementHuman = $matrix[3][0] * $matrix[0][3] / $matrix[3][3];
    $agreementBot   = $matrix[3][1] * $matrix[1][3] / $matrix[3][3];
    $agreementDiscard = $matrix[3][2] * $matrix[2][3] / $matrix[3][3];

    $agreementTotal = count($HumanAgree) + count($BotAgree) + count($DiscardAgree);
    $byChanceTotal = $agreementHuman + $agreementBot + $agreementDiscard;

    $kappa = ($agreementTotal - $byChanceTotal) / ($matrix[3][3] - $byChanceTotal);
    if (($matrix[3][3] - $byChanceTotal) === 0) {
        $kappa = "E";
    }

    echo "
    <!DOCTYPE html>
    <html>
    <head>
    <title>Kappa</title>
    </head>
    <body>
    <pre>
        <table>
            <tr>
                <th></th>
                <th>Human</th>
                <th>Bot</th>
                <th>Discard</th>
                <th>Total</th>
            </tr>
            <tr>
                <th>Human</th>
                <td>" . count($HumanAgree) . "</td>
                <td>" . count($HumanBot) . "</td>
                <td>" . count($HumanDiscard) . "</td>
                <td>" . $totalHumanRow . "</td>
            </tr>
            <tr>
                <th>Bot</th>
                <td>" . count($BotHuman) . "</td>
                <td>" . count($BotAgree) . "</td>
                <td>" . count($BotDiscard) . "</td>
                <td>" . $totalBotRow . "</td>
            </tr>
            <tr>
                <th>Discard</th>
                <td>" . count($DiscardHuman) . "</td>
                <td>" . count($DiscardBot) . "</td>
                <td>" . count($DiscardAgree) . "</td>
                <td>" . $totalDiscardRow . "</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>" . $totalHumanCol . "</td>
                <td>" . $totalBotCol . "</td>
                <td>" . $totalDiscardCol . "</td>
                <td>" . $totalBoth . "</td>
            </tr>
        </table><br />
    ";

    echo "<strong>Kappa: </strong><span id='kappa'>{$kappa}</span><br />";
    echo "<strong>Intersect total: </strong><span id='total'>{$totalBoth}</span><br />";

    /* Calculate the weight
    $totalUsers = $DatabaseEngine->arrayquery(
        "SELECT d1.twitter_id
         FROM `labels` AS d1
         JOIN `labels` AS d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id = :user2",
         array(
            ":user1" => $_GET['user1'],
            ":user2" => $_GET['user2']
        )
    );
    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($totalUsers));
    $listofIDs = implode(", ", iterator_to_array($it, false));

    var_dump(
        "SELECT d1.volunteer_id
         FROM `labels` as d1
         JOIN `labels` as d2 ON d1.twitter_id = d2.twitter_id AND d1.volunteer_id = :user1 AND d2.volunteer_id2 = :user2
         WHERE d1.twitter_id IN($listofIDs)"
    );
    */

    echo "</body></html>";

/*
    //set POST variables
    $url = 'http://graphpad.com/quickcalcs/kappa2/';
    $fields = array(
        'K'     => '3',
        'R01C01'=> count($HumanAgree),
        'R01C02'=> count($HumanBot),
        'R01C03'=> count($HumanDiscard),
        'R02C01'=> count($BotHuman),
        'R02C02'=> count($BotAgree),
        'R02C03'=> count($BotDiscard),
        'R03C01'=> count($DiscardHuman),
        'R03C02'=> count($DiscardBot),
        'R03C03'=> count($DiscardAgree)
    );

    //url-ify the data for the POST
    $fields_string = "";
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    //curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    //echo htmlspecialchars($result);

    // Output the result
    //preg_match('/(<table class="resultTable">.*?<\/table>.*?<\/table>)/ms', $result, $matches);

    //echo $matches[0];
    */
    exit();
}

?>

<html>
    <head>
    </head>
    <body>
        <h2>Select two users</h2>
        <form action="kappa.php" method="GET">
            <?php
                $users = $DatabaseEngine->arrayquery(
                    "SELECT `volunteer_id` FROM `volunteers`"
                );
            ?>
            User 1: <select name="user1">
            <?php foreach ($users as $user)
                echo "<option>{$user['volunteer_id']}</option>";
            ?>
            </select><br />
            User 2: <select name="user2">
            <?php foreach ($users as $user)
                echo "<option>{$user['volunteer_id']}</option>";
            ?>
            </select><br />
            <input type="submit" value="Get Data" />
        </form>
    </body>
</html>