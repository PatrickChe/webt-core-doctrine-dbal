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
$queryBuilder = $conn->createQueryBuilder();

$queryBuilder
    ->select('*')
    ->from('Participant');

$stmt = $conn->executeQuery($queryBuilder);

$options = '';
while (($row = $stmt->fetchAssociative()) !== false) {
    $options .= "<option value=\"{$row['PK_Participant_ID']}\">{$row['First_Name']} {$row['Last_Name']} ({$row['Nickname']})</option>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addParticipant'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $nickname = $_POST['nickname'];

        $queryBuilder->select('COUNT(PK_Participant_ID)')
            ->from('Participant');

        $stmt = $conn->executeQuery($queryBuilder);
        $count = $stmt->fetchOne();

        $queryBuilder
            ->insert('Participant')
            ->values([
                'PK_Participant_ID' => $queryBuilder->expr()->literal($count + 1),
                'First_Name' => $queryBuilder->expr()->literal($firstName),
                'Last_Name' => $queryBuilder->expr()->literal($lastName),
                'Nickname' => $queryBuilder->expr()->literal($nickname)
            ]);

        $conn->executeStatement($queryBuilder);
    } elseif (isset($_POST['addGame'])) {
        $participant1 = $_POST['participant1'];
        $symbol1 = $_POST['symbol1'];
        $participant2 = $_POST['participant2'];
        $symbol2 = $_POST['symbol2'];
        $matchDate = $_POST['matchDate'];

        $queryBuilder->select('COUNT(PK_Match_ID)')
            ->from('Game');

        $stmt = $conn->executeQuery($queryBuilder);
        $count = $stmt->fetchOne();

        $queryBuilder
            ->insert('Game')
            ->values([
                'PK_Match_ID' => $queryBuilder->expr()->literal($count + 1),
                'Participant1' => $queryBuilder->expr()->literal($participant1),
                'Symbol1' => $queryBuilder->expr()->literal($symbol1),
                'Participant2' => $queryBuilder->expr()->literal($participant2),
                'Symbol2' => $queryBuilder->expr()->literal($symbol2),
                'Match_Date' => $queryBuilder->expr()->literal($matchDate)
            ]);

        $conn->executeStatement($queryBuilder);
    }
}

echo <<<HT
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Entry Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-4">
    <h2 class="text-2xl font-bold mb-4 text-center">Add Participant</h2>
    <form method="post" class="max-w-md mx-auto">
        <label for="firstName" class="block mb-2">First Name:</label>
        <input type="text" id="firstName" name="firstName" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">

        <label for="lastName" class="block mb-2">Last Name:</label>
        <input type="text" id="lastName" name="lastName" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">

        <label for="nickname" class="block mb-2">Nickname:</label>
        <input type="text" id="nickname" name="nickname" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">

        <input type="submit" name="addParticipant" value="Add Participant"
            class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">
    </form>

    <h2 class="text-2xl font-bold mt-8 mb-4 text-center">Add Game</h2>
    <form method="post" class="max-w-md mx-auto">
        <label for="participant1" class="block mb-2">Participant 1:</label>
        <select id="participant1" name="participant1" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Participant</option>
            $options
        </select>

        <label for="symbol1" class="block mb-2">Symbol 1:</label>
        <select id="symbol1" name="symbol1" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Symbol</option>
            <option value="Rock">Rock</option>
            <option value="Paper">Paper</option>
            <option value="Scissors">Scissors</option>
        </select>

        <label for="participant2" class="block mb-2">Participant 2:</label>
        <select id="participant2" name="participant2" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Participant</option>
            $options
        </select>

        <label for="symbol2" class="block mb-2">Symbol 2:</label>
        <select id="symbol2" name="symbol2" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Symbol</option>
            <option value="Rock">Rock</option>
            <option value="Paper">Paper</option>
            <option value="Scissors">Scissors</option>
        </select>

        <label for="matchDate" class="block mb-2">Match Date:</label>
        <input type="datetime-local" id="matchDate" name="matchDate" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">

        <input type="submit" name="addGame" value="Add Game"
            class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">
    </form>

</body>

</html>

HT;
