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

while (($row = $stmt->fetchAssociative()) !== false) {
    echo $row['Match_Date'];
}