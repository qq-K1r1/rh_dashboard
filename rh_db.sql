-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2024 at 04:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rh_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `loginUser` (IN `username` VARCHAR(255))   BEGIN
    DECLARE auth_id INT;
    DECLARE user_id INT;
    DECLARE full_name VARCHAR(255);
    DECLARE user_role VARCHAR(50);
    DECLARE stored_password_hash VARCHAR(255);

    -- Check for RH Manager
    SELECT a.AuthentificationID, r.RHManagerID, r.NomPrenom, a.Role, a.Mot_de_passe 
    INTO auth_id, user_id, full_name, user_role, stored_password_hash
    FROM Authentification a
    INNER JOIN RHManager r ON a.AuthentificationID = r.AuthentificationID
    WHERE a.Identifiant = username
    LIMIT 1;

    -- Check if RH Manager is found
    IF auth_id IS NULL THEN
        -- Check for Employé
        SELECT a.AuthentificationID, e.EmployéID, e.NomPrenom, a.Role, a.Mot_de_passe 
        INTO auth_id, user_id, full_name, user_role, stored_password_hash
        FROM Authentification a
        INNER JOIN Employé e ON a.AuthentificationID = e.AuthentificationID
        WHERE a.Identifiant = username
        LIMIT 1;
    END IF;

    -- Return user data
    SELECT auth_id AS AuthentificationID, user_id AS UserID, full_name AS FullName, user_role AS Role, stored_password_hash AS PasswordHash;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `authentification`
--

CREATE TABLE `authentification` (
  `AuthentificationID` int(11) NOT NULL,
  `Identifiant` varchar(255) DEFAULT NULL,
  `Mot_de_passe` varchar(255) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authentification`
--

INSERT INTO `authentification` (`AuthentificationID`, `Identifiant`, `Mot_de_passe`, `Role`) VALUES
(35, 'manager', '$2y$10$T2IwncYj74i4252i.8bse.KZclKCIX/IeMGRFvpY5ENKLet2xSY5K', 'RH Manager'),
(36, 'employe', '$2y$10$h81QSUSVHSfyqXZvWnINa.i4dejUNebi68GB.L0QIqdGv0QZhqwBy', 'employe'),
(37, 'walide', '$2y$10$NxnVDtgjYw9yNiZg0A/aOejQ2jmTT09Pc1yoxGhlOFGBXt5NJPrtG', 'Employé'),
(38, 'admin', '$2y$10$/9fo2UGnKRe8IkP59JRG9OrocQkhFWbybLuV4QnoFHOrN3UAGSdve', 'RH Manager'),
(39, 'emp', '$2y$10$V0zVmc7ykW5h2ixwQm9bue2AWfh/avfHvFMhXaj7E0tqkOQz0d0h.', 'Employé'),
(40, 'emptest', '$2y$10$niG6XWpERaLX.lpmmwNyOOUuX0dpKVqvR3J3CJmXNDR0Tv0uWIGtG', 'Employé'),
(41, 'emp-01', '$2y$10$9NBPQ1uBUaqLSbLCfNSugOulVFw38ORIv8sDqD5c7famd.lZ3Obwe', 'Employé'),
(42, 'MNG-001', '$2y$10$kMYrZ65QcT/Nabfu4yR.K.9zw30oOIJMthOEX96sJ6kQ2lMJn5dce', 'HR Manager'),
(43, 'mng test', '$2y$10$W9GecvUVwbcNyKxQHG3CN.rSLIJ7QkUMfuGwbCaHKM/i8NImektpC', 'RH Manager'),
(44, 'test mng', '$2y$10$EGOcztdzVdB.33Feo18HkOuAyPHuopfNHoSnVUeLdbWdP/678N9/u', 'RH Manager'),
(45, 'qqq', '$2y$10$d4R56OfLvi/COuvvabxjGep/RoajoRrvQUGPK4XefaZswPMXItwqu', 'RH Manager'),
(46, 'ooo', '$2y$10$E/O.qRPaP.Z.jYQLZl5A5OtAJ4TqkIEHxdiEJnvs32kM8.QX0z3au', 'RH Manager'),
(47, 'MNG-001', '$2y$10$Md5DZb2koV025mMVJd4vG./HT7RMoVLWh.4hzhMdOV1Bt6LByfYta', 'RH Manager'),
(48, 'qqq', '$2y$10$in5..I6Mu/Zpqw04ieyo5ulG.O0mmndCSVgd93yzzbr3TwDSeAZHi', 'Employé'),
(49, 'MNG', '$2y$10$93oAj2GXP4AXAzzfCl.1N.B.L4N4tpI0apvEbG6T/Wk/tjg12oUje', 'RH Manager'),
(50, 'emm', '$2y$10$1unbYHUsCGH/ZZ8xPaDsKOhW/BkA88u5sh64hqPLZcx.CEjMysf06', 'Employé'),
(51, 'EMP-001', '$2y$10$siG.brVUeqwmAX6h1AZHzOROaBHg9qg4rwpP539kmtqs8b4BuE7My', 'Employé'),
(52, 'EMP-001', '$2y$10$j8Ept2FczJDfH3bGkOLTgu1empSjSSz6TbgPlCoKLwfwAxRW.TDU.', 'Employé'),
(54, 'EMP-002', '$2y$10$bnBmvxSsDtNDj0jGIBnHCeISYv/wKo5QIhMnVGw/H/IuQA9M05ynO', 'Employé'),
(55, 'EMP-003', '$2y$10$Kydf9MrnhLLK8XarLucnBuqWecaTknrTnMt3CwrPf8ynvqWsUf4Dy', 'Employé'),
(56, 'EMP-004', '$2y$10$fgCWL73b9BJkdj4FnsQEfeeXMHSogBxO0g9qiHchr7btDHP0nnWG2', 'Employé'),
(57, 'EMP-005', '$2y$10$1RIs8G5vFipGbi9ddZDGUeu6B5bGdH1WVbmHFzlBAIHtlWEHS15fa', 'Employé'),
(58, 'EMP-006', '$2y$10$G14W8qoa6HykBx7N/nAImenOi2ZeF586W5k1ZCoJM9ui0LsOxJf..', 'Employé');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `BlogID` int(11) NOT NULL,
  `Titre` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `DateCreation` timestamp NOT NULL DEFAULT current_timestamp(),
  `Auteur` varchar(255) NOT NULL,
  `AuthentificationID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`BlogID`, `Titre`, `Description`, `Photo`, `DateCreation`, `Auteur`, `AuthentificationID`) VALUES
(32, 'Les Héros du Code : Histoires inspirantes de développeurs qui changent le monde', 'Le codage n\'est pas qu\'écrire des lignes de texte absconses sur un écran. C\'est un outil puissant qui permet de créer des solutions innovantes et de résoudre des problèmes complexes. Ce blog met en lumière des développeurs extraordinaires qui utilisent leurs compétences pour changer le monde.  Découvrez des récits inspirants d\'entrepreneurs qui ont lancé des startups révolutionnaires, de chercheurs qui développent des technologies de pointe pour un avenir meilleur, et de militants qui utilisent le code pour promouvoir la justice sociale.  Plongez dans les coulisses du processus de développement, des premiers brainstormings aux lancements réussis.  Ce blog vous montrera comment le code peut être utilisé pour faire une réelle différence et vous motivera peut-être à devenir vous-même un héros du code !  Alors, attrapez votre clavier, laissez-vous inspirer et commencez à écrire votre propre histoire de changement.', 'blog3.jpg', '2024-07-08 11:26:47', 'Manager Name', 49),
(33, 'RH à l\'ère du code : Attirer les talents et bâtir des équipes techniques de rêve', 'Dans l\'économie numérique d\'aujourd\'hui, les talents techniques sont plus demandés que jamais.  Ce blog s\'adresse aux professionnels des ressources humaines qui cherchent à attirer les meilleurs développeurs, ingénieurs et autres experts en technologie.  Découvrez des stratégies de recrutement innovantes, allant de la création d\'offres d\'emploi attrayantes à l\'utilisation de plateformes de recrutement spécialisées.  Apprenez à évaluer les compétences techniques et culturelles pendant les entretiens et à créer un environnement de travail stimulant qui retient les talents.  Le blog explore également l\'importance de la culture d\'entreprise axée sur la technologie, de la formation continue et des programmes de perfectionnement pour fidéliser vos équipes techniques de rêve.  Préparez-vous à transformer votre stratégie RH et à bâtir une force de travail technologique de premier ordre.', 'blog.png', '2024-07-08 11:29:16', 'oussama ahaddane', 49),
(34, 'Le Futur du Code : Tendances à surveiller et comment s\'y préparer', ' Le monde du codage évolue à un rythme effréné. Ce blog explore les tendances technologiques les plus passionnantes qui façonneront l\'avenir du développement logiciel.  Plongez dans des sujets brûlants comme l\'intelligence artificielle, la blockchain, l\'informatique quantique et le développement low-code/no-code.  Apprenez comment ces innovations transforment la façon dont nous construisons des applications, automatisons des processus et interagissons avec la technologie.  Le blog vous propose également des conseils pratiques pour vous préparer à ces changements et rester à la pointe de votre domaine.  Que vous soyez un développeur junior ou un vétéran chevronné, ce blog vous donnera un aperçu de l\'avenir passionnant du code et vous aidera à développer les compétences nécessaires pour y prospérer.', 'blog.jpg', '2024-07-08 11:30:30', 'oussama ahaddane', 49);

-- --------------------------------------------------------

--
-- Table structure for table `congé`
--

CREATE TABLE `congé` (
  `CongéID` int(11) NOT NULL,
  `Type_Congé` varchar(50) DEFAULT NULL,
  `Date_Début` date DEFAULT NULL,
  `Date_Fin` date DEFAULT NULL,
  `Motif` varchar(255) DEFAULT NULL,
  `Statut` varchar(50) DEFAULT NULL,
  `EmployéID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `congé`
--

INSERT INTO `congé` (`CongéID`, `Type_Congé`, `Date_Début`, `Date_Fin`, `Motif`, `Statut`, `EmployéID`) VALUES
(3, 'Congé maladie', '2024-06-14', '2024-06-22', 'mnm', 'Approuvé', 20),
(4, 'Congé maladie', '2024-06-22', '2024-06-29', 'nnnn', 'Approuvé', 20),
(5, 'Congé maternité/paternité', '2024-06-21', '2024-06-28', 'jkkjlkjl', 'Rejeté', 20),
(6, 'Congé maternité/paternité', '2024-06-29', '2024-07-05', 'kjlkjl', 'Rejeté', 20),
(7, 'Congé sans solde', '2024-06-07', '2024-06-22', 'kkkk', 'Approuvé', 20),
(8, 'Congé maladie', '2024-06-29', '2024-06-28', 'oussam ah', 'Approved', 21),
(9, 'Congé maternité/paternité', '2024-06-21', '2024-06-28', 'motif', 'Approuvé', 20),
(10, 'Congé annuel', '2024-06-23', '2024-06-24', 'jkjkjjkjkjkjk', 'Rejeté', 21),
(11, 'Congé maladie', '2024-06-25', '2024-06-30', 'maladie', 'Approuvé', 26),
(12, 'Congé maladie', '2024-06-25', '2024-06-30', 'maladie', 'Approuvé', 26),
(13, 'Congé annuel', '2024-07-05', '2024-07-12', 'kk', 'Rejeté', 24),
(14, 'Congé maternité/paternité', '2024-07-06', '2024-07-13', 'maladie', 'Approuvé', 29),
(15, 'Congé maternité/paternité', '2024-07-06', '2024-07-12', 'motif', 'Approuvé', 29),
(16, 'Congé maladie', '2024-07-07', '2024-07-08', 'maladie', 'Approuvé', 32),
(17, 'Congé sans solde', '2024-07-09', '2024-07-14', 'vacance', 'Approuvé', 29);

-- --------------------------------------------------------

--
-- Table structure for table `département`
--

CREATE TABLE `département` (
  `DépartementID` int(11) NOT NULL,
  `Nom_Département` varchar(255) NOT NULL,
  `Entreprise` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `département`
--

INSERT INTO `département` (`DépartementID`, `Nom_Département`, `Entreprise`, `Description`) VALUES
(2, 'resources humaines', 'Eureka Création', 'description !!!0'),
(3, 'devlepment', 'Eureka Création', 'description !!'),
(8, 'comptabilite', 'Eureka Création', 'comptabilite '),
(9, 'marketing', 'Eureka Création', 'marketing '),
(10, 'digital marketing', 'Eureka Création', 'marketing digital'),
(11, 'finance ', 'Eureka Création', ''),
(12, 'mobile development ', 'Eureka Création', 'mobile development '),
(13, 'mobile development ', 'Eureka Création', 'mobile development '),
(14, 'mobile development ', 'Eureka Création', 'mobile development '),
(15, 'mobile development ', 'Eureka Création', 'mobile development '),
(16, 'mobile development ', 'Eureka Création', 'mobile development ');

-- --------------------------------------------------------

--
-- Table structure for table `employeeformation`
--

CREATE TABLE `employeeformation` (
  `EmployeeFormationID` int(11) NOT NULL,
  `EmployeID` int(11) DEFAULT NULL,
  `FormationID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeeformation`
--

INSERT INTO `employeeformation` (`EmployeeFormationID`, `EmployeID`, `FormationID`) VALUES
(34, 20, 1),
(35, 20, 4),
(41, 24, 4),
(42, 24, 1),
(43, 24, 6),
(44, 24, 5),
(45, 25, 1),
(47, 26, 8),
(48, 29, 8),
(49, 29, 6);

-- --------------------------------------------------------

--
-- Table structure for table `employé`
--

CREATE TABLE `employé` (
  `EmployéID` int(11) NOT NULL,
  `NomPrenom` varchar(255) NOT NULL,
  `Nom_utilisateur` varchar(255) DEFAULT NULL,
  `Mot_De_Passe` varchar(255) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Adresse` varchar(255) NOT NULL,
  `Téléphone` varchar(50) NOT NULL,
  `Date_Embauche` date NOT NULL,
  `Role` varchar(50) NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `DépartementID` int(11) DEFAULT NULL,
  `AuthentificationID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employé`
--

INSERT INTO `employé` (`EmployéID`, `NomPrenom`, `Nom_utilisateur`, `Mot_De_Passe`, `Email`, `Adresse`, `Téléphone`, `Date_Embauche`, `Role`, `profile_photo`, `DépartementID`, `AuthentificationID`) VALUES
(20, 'oussama ahddane', 'manager', '$2y$10$T2IwncYj74i4252i.8bse.KZclKCIX/IeMGRFvpY5ENKLet2xSY5K', 'manager@gmail.com', 'rue lmatar safi maroc', '0661648488', '2024-06-01', 'RH Manager', '', 2, 35),
(21, 'yassine elrhbie', 'employe', '$2y$10$h81QSUSVHSfyqXZvWnINa.i4dejUNebi68GB.L0QIqdGv0QZhqwBy', 'employe@gmail.com', 'rue eljrifate safi maroc', '06651589965', '2024-06-22', 'RH Manager', '', 2, 36),
(24, 'oussma ahaddane', 'emp', '$2y$10$F5aK07B.8O9SbznCRDGpHe4TKNv.q/9ItMmlvSV3JFA3sNyC4UNoq', 'emp@gmail.com', 'rue safi maroc', '06666677777', '2024-06-26', 'Employé', 'profile.jpg', 3, 39),
(25, 'test', 'emptest', '$2y$10$niG6XWpERaLX.lpmmwNyOOUuX0dpKVqvR3J3CJmXNDR0Tv0uWIGtG', 'emptest@gg.cc', 'emptest', '065555', '2024-06-25', 'Employé', '', 3, 40),
(26, 'said abou', 'emp-01', '$2y$10$9NBPQ1uBUaqLSbLCfNSugOulVFw38ORIv8sDqD5c7famd.lZ3Obwe', 'said@gmail.com', 'sbt', '06666666666', '2024-06-25', 'Employé', '', 3, 41),
(29, 'yassine elrhbie', 'EMP-001', '$2y$10$siG.brVUeqwmAX6h1AZHzOROaBHg9qg4rwpP539kmtqs8b4BuE7My', 'emp001@gamil.com', 'rue el jrifat safi maroc', '0666552235', '2024-07-05', 'Employé', '', 3, 51),
(32, 'oussama', 'EMP-002', '$2y$10$bnBmvxSsDtNDj0jGIBnHCeISYv/wKo5QIhMnVGw/H/IuQA9M05ynO', 'emp002@gamil.com', 'Rue Lmatare Safi Maroc', '0666552235', '2024-07-07', 'Employé', '', 3, 54),
(33, 'oussama emp', 'EMP-003', '$2y$10$Kydf9MrnhLLK8XarLucnBuqWecaTknrTnMt3CwrPf8ynvqWsUf4Dy', 'oussamaemp@ggg.vv', 'oussama emp', '055555555555', '2024-07-17', 'Employé', '', 2, 55),
(34, 'oussama emp', 'EMP-004', '$2y$10$fgCWL73b9BJkdj4FnsQEfeeXMHSogBxO0g9qiHchr7btDHP0nnWG2', 'oussamaemp@ff.f', 'oussamaemp', '0222222222', '2024-07-02', 'Employé', '', 3, 56),
(35, 'oussamaemp', 'EMP-005', '$2y$10$1RIs8G5vFipGbi9ddZDGUeu6B5bGdH1WVbmHFzlBAIHtlWEHS15fa', 'oussamaemp@gg.g', 'oussamaemp', '0111111111', '2024-07-10', 'Employé', '', 9, 57),
(36, 'oussamaemp', 'EMP-006', '$2y$10$G14W8qoa6HykBx7N/nAImenOi2ZeF586W5k1ZCoJM9ui0LsOxJf..', 'oussamaemp@gmail.com', 'oussamaemp', '0222222222', '2024-07-24', 'Employé', '', 8, 58);

-- --------------------------------------------------------

--
-- Table structure for table `formation`
--

CREATE TABLE `formation` (
  `FormationID` int(11) NOT NULL,
  `Nom_Formation` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Date_Formation` date DEFAULT NULL,
  `Duree` varchar(50) DEFAULT NULL,
  `EmployéID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `formation`
--

INSERT INTO `formation` (`FormationID`, `Nom_Formation`, `Description`, `Date_Formation`, `Duree`, `EmployéID`) VALUES
(1, 'ajax js', 'AJAX (Asynchronous JavaScript and XML) permet aux développeurs web de créer des applications web plus fluides et interactives. En communiquant avec le serveur en arrière-plan, AJAX évite les rechargements complets de la page, ce qui peut être lent et gêna', '2024-06-21', '3 jours', NULL),
(4, 'java EE', 'Java EE (Enterprise Edition) est une plateforme destinée au développement d\'applications complexes et à grande échelle. Elle fournit un ensemble de technologies qui gèrent les tâches informatiques courantes en entreprise, telles que l\'accès aux bases de d', '2024-06-24', '2 mois', NULL),
(5, 'fomation test', 'testttttttttttttttttttttttttttt', '2024-06-25', '30 jours', NULL),
(6, 'ttt', 'tttt', '2024-06-27', '30 jours', NULL),
(7, 'fomation test', 'fomation test', '2024-06-25', '10 jours', NULL),
(8, 'react native mobile ', 'react native mobile !!', '2024-06-26', '3 mois', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `objectifs`
--

CREATE TABLE `objectifs` (
  `ObjectifID` int(11) NOT NULL,
  `ParentObjectifID` int(11) DEFAULT NULL,
  `Titre` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `EmployeID` int(11) DEFAULT NULL,
  `DateCreation` date DEFAULT NULL,
  `DateEcheance` date DEFAULT NULL,
  `Type` enum('Principal','Sous-objectif') NOT NULL,
  `Statut` enum('En cours','Terminé','En attente') DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `objectifs`
--

INSERT INTO `objectifs` (`ObjectifID`, `ParentObjectifID`, `Titre`, `Description`, `EmployeID`, `DateCreation`, `DateEcheance`, `Type`, `Statut`) VALUES
(1, NULL, 'Certification professionnelle', 'Certification professionnelle', 26, '2024-06-26', '2024-06-27', 'Principal', 'Terminé'),
(2, NULL, 'Certification professionnelle', 'Certification professionnelle', 26, '2024-06-26', '2024-06-28', 'Principal', 'Terminé'),
(3, NULL, 'said obj', 'said obj', 26, '2024-06-26', '2024-06-28', 'Principal', 'Terminé'),
(4, NULL, 'said obj oob', 'said obj oob', 26, '2024-06-26', '2024-06-27', 'Principal', 'Terminé'),
(5, 4, 'said obj oob said obj oob', 'said obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oob', 26, '2024-06-26', '2024-06-28', 'Sous-objectif', 'Terminé'),
(6, 4, 'said obj oob said obj oob said obj oob', 'said obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oobsaid obj oob said obj oob', 26, '2024-06-26', '2024-06-28', 'Sous-objectif', 'Terminé'),
(7, NULL, 'sub objectif', 'sub objectif sub objectif', 26, '2024-06-27', '2024-06-28', 'Principal', 'Terminé'),
(8, 7, 'sous objectif 1', NULL, 26, '2024-06-27', '2024-06-28', 'Sous-objectif', 'Terminé'),
(9, 7, 'sous objectif 2', NULL, 26, '2024-06-27', '2024-06-28', 'Sous-objectif', 'Terminé'),
(10, 7, 'sous objectif 1', NULL, 26, '2024-06-27', '2024-06-28', 'Sous-objectif', 'Terminé'),
(11, NULL, 'soub obj', 'soub obj soub obj', 26, '2024-06-27', '2024-06-27', 'Principal', 'Terminé'),
(12, 11, 'soub obj1', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En cours'),
(13, 11, 'soub obj 2', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En attente'),
(14, 11, 'soub obj 3', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En attente'),
(15, NULL, 'EDIT YOUR PROFILE', 'ADD PROFILE PIC ADD PROFILE PIC ADD PROFILE PIC', 26, '2024-06-27', '2024-06-27', 'Principal', 'Terminé'),
(16, 15, 'ADD PROFILE PIC PRO', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'Terminé'),
(17, 15, 'ADD NEW ADRESSE', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'Terminé'),
(18, 15, 'ADD NEW PHONE NUMBER', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'Terminé'),
(19, NULL, 'Objectif', 'ObjectifObjectif Objectif vObjectifObjectifObjectifObjectif', 26, '2024-06-27', '2024-06-27', 'Principal', 'Terminé'),
(20, 19, 'Objectif 1', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'Terminé'),
(21, 19, 'Objectif 2', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En attente'),
(22, 19, 'Objectif 3', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En attente'),
(23, 19, 'Objectif4', NULL, 26, '2024-06-27', '2024-06-27', 'Sous-objectif', 'En attente'),
(24, NULL, 'objectif test for emp test', 'objectif test for emp test objectif test for emp test', 25, '2024-06-27', '2024-06-28', 'Principal', 'Terminé'),
(25, 24, 'objectif test 1', NULL, 25, '2024-06-27', '2024-06-28', 'Sous-objectif', 'En cours'),
(26, 24, 'objectif test 2', NULL, 25, '2024-06-27', '2024-06-28', 'Sous-objectif', 'En cours'),
(27, NULL, 'test obj', 'test obj', 25, '2024-06-27', '2024-06-28', 'Principal', 'Terminé'),
(28, 27, 'test obj 1', NULL, 25, '2024-06-27', '2024-06-28', 'Sous-objectif', 'Terminé'),
(29, NULL, 'tst', 'tst tsttsttsttsttst', 24, '2024-06-27', '2024-07-05', 'Principal', 'Terminé'),
(30, NULL, 'kkkkkkk', ';;;;;;;;;;;', 26, '2024-06-27', '2024-06-29', 'Principal', 'En attente'),
(31, 30, 'nnnnnnnnnnnnnn', NULL, 26, '2024-06-27', '2024-06-29', 'Sous-objectif', 'En attente'),
(32, NULL, 'new obj', 'add new pic of profile ', 24, '2024-07-04', '2024-07-04', 'Principal', 'Terminé'),
(33, 32, 'add addresse ', NULL, 24, '2024-07-04', '2024-07-04', 'Sous-objectif', 'Terminé'),
(34, NULL, 'new obj', 'no desc', 29, '2024-07-06', '2024-07-07', 'Principal', 'Terminé');

-- --------------------------------------------------------

--
-- Table structure for table `objectives`
--

CREATE TABLE `objectives` (
  `ObjectiveID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `ObjectiveText` text DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performancemetrics`
--

CREATE TABLE `performancemetrics` (
  `MetricID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `MetricName` varchar(100) DEFAULT NULL,
  `MetricValue` varchar(100) DEFAULT NULL,
  `MetricDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `présence_journalière`
--

CREATE TABLE `présence_journalière` (
  `PrésenceID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Heure_Arrivee` time DEFAULT NULL,
  `Heure_Depart` time DEFAULT NULL,
  `EmployéID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `présence_journalière`
--

INSERT INTO `présence_journalière` (`PrésenceID`, `Date`, `Heure_Arrivee`, `Heure_Depart`, `EmployéID`) VALUES
(4, '2024-07-05', '11:18:00', '21:19:00', 24),
(7, '2024-07-05', '10:05:00', '19:05:00', 29),
(8, '2024-07-06', '12:07:00', '08:00:00', 29),
(9, '2024-07-07', '09:42:25', '17:42:25', 29),
(10, '2024-07-08', '09:42:25', '17:42:25', 29),
(11, '2024-07-09', '09:00:25', '17:00:25', 29),
(12, '2024-07-10', '09:00:25', '17:00:25', 29),
(13, '2024-07-11', '09:00:25', '17:00:25', 29),
(14, '2024-07-12', '09:42:25', '17:42:25', 29),
(15, '2024-06-01', '09:42:25', '17:42:25', 29),
(16, '2024-06-02', '09:00:00', '17:00:00', 29),
(17, '2024-06-04', '09:00:00', '17:00:00', 29),
(18, '2024-06-05', '09:00:00', '17:00:00', 29),
(19, '2024-06-06', '09:00:00', '17:00:00', 29),
(20, '2024-06-07', '09:00:00', '17:00:00', 29),
(21, '2024-06-08', '09:00:00', '17:00:00', 29),
(22, '2024-07-12', '09:42:25', '17:42:25', 29),
(23, '2024-07-13', '09:00:25', '17:00:25', 29),
(24, '2024-07-14', '09:00:25', '17:00:25', 29),
(25, '2024-07-15', '09:00:25', '17:00:25', 29),
(26, '2024-07-15', '09:00:25', '17:00:25', 29),
(27, '2024-07-16', '09:00:25', '17:00:25', 29),
(28, '2024-07-18', '09:00:25', '17:00:25', 29),
(29, '2024-07-19', '09:00:25', '17:00:25', 29),
(30, '2024-07-07', '08:24:00', '08:25:00', 32),
(31, '2024-07-08', '10:34:00', '10:40:00', 32);

-- --------------------------------------------------------

--
-- Table structure for table `rhmanager`
--

CREATE TABLE `rhmanager` (
  `RHManagerID` int(11) NOT NULL,
  `NomPrenom` varchar(255) NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Telephone` varchar(255) DEFAULT NULL,
  `Nom_utilisateur` varchar(255) NOT NULL,
  `Mot_De_Passe` varchar(255) NOT NULL,
  `Date_Embauche` date NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `AuthentificationID` int(11) DEFAULT NULL,
  `Role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rhmanager`
--

INSERT INTO `rhmanager` (`RHManagerID`, `NomPrenom`, `Email`, `Telephone`, `Nom_utilisateur`, `Mot_De_Passe`, `Date_Embauche`, `profile_photo`, `AuthentificationID`, `Role`) VALUES
(12, 'oussama Manager', 'oussama.ahaddane@gmail.com', '0661849688', 'MNG-001', '$2y$10$Md5DZb2koV025mMVJd4vG./HT7RMoVLWh.4hzhMdOV1Bt6LByfYta', '2024-07-02', '', 47, NULL),
(13, 'manager', 'mng@gmail.com', '06666', 'MNG', '$2y$10$93oAj2GXP4AXAzzfCl.1N.B.L4N4tpI0apvEbG6T/Wk/tjg12oUje', '2024-07-03', 'member-1.jpg', 49, 'RH Manager');

-- --------------------------------------------------------

--
-- Table structure for table `soumissions_contact`
--

CREATE TABLE `soumissions_contact` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_soumission` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soumissions_contact`
--

INSERT INTO `soumissions_contact` (`id`, `nom`, `email`, `sujet`, `message`, `date_soumission`) VALUES
(1, 'SSSSS', 'SSS@FSDF.C', 'DSDSSDSDD', 'DD', '2024-07-07 20:43:27'),
(2, 'oussma ahaddane', 'oussama.ahaddane@gmail.com', 'DSDSSDSDD', 'KK', '2024-07-07 20:45:27');

-- --------------------------------------------------------

--
-- Table structure for table `toutprésence`
--

CREATE TABLE `toutprésence` (
  `PrésenceID` int(11) NOT NULL,
  `Mois` int(11) DEFAULT NULL,
  `Année` int(11) DEFAULT NULL,
  `EmployéID` int(11) DEFAULT NULL,
  `Jour1` varchar(1) DEFAULT 'A',
  `Jour2` varchar(1) DEFAULT 'A',
  `Jour3` varchar(1) DEFAULT 'A',
  `Jour4` varchar(1) DEFAULT 'A',
  `Jour5` varchar(1) DEFAULT 'A',
  `Jour6` varchar(1) DEFAULT 'A',
  `Jour7` varchar(1) DEFAULT 'A',
  `Jour8` varchar(1) DEFAULT 'A',
  `Jour9` varchar(1) DEFAULT 'A',
  `Jour10` varchar(1) DEFAULT 'A',
  `Jour11` varchar(1) DEFAULT 'A',
  `Jour12` varchar(1) DEFAULT 'A',
  `Jour13` varchar(1) DEFAULT 'A',
  `Jour14` varchar(1) DEFAULT 'A',
  `Jour15` varchar(1) DEFAULT 'A',
  `Jour16` varchar(1) DEFAULT 'A',
  `Jour17` varchar(1) DEFAULT 'A',
  `Jour18` varchar(1) DEFAULT 'A',
  `Jour19` varchar(1) DEFAULT 'A',
  `Jour20` varchar(1) DEFAULT 'A',
  `Jour21` varchar(1) DEFAULT 'A',
  `Jour22` varchar(1) DEFAULT 'A',
  `Jour23` varchar(1) DEFAULT 'A',
  `Jour24` varchar(1) DEFAULT 'A',
  `Jour25` varchar(1) DEFAULT 'A',
  `Jour26` varchar(1) DEFAULT 'A',
  `Jour27` varchar(1) DEFAULT 'A',
  `Jour28` varchar(1) DEFAULT 'A',
  `Jour29` varchar(1) DEFAULT 'A',
  `Jour30` varchar(1) DEFAULT 'A',
  `Jour31` varchar(1) DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toutprésence`
--

INSERT INTO `toutprésence` (`PrésenceID`, `Mois`, `Année`, `EmployéID`, `Jour1`, `Jour2`, `Jour3`, `Jour4`, `Jour5`, `Jour6`, `Jour7`, `Jour8`, `Jour9`, `Jour10`, `Jour11`, `Jour12`, `Jour13`, `Jour14`, `Jour15`, `Jour16`, `Jour17`, `Jour18`, `Jour19`, `Jour20`, `Jour21`, `Jour22`, `Jour23`, `Jour24`, `Jour25`, `Jour26`, `Jour27`, `Jour28`, `Jour29`, `Jour30`, `Jour31`) VALUES
(20, 7, 2024, 24, NULL, NULL, NULL, NULL, 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 8, 2024, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 6, 2024, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A'),
(23, 7, 2024, 29, NULL, NULL, NULL, NULL, 'P', 'A', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', NULL, 'P', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, 2024, 29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 6, 2024, 29, 'P', 'P', NULL, 'P', 'P', 'P', 'P', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A'),
(26, 4, 2024, 29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authentification`
--
ALTER TABLE `authentification`
  ADD PRIMARY KEY (`AuthentificationID`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`BlogID`),
  ADD KEY `AuthentificationID` (`AuthentificationID`);

--
-- Indexes for table `congé`
--
ALTER TABLE `congé`
  ADD PRIMARY KEY (`CongéID`),
  ADD KEY `EmployéID` (`EmployéID`);

--
-- Indexes for table `département`
--
ALTER TABLE `département`
  ADD PRIMARY KEY (`DépartementID`);

--
-- Indexes for table `employeeformation`
--
ALTER TABLE `employeeformation`
  ADD PRIMARY KEY (`EmployeeFormationID`),
  ADD KEY `EmployeID` (`EmployeID`),
  ADD KEY `FormationID` (`FormationID`);

--
-- Indexes for table `employé`
--
ALTER TABLE `employé`
  ADD PRIMARY KEY (`EmployéID`),
  ADD UNIQUE KEY `unique_nom_utilisateur` (`Nom_utilisateur`),
  ADD KEY `DépartementID` (`DépartementID`),
  ADD KEY `AuthentificationID` (`AuthentificationID`);

--
-- Indexes for table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`FormationID`),
  ADD KEY `EmployéID` (`EmployéID`);

--
-- Indexes for table `objectifs`
--
ALTER TABLE `objectifs`
  ADD PRIMARY KEY (`ObjectifID`),
  ADD KEY `fk_employe` (`EmployeID`),
  ADD KEY `fk_parent_objectif` (`ParentObjectifID`);

--
-- Indexes for table `objectives`
--
ALTER TABLE `objectives`
  ADD PRIMARY KEY (`ObjectiveID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `performancemetrics`
--
ALTER TABLE `performancemetrics`
  ADD PRIMARY KEY (`MetricID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `présence_journalière`
--
ALTER TABLE `présence_journalière`
  ADD PRIMARY KEY (`PrésenceID`),
  ADD KEY `EmployéID` (`EmployéID`);

--
-- Indexes for table `rhmanager`
--
ALTER TABLE `rhmanager`
  ADD PRIMARY KEY (`RHManagerID`),
  ADD KEY `AuthentificationID` (`AuthentificationID`);

--
-- Indexes for table `soumissions_contact`
--
ALTER TABLE `soumissions_contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toutprésence`
--
ALTER TABLE `toutprésence`
  ADD PRIMARY KEY (`PrésenceID`),
  ADD KEY `EmployéID` (`EmployéID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authentification`
--
ALTER TABLE `authentification`
  MODIFY `AuthentificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `BlogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `congé`
--
ALTER TABLE `congé`
  MODIFY `CongéID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `département`
--
ALTER TABLE `département`
  MODIFY `DépartementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employeeformation`
--
ALTER TABLE `employeeformation`
  MODIFY `EmployeeFormationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `employé`
--
ALTER TABLE `employé`
  MODIFY `EmployéID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `formation`
--
ALTER TABLE `formation`
  MODIFY `FormationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `objectifs`
--
ALTER TABLE `objectifs`
  MODIFY `ObjectifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `objectives`
--
ALTER TABLE `objectives`
  MODIFY `ObjectiveID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performancemetrics`
--
ALTER TABLE `performancemetrics`
  MODIFY `MetricID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `présence_journalière`
--
ALTER TABLE `présence_journalière`
  MODIFY `PrésenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `rhmanager`
--
ALTER TABLE `rhmanager`
  MODIFY `RHManagerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `soumissions_contact`
--
ALTER TABLE `soumissions_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `toutprésence`
--
ALTER TABLE `toutprésence`
  MODIFY `PrésenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`AuthentificationID`) REFERENCES `authentification` (`AuthentificationID`);

--
-- Constraints for table `congé`
--
ALTER TABLE `congé`
  ADD CONSTRAINT `congé_ibfk_1` FOREIGN KEY (`EmployéID`) REFERENCES `employé` (`EmployéID`);

--
-- Constraints for table `employeeformation`
--
ALTER TABLE `employeeformation`
  ADD CONSTRAINT `employeeformation_ibfk_1` FOREIGN KEY (`EmployeID`) REFERENCES `employé` (`EmployéID`),
  ADD CONSTRAINT `employeeformation_ibfk_2` FOREIGN KEY (`FormationID`) REFERENCES `formation` (`FormationID`);

--
-- Constraints for table `employé`
--
ALTER TABLE `employé`
  ADD CONSTRAINT `employé_ibfk_1` FOREIGN KEY (`DépartementID`) REFERENCES `département` (`DépartementID`),
  ADD CONSTRAINT `employé_ibfk_2` FOREIGN KEY (`AuthentificationID`) REFERENCES `authentification` (`AuthentificationID`);

--
-- Constraints for table `formation`
--
ALTER TABLE `formation`
  ADD CONSTRAINT `formation_ibfk_1` FOREIGN KEY (`EmployéID`) REFERENCES `employé` (`EmployéID`);

--
-- Constraints for table `objectifs`
--
ALTER TABLE `objectifs`
  ADD CONSTRAINT `fk_employe` FOREIGN KEY (`EmployeID`) REFERENCES `employé` (`EmployéID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_objectif` FOREIGN KEY (`ParentObjectifID`) REFERENCES `objectifs` (`ObjectifID`) ON DELETE CASCADE;

--
-- Constraints for table `objectives`
--
ALTER TABLE `objectives`
  ADD CONSTRAINT `objectives_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employé` (`EmployéID`);

--
-- Constraints for table `performancemetrics`
--
ALTER TABLE `performancemetrics`
  ADD CONSTRAINT `performancemetrics_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employé` (`EmployéID`);

--
-- Constraints for table `présence_journalière`
--
ALTER TABLE `présence_journalière`
  ADD CONSTRAINT `présence_journalière_ibfk_1` FOREIGN KEY (`EmployéID`) REFERENCES `employé` (`EmployéID`);

--
-- Constraints for table `rhmanager`
--
ALTER TABLE `rhmanager`
  ADD CONSTRAINT `rhmanager_ibfk_1` FOREIGN KEY (`AuthentificationID`) REFERENCES `authentification` (`AuthentificationID`);

--
-- Constraints for table `toutprésence`
--
ALTER TABLE `toutprésence`
  ADD CONSTRAINT `toutprésence_ibfk_1` FOREIGN KEY (`EmployéID`) REFERENCES `employé` (`EmployéID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
