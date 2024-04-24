DROP DATABASE IF EXISTS usarps;

CREATE DATABASE IF NOT EXISTS usarps
DEFAULT CHARACTER SET utf8
COLLATE utf8_general_ci;

use usarps;

CREATE TABLE Participant (
    PK_Participant_ID INT PRIMARY KEY,
    First_Name VARCHAR(50),
    Last_Name VARCHAR(50),
    Nickname VARCHAR(50)
);

CREATE TABLE Game (
    PK_Match_ID INT PRIMARY KEY,
    Participant1 INT NOT NULL,
    Symbol1 ENUM('Rock', 'Paper', 'Scissors') NOT NULL,
    Participant2 INT NOT NULL,
    Symbol2 ENUM('Rock', 'Paper', 'Scissors') NOT NULL,
    Match_Date DATETIME
);

INSERT INTO Participant (PK_Participant_ID, First_Name, Last_Name, Nickname)
VALUES 
    (1, 'Peter', 'Schulteis', 'Slinghshot'),
    (2, 'Mark', 'Chironna', 'Cottonwood'),
    (3, 'Rob', 'Krueger', 'Blue Star'),
    (4, 'Shannon', 'Johnson', 'The Cannon'),
    (5, 'Guy', 'Rich', 'Sizzle'),
    (6, 'Marcus', 'Scimé', 'Lionheart'),
    (7, 'Craig', 'Hamlin', 'The Monster'),
    (8, 'Steve', 'Mullins', 'Twinkie'),
    (9, 'Robert', 'Crawford', 'Dr. Hugenstein'),
    (10, 'Josh', 'Wellman', 'The Pest');

INSERT INTO `Game` (PK_Match_ID, Participant1, Symbol1, Participant2, Symbol2, Match_Date)
VALUES 
    (1, 1, 'Rock', 2, 'Scissors', '2024-03-20 10:00:00'),
    (2, 3, 'Paper', 4, 'Rock', '2024-03-20 10:15:00'),
    (3, 5, 'Scissors', 6, 'Paper', '2024-03-20 10:30:00'),
    (4, 7, 'Rock', 8, 'Scissors', '2024-03-20 10:45:00'),
    (5, 9, 'Paper', 10, 'Rock', '2024-03-20 11:00:00');

