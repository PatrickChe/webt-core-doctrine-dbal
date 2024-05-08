<?php
require_once 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

// Database connection parameters
$connectionParams = [
    'dbname' => 'usarps',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

// Establishing database connection
$conn = DriverManager::getConnection($connectionParams);
$queryBuilder = $conn->createQueryBuilder();

// Selecting all participants from the database
$queryBuilder
    ->select('*')
    ->from('Participant');

$stmt = $conn->executeQuery($queryBuilder);

// Generating options for participant dropdown list
$options = '';
while (($row = $stmt->fetchAssociative()) !== false) {
    $options .= "<option value=\"{$row['PK_Participant_ID']}\">{$row['First_Name']} {$row['Last_Name']} ({$row['Nickname']})</option>";
}

// Selecting distinct games from the database
$queryBuilder
    ->select('DISTINCT g.PK_Match_ID', 'P1.Nickname AS P1_Nickname', 'P2.Nickname AS P2_Nickname')
    ->from('Game', 'g')
    ->join('g', 'Participant', 'P1', 'P1.PK_Participant_ID = g.Participant1', 'P1')
    ->join('g', 'Participant', 'P2', 'P2.PK_Participant_ID = g.Participant2', 'P2');

$stmt = $conn->executeQuery($queryBuilder);

// Generating options for game dropdown list
$games = '';
while (($row = $stmt->fetchAssociative()) !== false) {
    $games .= "<option value=\"{$row['PK_Match_ID']}\">{$row['P1_Nickname']} vs {$row['P2_Nickname']}</option>";
}

// Handling form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addParticipant'])) {
        // Adding a new participant
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $nickname = $_POST['nickname'];

        // Counting existing participants
        $queryBuilder
            ->select('COUNT(Participant.PK_Participant_ID)')
            ->from('Participant');

        $stmt = $conn->executeQuery($queryBuilder);
        $count = $stmt->fetchOne();

        // Inserting new participant into the database
        // Using expr()->literal() for correct formating in the INSERT query and prevention of sql-injection attacks.
        $queryBuilder
            ->insert('Participant')
            ->values([
                'PK_Participant_ID' => $queryBuilder->expr()->literal($count + 1),
                'First_Name' => $queryBuilder->expr()->literal($firstName),
                'Last_Name' => $queryBuilder->expr()->literal($lastName),
                'Nickname' => $queryBuilder->expr()->literal($nickname)
            ]);

        $conn->executeStatement($queryBuilder);

        header('Location: manageData.php');
        exit();
    } elseif (isset($_POST['addGame'])) {
        // Adding a new game
        $participant1 = $_POST['participant1'];
        $symbol1 = $_POST['symbol1'];
        $participant2 = $_POST['participant2'];
        $symbol2 = $_POST['symbol2'];
        $matchDate = $_POST['matchDate'];

        // Counting existing games
        $queryBuilder
            ->select('COUNT(Game.PK_Match_ID)')
            ->from('Game');

        $stmt = $conn->executeQuery($queryBuilder);
        $count = $stmt->fetchOne();

        // Inserting new game into the database
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

        header('Location: manageData.php');
        exit();
    } elseif (isset($_POST['deleteParticipant'])) {
        // Deleting a participant
        $participantId = $_POST['participantId'];
        try {
            $queryBuilder
                ->delete('Participant')
                ->where('PK_Participant_ID = ' . $participantId);

            $conn->executeStatement($queryBuilder);
            header('Location: manageData.php');
            exit();
        } catch (Exception $e) {
            echo "Der Spieler kann nicht gelöscht werden, da er in einem Spiel involviert ist.";
        }
    } elseif (isset($_POST['deleteGame'])) {
        // Deleting a game
        $gameId = $_POST['gameId'];

        $queryBuilder
            ->delete('Game')
            ->where('PK_Match_ID = ' . $gameId)
            ->setParameter('id', $gameId);

        $conn->executeStatement($queryBuilder);
        header('Location: manageData.php');
        exit();
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
    <h2 class="text-2xl font-bold mb-4 text-center mt-4">Delete Participant</h2>
    <form method="post" class="max-w-md mx-auto">
        <label for="participantToDelete" class="block mb-2">Select Participant to Delete:</label>
        <select id="participantToDelete" name="participantId" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Participant</option>
            $options
        </select>
        <input type="submit" name="deleteParticipant" value="Delete Participant"
            class="w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">
    </form>

    <h2 class="text-2xl font-bold mt-8 mb-4 text-center">Delete Game</h2>
    <form method="post" class="max-w-md mx-auto">
        <label for="gameToDelete" class="block mb-2">Select Game to Delete:</label>
        <select id="gameToDelete" name="gameId" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 focus:outline-none focus:ring focus:border-blue-300">
            <option value="">Select Game</option>
            $games
        </select>
        <input type="submit" name="deleteGame" value="Delete Game"
        class="w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300">
    </form>
</body>
</html>
HT;
