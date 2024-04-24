<?php
require_once 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connectionParams = [
    'dbname' => 'usarps',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$conn = DriverManager::getConnection($connectionParams);

$sql = "SELECT Game.*, P1.First_Name AS P1_First_Name, P2.First_Name AS P2_First_Name, P1.Nickname AS P1_Nickname, P2.Nickname AS P2_Nickname, P1.Last_Name AS P1_Last_Name, P2.Last_Name AS P2_Last_Name
        FROM Game
        JOIN Participant P1 ON P1.PK_Participant_ID = Game.Participant1
        JOIN Participant P2 ON P2.PK_Participant_ID = Game.Participant2";

$stmt = $conn->executeQuery($sql);


$htmlTemplateGame = file_get_contents('game.html');
$htmlTemplate = file_get_contents('matches.html');

$htmlOut = '';
$htmlFin = '';

while (($row = $stmt->fetchAssociative()) !== false) {
    $html = $htmlTemplateGame;
    $html = str_replace("{round}", $row['PK_Match_ID'], $html);
    $html = str_replace("{player1}", $row['P1_First_Name'] . ' "' . $row['P1_Nickname'] . '" ' . $row['P1_Last_Name'], $html);
    $html = str_replace("{symbol1}", $row['Symbol1'], $html);
    $html = str_replace("{player2}", $row['P2_First_Name'] . ' "' . $row['P2_Nickname'] . '" ' . $row['P2_Last_Name'], $html);
    $html = str_replace("{symbol2}", $row['Symbol2'], $html);
    $html = str_replace("{date}", $row['Match_Date'], $html);
    $htmlOut .= $html;
}

$htmlFin = $htmlTemplate;
$htmlFin = str_replace("{matches}", $htmlOut, $htmlFin);
echo $htmlFin;