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

$sql = "SELECT * FROM Game";
$stmt = $conn->executeQuery($sql);


$htmlTemplateGame = file_get_contents('game.html');

while (($row = $stmt->fetchAssociative()) !== false) {
    $html = $htmlTemplate;
    $html = str_replace("{player1}", $row[''], $html);
    $html = str_replace("{hotel_description}", $hotel['description'], $html);
    echo $row['Match_Date'];
}
