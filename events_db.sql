-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gazdă: 127.0.0.1
-- Timp de generare: ian. 19, 2025 la 12:42 PM
-- Versiune server: 10.4.32-MariaDB
-- Versiune PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `events_db`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `client`
--

CREATE TABLE `client` (
  `ClientID` int(10) NOT NULL,
  `NumeClient` varchar(20) NOT NULL,
  `PrenumeClient` varchar(20) NOT NULL,
  `NrTelefonClient` varchar(10) NOT NULL,
  `EmailClient` varchar(50) NOT NULL,
  `AdresaClient` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `client`
--

INSERT INTO `client` (`ClientID`, `NumeClient`, `PrenumeClient`, `NrTelefonClient`, `EmailClient`, `AdresaClient`) VALUES
(1, 'Popescu', 'Matei-Andrei', '0733648022', 'popescumatei@gmail.com', 'Strada Primaverii, 290'),
(2, 'Ionescu', 'Andreea-Alina', '0724920288', 'andreeaionescu@gmail.com', 'Strada Florilor 180'),
(3, 'Andreescu', 'Mihai', '0728401849', 'mihai@gmail.com', 'Strada Tei 453'),
(4, 'Alexandrescu', 'Miruna', '0720184302', 'miruna@gmail.com', 'Strada Ion Barbu 472'),
(5, 'Mateescu', 'Alexandra', '0710342374', 'alexandra@yahoo.com', 'Strada Dimitrie Cantemir 462'),
(6, 'Despoiu', 'Sabina', '0735291034', 'sabinadespoiu@gmail.com', 'Strada Crizantemelor, 280'),
(7, 'Andrei', 'Antonia-Stefania', '0745830273', 'andreiantonia@gmail.com', 'Strada Obor, 143'),
(8, 'Ciuca', 'Alexia', '0784532037', 'alexiaciuca@gmail.com', 'Strada Teilor, 324'),
(9, 'Mihalache', 'Adriana', '0745382074', 'adrianamihalache@gmail.com', 'Strada Lalelelor, 533'),
(10, 'Militaru', 'Bianca', '0733648022', 'biancamilitaru@gmail.com', 'Strada Nicolae Titulescu, 34'),
(13, 'Tora', 'Florentina', '0758302722', 'florentinatora@gmail.com', 'Strada Plopilor, 289');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `eveniment`
--

CREATE TABLE `eveniment` (
  `EvenimentID` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `OrganizatorID` int(11) NOT NULL,
  `LocatieID` int(11) NOT NULL,
  `NumeEveniment` varchar(30) NOT NULL,
  `DataEveniment` date NOT NULL,
  `CostEveniment` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `eveniment`
--

INSERT INTO `eveniment` (`EvenimentID`, `ClientID`, `OrganizatorID`, `LocatieID`, `NumeEveniment`, `DataEveniment`, `CostEveniment`) VALUES
(1, 2, 1, 2, 'Aniversare 20 ani casnicie', '2025-11-03', 10000),
(2, 5, 2, 4, 'Botez', '2026-12-28', 30000),
(3, 1, 3, 4, 'Casatorie (mariaj)', '2026-07-25', 70000),
(4, 3, 4, 1, 'Majorat', '2024-12-30', 15000),
(5, 4, 1, 3, 'Zi onomastica', '2025-02-14', 15000),
(10, 7, 4, 5, 'Majorat', '2025-10-16', 2000),
(11, 7, 3, 3, 'Zi de nastere', '2025-04-04', 6000),
(12, 10, 2, 2, 'Aniversare', '2025-08-11', 4000),
(15, 8, 5, 5, 'Aniversare 21 ani', '2025-05-11', 4000),
(16, 8, 4, 3, 'Revelion', '2025-12-31', 13000),
(23, 6, 3, 1, 'Eveniment Funerar', '2025-02-15', 4000),
(24, 9, 2, 4, 'Zi de nastere', '2025-11-17', 15000);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `evenimentfurnizor`
--

CREATE TABLE `evenimentfurnizor` (
  `EvenimentID` int(11) NOT NULL,
  `FurnizorID` int(11) NOT NULL,
  `RolFurnizor` varchar(50) NOT NULL,
  `DataColaborare` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `evenimentfurnizor`
--

INSERT INTO `evenimentfurnizor` (`EvenimentID`, `FurnizorID`, `RolFurnizor`, `DataColaborare`) VALUES
(1, 3, 'Meniuri', '2023-07-03'),
(4, 5, 'Recuzita', '2022-07-28'),
(3, 4, 'Candy bar', '2022-09-04'),
(3, 2, 'Muzica', '2023-03-29'),
(1, 2, 'Baloane, artificii, confetti', '2023-08-23'),
(10, 4, '', '0000-00-00'),
(11, 3, 'Recuzita', '2025-01-07'),
(12, 5, 'Meniuri', '2025-01-10'),
(13, 2, 'Meniuri si muzica', '2025-01-14'),
(14, 4, 'Meniuri si muzica', '2025-01-16'),
(15, 1, 'Meniuri si muzica', '2025-01-11'),
(5, 3, 'Meniuri', '2025-01-06'),
(16, 5, 'Recuzita', '2025-01-05'),
(2, 4, 'Candy bar', '2025-01-04'),
(23, 4, 'Meniuri si muzica bocitoare', '2025-01-10');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `facturaeveniment`
--

CREATE TABLE `facturaeveniment` (
  `FacturaID` int(10) NOT NULL,
  `EvenimentID` int(10) NOT NULL,
  `TotalPlata` double NOT NULL,
  `DeadlinePlata` date NOT NULL,
  `DataEmitereFactura` date NOT NULL,
  `Status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `facturaeveniment`
--

INSERT INTO `facturaeveniment` (`FacturaID`, `EvenimentID`, `TotalPlata`, `DeadlinePlata`, `DataEmitereFactura`, `Status`) VALUES
(1, 3, 70000, '2025-01-08', '2024-12-05', 'Neplatit'),
(2, 5, 7000, '2025-01-07', '2024-12-23', 'Neplatit'),
(3, 2, 30000, '2025-01-07', '2024-12-17', 'Platit'),
(4, 4, 15000, '2025-01-10', '2024-12-14', 'Neplatit'),
(5, 1, 10000, '2025-01-09', '2024-12-09', 'Platit'),
(7, 10, 32000, '2025-02-08', '2025-01-09', 'Neplatit'),
(8, 11, 14000, '2025-02-01', '2025-01-09', 'Platit'),
(9, 12, 12000, '2025-02-06', '2025-01-09', 'Neplatit'),
(10, 15, 15000, '2025-02-04', '2025-01-09', 'Platit'),
(11, 16, 20000, '2025-02-05', '2025-01-09', 'Platit');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `furnizor`
--

CREATE TABLE `furnizor` (
  `FurnizorID` int(10) NOT NULL,
  `EvenimentID` int(10) NOT NULL,
  `NumeFurnizor` varchar(30) NOT NULL,
  `NrTelefonFurnizor` varchar(20) NOT NULL,
  `EmailFurnizor` varchar(30) NOT NULL,
  `ServiciuFurnizor` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `furnizor`
--

INSERT INTO `furnizor` (`FurnizorID`, `EvenimentID`, `NumeFurnizor`, `NrTelefonFurnizor`, `EmailFurnizor`, `ServiciuFurnizor`) VALUES
(1, 2, 'Popescu Mihai', '0772391028', 'mihaipopescu@gmail.com', 'Baloane, artificii, confetti'),
(2, 1, 'Luca Andreea', '0715249369', 'lucandreea@gmail.com', 'Muzica'),
(3, 5, 'Mihai Elena', '0723610837', 'elenamihai@gmail.com', 'Meniuri'),
(4, 4, 'Andrei Larisa', '0753201872', 'larisaandrei@gmail.com', 'Candy bar'),
(5, 3, 'Mihaescu Denisa', '0732013649', 'denisa@gmail.com', 'Recuzita (mese, scaune persona');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `locatie`
--

CREATE TABLE `locatie` (
  `LocatieID` int(10) NOT NULL,
  `NumeLocatie` varchar(30) NOT NULL,
  `AdresaLocatie` varchar(50) NOT NULL,
  `CapacitateLocatie` int(10) NOT NULL,
  `ContactLocatie` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `locatie`
--

INSERT INTO `locatie` (`LocatieID`, `NumeLocatie`, `AdresaLocatie`, `CapacitateLocatie`, `ContactLocatie`) VALUES
(1, 'Restaurant Unirea', 'Strada Crizantemelor, 472', 5000, '0291836482'),
(2, 'Restaurant Voila', 'Strada Crinilor, 352', 3000, '0241301373'),
(3, 'Terasa L\'Orosccoppo', 'Strada Ion Mihalache, 274', 6000, '0240148620'),
(4, 'Restaurant Trattoria', 'Strada Ion Barbu, 281', 9000, '0246291382'),
(5, 'Restaurant Chateau Blanc', 'Strada Sperantei, 179', 10000, '0283164022');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `usertype` varchar(50) NOT NULL DEFAULT 'user',
  `ClientID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `usertype`, `ClientID`) VALUES
(1, 'AdminNew', '1adminnew', 'admin', NULL),
(2, 'User', '1234', 'user', NULL),
(3, 'Matei_andrei', '1mateiandrei', 'user', 1),
(5, 'Andreea_alina', '1andreeaionescu', 'user', 2),
(6, 'Mihai', '1mihaiantonescu', 'user', 3),
(7, 'Miruna', '1mirunalexandrescu', 'user', 4),
(8, 'Alexandra', '1alexandramateescu', 'user', 5),
(9, 'Sabina', '1sabina', 'user', 6),
(10, 'Antonia_stefania', '1antoniastefania', 'user', 7),
(11, 'Alexia', '1alexiaciuca', 'user', 8),
(12, 'Adriana', '1adrianamihalache', 'user', 9),
(13, 'Bianca', '1biancamilitaru', 'user', 10),
(14, 'Florentina', '1florentinatora', 'user', 13);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `organizator`
--

CREATE TABLE `organizator` (
  `OrganizatorID` int(10) NOT NULL,
  `NumeOrganizator` varchar(20) NOT NULL,
  `PrenumeOrganizator` varchar(20) NOT NULL,
  `NrTelefonOrganizator` varchar(20) NOT NULL,
  `EmailOrganizator` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `organizator`
--

INSERT INTO `organizator` (`OrganizatorID`, `NumeOrganizator`, `PrenumeOrganizator`, `NrTelefonOrganizator`, `EmailOrganizator`) VALUES
(1, 'Popescu', 'Andrei', '0735271937', 'andrei@gmail.com'),
(2, 'Popa', 'Maria', '0746622003', 'mariapopa@gmail.com'),
(3, 'Lucescu', 'Madalina', '0725301833', 'madalina@gmail.com'),
(4, 'Vlad', 'Ana', '0736820183', 'anavlad@gmail.com'),
(5, 'Ilie', 'Mariana', '0735171037', 'marianailie@gmail.com');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `serviciifurnizor`
--

CREATE TABLE `serviciifurnizor` (
  `ServiciiFurnizorID` int(11) NOT NULL,
  `FurnizorID` int(11) NOT NULL,
  `NumeServiciu` varchar(30) NOT NULL,
  `CostServiciu` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `serviciifurnizor`
--

INSERT INTO `serviciifurnizor` (`ServiciiFurnizorID`, `FurnizorID`, `NumeServiciu`, `CostServiciu`) VALUES
(1, 3, 'Meniuri', 4000),
(2, 5, 'Recuzita', 6000),
(3, 2, 'Muzica', 1500),
(4, 4, 'Candy bar', 2000),
(5, 1, 'Baloane, artificii, confetti', 2500);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `support_messages`
--

INSERT INTO `support_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Maria Lacraru', 'lacrarumaria@gmail.com', 'Nu functioneaza pagina de setari.', '2024-12-29 09:26:54'),
(2, 'Sabina Despoiu', 'sabinadespoiu@gmail.com', 'Nu mi-ati organizat evenimentul funerar!!!', '2025-01-09 22:19:01');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexuri pentru tabele `eveniment`
--
ALTER TABLE `eveniment`
  ADD PRIMARY KEY (`EvenimentID`);

--
-- Indexuri pentru tabele `facturaeveniment`
--
ALTER TABLE `facturaeveniment`
  ADD PRIMARY KEY (`FacturaID`);

--
-- Indexuri pentru tabele `furnizor`
--
ALTER TABLE `furnizor`
  ADD PRIMARY KEY (`FurnizorID`);

--
-- Indexuri pentru tabele `locatie`
--
ALTER TABLE `locatie`
  ADD PRIMARY KEY (`LocatieID`);

--
-- Indexuri pentru tabele `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `organizator`
--
ALTER TABLE `organizator`
  ADD PRIMARY KEY (`OrganizatorID`);

--
-- Indexuri pentru tabele `serviciifurnizor`
--
ALTER TABLE `serviciifurnizor`
  ADD PRIMARY KEY (`ServiciiFurnizorID`);

--
-- Indexuri pentru tabele `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `client`
--
ALTER TABLE `client`
  MODIFY `ClientID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pentru tabele `eveniment`
--
ALTER TABLE `eveniment`
  MODIFY `EvenimentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pentru tabele `facturaeveniment`
--
ALTER TABLE `facturaeveniment`
  MODIFY `FacturaID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pentru tabele `furnizor`
--
ALTER TABLE `furnizor`
  MODIFY `FurnizorID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pentru tabele `locatie`
--
ALTER TABLE `locatie`
  MODIFY `LocatieID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pentru tabele `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pentru tabele `organizator`
--
ALTER TABLE `organizator`
  MODIFY `OrganizatorID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pentru tabele `serviciifurnizor`
--
ALTER TABLE `serviciifurnizor`
  MODIFY `ServiciiFurnizorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pentru tabele `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pentru tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
