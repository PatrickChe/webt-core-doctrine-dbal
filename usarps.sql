DROP DATABASE IF EXISTS usarps;

CREATE DATABASE IF NOT EXISTS usarps
DEFAULT CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE Participant (
    PK_Participant_ID INT PRIMARY KEY,
    First_Name VARCHAR(50),
    Last_Name VARCHAR(50),
    Nickname VARCHAR(50)
);

CREATE TABLE Match (
    PK_Match_ID INT PRIMARY KEY,
    Participant1 INT NOT NULL,
    Symbol1 ENUM('Rock', 'Paper', 'Scissors') NOT NULL,
    Participant2 INT NOT NULL,
    Symbol2 ENUM('Rock', 'Paper', 'Scissors') NOT NULL,
    Match_Date DATETIME
);

INSERT INTO Participant (PK_Participant_ID, First_Name, Last_Name, Nickname)
VALUES
    (1, 'John', 'Doe', 'Rocky'),
    (2, 'Jane', 'Smith', 'PaperQueen'),
    (3, 'Michael', 'Johnson', 'ScissorsMaster'),
    (4, 'Emily', 'Brown', 'RockStar');

INSERT INTO Match (PK_Match_ID, Participant1, Symbol1, Participant2, Symbol2, Match_Date)
VALUES
    (1, 1, 'Rock', 2, 'Scissors', '2008-06-01 10:00:00'),
    (2, 3, 'Paper', 4, 'Rock', '2008-06-02 11:30:00'),
    (3, 1, 'Scissors', 3, 'Paper', '2008-06-03 09:45:00'),
    (4, 2, 'Rock', 4, 'Paper', '2008-06-04 13:15:00');

