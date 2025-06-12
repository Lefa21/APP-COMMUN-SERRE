-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 13 juin 2025 à 01:43
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `8pduqiq06b_bdd_serre`
--

-- --------------------------------------------------------

--
-- Structure de la table `actionneurs`
--

CREATE TABLE `actionneurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `team_id` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `current_state` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `actionneurs`
--

INSERT INTO `actionneurs` (`id`, `nom`, `type`, `team_id`, `is_active`, `current_state`, `created_at`) VALUES
(1, 'Arrosage Automatique A', 'irrigation', 1, 1, 0, '2025-06-11 17:14:59'),
(3, 'Éclairage A', 'lighting', 1, 1, 0, '2025-06-11 17:14:59'),
(4, 'Chauffage A', 'heating', 1, 1, 0, '2025-06-11 17:14:59'),
(5, 'Arrosage Automatique B', 'irrigation', 2, 1, 0, '2025-06-11 17:14:59'),
(6, 'Ventilation B', 'ventilation', 2, 1, 0, '2025-06-11 17:14:59'),
(7, 'Éclairage B', 'lighting', 2, 1, 0, '2025-06-11 17:14:59');

-- --------------------------------------------------------

--
-- Structure de la table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `user_id` char(36) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `actuator_logs`
--

CREATE TABLE `actuator_logs` (
  `id` int(11) NOT NULL,
  `actionneur_id` int(11) NOT NULL,
  `action` enum('ON','OFF') NOT NULL,
  `user_id` char(36) NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `actuator_logs`
--

INSERT INTO `actuator_logs` (`id`, `actionneur_id`, `action`, `user_id`, `timestamp`) VALUES
(25, 1, 'ON', 'admin-2025-06-12-225346', '2025-06-12 21:33:25'),
(26, 1, 'OFF', 'admin-2025-06-12-225346', '2025-06-12 21:33:26');

-- --------------------------------------------------------

--
-- Structure de la table `bienvenue`
--

CREATE TABLE `bienvenue` (
  `Message` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `bienvenue`
--

INSERT INTO `bienvenue` (`Message`) VALUES
('Salut ! Tu peux configurer ta BDD avec le logiciel de ton choix !');

-- --------------------------------------------------------

--
-- Structure de la table `capteurs`
--

CREATE TABLE `capteurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `unite` varchar(10) DEFAULT NULL,
  `team_id` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `capteurs`
--

INSERT INTO `capteurs` (`id`, `nom`, `type`, `unite`, `team_id`, `is_active`, `created_at`) VALUES
(1, 'Température Serre A', 'temperature', '°C', 1, 1, '2025-06-11 17:14:59'),
(2, 'Humidité Serre A', 'humidity', '%', 1, 1, '2025-06-11 17:14:59'),
(3, 'Humidité Sol A', 'soil_moisture', '%', 1, 1, '2025-06-11 17:14:59'),
(4, 'Luminosité A', 'light', 'lux', 1, 1, '2025-06-11 17:14:59'),
(5, 'pH Sol A', 'ph', 'pH', 1, 1, '2025-06-11 17:14:59'),
(6, 'Température Serre B', 'temperature', '°C', 2, 1, '2025-06-11 17:14:59'),
(7, 'Humidité Serre B', 'humidity', '%', 2, 1, '2025-06-11 17:14:59'),
(8, 'CO2 Serre A', 'co2', 'ppm', 1, 1, '2025-06-11 17:14:59');

-- --------------------------------------------------------

--
-- Structure de la table `etats_actionneurs`
--

CREATE TABLE `etats_actionneurs` (
  `id` bigint(20) NOT NULL,
  `actionneur_id` int(11) NOT NULL,
  `etat` tinyint(4) NOT NULL,
  `date_heure` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etats_actionneurs`
--

INSERT INTO `etats_actionneurs` (`id`, `actionneur_id`, `etat`, `date_heure`) VALUES
(1, 1, 1, '2025-06-11 18:14:25'),
(2, 1, 0, '2025-06-11 18:14:27'),
(3, 1, 1, '2025-06-11 18:14:28'),
(4, 1, 0, '2025-06-11 18:14:32'),
(5, 1, 1, '2025-06-11 20:10:29'),
(6, 1, 0, '2025-06-11 20:10:30'),
(7, 1, 1, '2025-06-11 20:10:31'),
(8, 1, 0, '2025-06-11 20:10:34'),
(9, 4, 1, '2025-06-11 20:11:10'),
(10, 4, 0, '2025-06-11 20:11:15'),
(11, 1, 1, '2025-06-11 20:27:34'),
(12, 1, 0, '2025-06-11 20:27:43'),
(13, 4, 1, '2025-06-12 09:39:34'),
(14, 4, 0, '2025-06-12 09:50:44'),
(15, 4, 1, '2025-06-12 09:50:55'),
(16, 4, 0, '2025-06-12 09:50:58'),
(17, 4, 1, '2025-06-12 10:39:56'),
(18, 4, 0, '2025-06-12 10:40:06'),
(19, 3, 1, '2025-06-12 10:40:11'),
(20, 3, 0, '2025-06-12 10:40:22'),
(21, 4, 1, '2025-06-12 10:40:28'),
(22, 4, 0, '2025-06-12 10:40:30'),
(23, 4, 1, '2025-06-12 10:40:31'),
(24, 4, 0, '2025-06-12 10:40:35'),
(25, 1, 1, '2025-06-12 21:33:25'),
(26, 1, 0, '2025-06-12 21:33:26');

-- --------------------------------------------------------

--
-- Structure de la table `mesures`
--

CREATE TABLE `mesures` (
  `id` bigint(20) NOT NULL,
  `capteur_id` int(11) NOT NULL,
  `valeur` float NOT NULL,
  `date_heure` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `mesures`
--

INSERT INTO `mesures` (`id`, `capteur_id`, `valeur`, `date_heure`) VALUES
(1, 1, 22.5, '2025-06-11 17:14:59'),
(2, 1, 23.1, '2025-06-11 16:44:59'),
(3, 1, 21.8, '2025-06-11 16:14:59'),
(4, 2, 65.2, '2025-06-11 17:14:59'),
(5, 2, 67.1, '2025-06-11 16:44:59'),
(6, 3, 45.3, '2025-06-11 17:14:59'),
(7, 3, 42.1, '2025-06-11 16:44:59'),
(8, 4, 850.5, '2025-06-11 17:14:59'),
(9, 5, 6.8, '2025-06-11 17:14:59'),
(10, 6, 24.2, '2025-06-11 17:14:59'),
(11, 7, 68.5, '2025-06-11 17:14:59'),
(12, 8, 420.3, '2025-06-11 17:14:59'),
(13, 8, 403, '2025-06-11 19:27:48'),
(14, 8, 301, '2025-06-11 19:27:48'),
(15, 8, 498, '2025-06-11 19:27:48');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'etudiant'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `log_type` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `log_type`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:08:39\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:08:39'),
(2, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:09:38\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:09:38'),
(3, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:09:40\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:09:40'),
(4, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:09:57\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:09:57'),
(5, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 11:13:11\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:13:11'),
(6, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 11:13:25\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:13:25'),
(7, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:15:22\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:15:22'),
(8, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:15:27\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:15:27'),
(9, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:15:28\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:15:28'),
(10, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:18:15\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:18:15'),
(11, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:18:59\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:18:59'),
(12, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:19:06\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:19:06'),
(13, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:21:04\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:21:04'),
(14, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:21:08\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:21:08'),
(15, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:21:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:21:37'),
(16, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:27:09\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:27:09'),
(17, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:29:03\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:29:03'),
(18, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:29:25\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:29:25'),
(19, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 11:29:26\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:29:26'),
(20, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:30:31\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:30:31'),
(21, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:30:33\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:30:33'),
(22, NULL, 'admin_action', 'Admin::exportUsers', '{\"controller\":\"admin\",\"action\":\"exportUsers\",\"timestamp\":\"2025-06-12 11:31:09\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:31:09'),
(23, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:31:18\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:31:18'),
(24, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:31:32\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:31:32'),
(25, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:31:35\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:31:35'),
(26, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:32:41\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:32:41'),
(27, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:33:55\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:33:55'),
(28, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:37:10\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:37:10'),
(29, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 11:37:12\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:37:12'),
(30, NULL, 'admin_action', 'Admin::exportUsers', '{\"controller\":\"admin\",\"action\":\"exportUsers\",\"timestamp\":\"2025-06-12 11:38:29\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:38:29'),
(31, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:39:00\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:39:00'),
(32, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:39:25\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:39:25'),
(33, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 11:39:34\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:39:34'),
(34, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:39:41\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:39:41'),
(35, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:42:51\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:42:51'),
(36, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 11:47:12\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:47:12'),
(37, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 11:47:17\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:47:17'),
(38, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:50:41\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:41'),
(39, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 11:50:44\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:44'),
(40, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:50:48\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:48'),
(41, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 11:50:55\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:55'),
(42, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:50:56\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:56'),
(43, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 11:50:58\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:50:58'),
(44, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 11:51:00\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 09:51:00'),
(45, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:23:31\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:23:31'),
(46, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 12:23:35\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:23:35'),
(47, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:23:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:23:37'),
(48, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:24:00\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:24:00'),
(49, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:27:24\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:27:24'),
(50, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:27:38\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:27:38'),
(51, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:28:27\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:28:27'),
(52, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:29:36\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:29:36'),
(53, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 12:29:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:29:37'),
(54, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:29:41\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:29:41'),
(55, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:29:54\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:29:54'),
(56, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:30:14\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:30:14'),
(57, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:37:20\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:37:20'),
(58, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:39:13\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:39:13'),
(59, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:39:16\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:39:16'),
(60, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:39:23\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:39:23'),
(61, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:39:56\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:39:56'),
(62, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:02\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:02'),
(63, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:05\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:05'),
(64, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:06\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:06'),
(65, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:10\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:10'),
(66, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:10\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:10'),
(67, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:11\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:11'),
(68, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:17\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:17'),
(69, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:18\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:18'),
(70, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:19\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:19'),
(71, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:21\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:21'),
(72, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:22\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:22'),
(73, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:28\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:28'),
(74, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:30\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:30'),
(75, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:31\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:31'),
(76, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:33\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:33'),
(77, NULL, 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 12:40:35\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:35'),
(78, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:40:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:40:37'),
(79, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:41:54\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:41:54'),
(80, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:41:57\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:41:57'),
(81, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 12:41:58\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:41:58'),
(82, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:42:16\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:42:16'),
(83, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:42:17\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:42:17'),
(84, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:42:33\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:42:33'),
(85, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:44:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:44:37'),
(86, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:44:49\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:44:49'),
(87, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:44:51\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:44:51'),
(88, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:44:55\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:44:55'),
(89, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:44:57\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:44:57'),
(90, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:50:34\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:50:34'),
(91, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 12:50:37\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:50:37'),
(92, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:50:47\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:50:47'),
(93, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:51:38\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:51:38'),
(94, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:51:44\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:51:44'),
(95, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:51:47\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:51:47'),
(96, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 12:51:48\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:51:48'),
(97, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 12:59:53\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:59:53'),
(98, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 12:59:54\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 10:59:54'),
(99, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 13:00:01\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:01'),
(100, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 13:00:05\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:05'),
(101, NULL, 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 13:00:16\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:16'),
(102, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 13:00:18\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:18'),
(103, NULL, 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 13:00:19\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:19'),
(104, NULL, 'user_action', 'Profile::exportActivity', '{\"controller\":\"profile\",\"action\":\"exportActivity\",\"timestamp\":\"2025-06-12 13:00:21\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:21'),
(105, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 13:00:28\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:00:28'),
(106, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:02:52\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:02:52'),
(107, NULL, 'admin_action', 'Admin::manageUser', '{\"controller\":\"admin\",\"action\":\"manageUser\",\"timestamp\":\"2025-06-12 13:03:16\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:16'),
(108, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:16\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:16'),
(109, NULL, 'admin_action', 'Admin::manageUser', '{\"controller\":\"admin\",\"action\":\"manageUser\",\"timestamp\":\"2025-06-12 13:03:29\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:29'),
(110, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:29\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:29'),
(111, NULL, 'admin_action', 'Admin::manageUser', '{\"controller\":\"admin\",\"action\":\"manageUser\",\"timestamp\":\"2025-06-12 13:03:46\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:46'),
(112, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:46\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:46'),
(113, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:51\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:51'),
(114, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:54\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:54'),
(115, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:03:58\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:03:58'),
(116, NULL, 'admin_action', 'Admin::manageUser', '{\"controller\":\"admin\",\"action\":\"manageUser\",\"timestamp\":\"2025-06-12 13:04:07\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:07'),
(117, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:04:07\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:07'),
(118, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:04:08\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:08'),
(119, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:04:08\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:08'),
(120, NULL, 'admin_action', 'Admin::manageUser', '{\"controller\":\"admin\",\"action\":\"manageUser\",\"timestamp\":\"2025-06-12 13:04:25\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:25'),
(121, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:04:25\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:25'),
(122, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:04:27\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:04:27'),
(123, NULL, 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 13:11:39\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:11:39'),
(124, NULL, 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 13:47:42\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:47:42'),
(125, NULL, 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 13:47:46\",\"session_id\":\"r9s76b6qgucermdd9tr4m26j7u\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 11:47:46'),
(126, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 22:54:42\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:54:42'),
(127, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 22:54:54\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:54:54'),
(128, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 22:55:17\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:55:17'),
(129, 'admin-2025-06-12-225346', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 22:55:20\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:55:20'),
(130, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 22:55:22\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:55:22'),
(131, 'admin-2025-06-12-225346', 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 22:55:23\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:55:23'),
(132, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 22:55:27\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 20:55:27'),
(133, 'admin-2025-06-12-225346', 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 23:33:25\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:33:25'),
(134, 'admin-2025-06-12-225346', 'user_action', 'Actuator::toggle', '{\"controller\":\"actuator\",\"action\":\"toggle\",\"timestamp\":\"2025-06-12 23:33:26\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:33:26'),
(135, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 23:39:39\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:39:39'),
(136, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 23:39:49\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:39:49'),
(137, 'admin-2025-06-12-225346', 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 23:40:17\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:40:17'),
(138, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 23:41:57\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:41:57'),
(139, 'admin-2025-06-12-225346', 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-12 23:42:15\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:42:15'),
(140, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-12 23:42:21\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:42:21'),
(141, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 23:42:42\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:42:42'),
(142, 'admin-2025-06-12-225346', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 23:42:45\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:42:45'),
(143, 'admin-2025-06-12-225346', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 23:45:15\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:15'),
(144, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 23:45:23\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:23'),
(145, 'admin-2025-06-12-225346', 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-12 23:45:25\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:25'),
(146, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 23:45:31\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:31'),
(147, 'admin-2025-06-12-225346', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-12 23:45:35\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:35'),
(148, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-12 23:45:38\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 21:45:38'),
(149, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:10:37\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:10:37'),
(150, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:13:00\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:13:00');
INSERT INTO `system_logs` (`id`, `user_id`, `log_type`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(151, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:13:03\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:13:03'),
(152, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:13:03\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:13:03'),
(153, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:22:46\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:22:46'),
(154, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 00:23:33\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:33'),
(155, 'admin-2025-06-12-225346', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-13 00:23:34\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:34'),
(156, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 00:23:35\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:35'),
(157, 'admin-2025-06-12-225346', 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-13 00:23:36\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:36'),
(158, 'admin-2025-06-12-225346', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 00:23:37\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:37'),
(159, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:23:41\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:41'),
(160, 'admin-2025-06-12-225346', 'admin_action', 'Admin::users', '{\"controller\":\"admin\",\"action\":\"users\",\"timestamp\":\"2025-06-13 00:23:44\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:23:44'),
(161, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:25:19\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:25:19'),
(162, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:25:26\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:25:26'),
(163, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:29:13\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:29:13'),
(164, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:29:19\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:29:19'),
(165, 'admin-2025-06-12-225346', 'user_action', 'Actuator::manage', '{\"controller\":\"actuator\",\"action\":\"manage\",\"timestamp\":\"2025-06-13 00:29:21\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:29:21'),
(166, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 00:40:38\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 22:40:38'),
(167, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 01:39:27\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 23:39:27'),
(168, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::notifications', '{\"controller\":\"profile\",\"action\":\"notifications\",\"timestamp\":\"2025-06-13 01:39:32\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 23:39:32'),
(169, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 01:39:33\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 23:39:33'),
(170, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::activity', '{\"controller\":\"profile\",\"action\":\"activity\",\"timestamp\":\"2025-06-13 01:39:34\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 23:39:34'),
(171, 'cf652b17-a216-4226-84d4-f66e651d5571', 'user_action', 'Profile::index', '{\"controller\":\"profile\",\"action\":\"index\",\"timestamp\":\"2025-06-13 01:39:36\",\"session_id\":\"pheeul8m6kugv6r2hd1s2iqm6l\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-12 23:39:36');

-- --------------------------------------------------------

--
-- Structure de la table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_maintenance', 'false', 'Mode maintenance du site', '2025-06-12 09:07:49'),
(2, 'max_upload_size', '10485760', 'Taille maximale de fichier en octets', '2025-06-12 09:07:49'),
(3, 'session_timeout', '3600', 'Durée de session en secondes', '2025-06-12 09:07:49'),
(4, 'auto_refresh_interval', '30', 'Intervalle de rafraîchissement automatique en secondes', '2025-06-12 09:07:49'),
(5, 'theme_default', 'auto', 'Thème par défaut du site', '2025-06-12 09:07:49');

-- --------------------------------------------------------

--
-- Structure de la table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `greenhouse_sector` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `teams`
--

INSERT INTO `teams` (`id`, `name`, `greenhouse_sector`) VALUES
(1, 'Équipe Alpha', 'Secteur A'),
(2, 'Équipe Beta', 'Secteur B'),
(3, 'Équipe Gamma', 'Secteur C'),
(4, 'Équipe Delta', 'Secteur D'),
(5, 'Équipe Epsilon', 'Secteur E');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `role_id`) VALUES
('admin-2025-06-12-225346', 'admin', '$2y$10$W26UV0mEdXFp6FZccWAfYeKUTOTQoq7AHY/rvhb6FjPk.NLdfv5pe', 2),
('cf652b17-a216-4226-84d4-f66e651d5571', 'fafa', '$2y$10$mI8yf0iPI9VJkbv4XQKAlOsCYjVgy.ZnGZFBrUZGQWlsH/lddouP6', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` char(36) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `notification_email` tinyint(1) DEFAULT 1,
  `notification_browser` tinyint(1) DEFAULT 1,
  `theme_preference` varchar(20) DEFAULT 'auto',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `email`, `first_name`, `last_name`, `phone`, `team_id`, `notification_email`, `notification_browser`, `theme_preference`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 'cf652b17-a216-4226-84d4-f66e651d5571', 'fafa@outlook.fr', '', '', NULL, 2, 1, 1, 'auto', '2025-06-12 22:40:17', '2025-06-12 22:40:17', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actionneurs`
--
ALTER TABLE `actionneurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_team_id` (`team_id`);

--
-- Index pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `last_activity` (`last_activity`);

--
-- Index pour la table `actuator_logs`
--
ALTER TABLE `actuator_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actionneur_id` (`actionneur_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Index pour la table `capteurs`
--
ALTER TABLE `capteurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_team_id` (`team_id`),
  ADD KEY `idx_type` (`type`);

--
-- Index pour la table `etats_actionneurs`
--
ALTER TABLE `etats_actionneurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actionneur_id` (`actionneur_id`),
  ADD KEY `date_heure` (`date_heure`);

--
-- Index pour la table `mesures`
--
ALTER TABLE `mesures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `capteur_id` (`capteur_id`),
  ADD KEY `date_heure` (`date_heure`),
  ADD KEY `idx_capteur_date` (`capteur_id`,`date_heure`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `created_at` (`created_at`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `log_type` (`log_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Index pour la table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Index pour la table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `user_role_FK` (`role_id`);

--
-- Index pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actionneurs`
--
ALTER TABLE `actionneurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `actuator_logs`
--
ALTER TABLE `actuator_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `etats_actionneurs`
--
ALTER TABLE `etats_actionneurs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `mesures`
--
ALTER TABLE `mesures`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT pour la table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actionneurs`
--
ALTER TABLE `actionneurs`
  ADD CONSTRAINT `actionneurs_team_FK` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `actuator_logs`
--
ALTER TABLE `actuator_logs`
  ADD CONSTRAINT `actuator_logs_actionneur_FK` FOREIGN KEY (`actionneur_id`) REFERENCES `actionneurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actuator_logs_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `capteurs`
--
ALTER TABLE `capteurs`
  ADD CONSTRAINT `capteurs_team_FK` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `etats_actionneurs`
--
ALTER TABLE `etats_actionneurs`
  ADD CONSTRAINT `etats_actionneurs_ibfk_1` FOREIGN KEY (`actionneur_id`) REFERENCES `actionneurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mesures`
--
ALTER TABLE `mesures`
  ADD CONSTRAINT `mesures_ibfk_1` FOREIGN KEY (`capteur_id`) REFERENCES `capteurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_role_FK` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Contraintes pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_team_FK` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_profiles_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
