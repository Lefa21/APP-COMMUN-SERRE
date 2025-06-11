-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 11 juin 2025 à 23:29
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
(2, 'Ventilation A', 'ventilation', 1, 1, 0, '2025-06-11 17:14:59'),
(3, 'Éclairage A', 'lighting', 1, 1, 0, '2025-06-11 17:14:59'),
(4, 'Chauffage A', 'heating', 1, 1, 0, '2025-06-11 17:14:59'),
(5, 'Arrosage Automatique B', 'irrigation', 2, 1, 0, '2025-06-11 17:14:59'),
(6, 'Ventilation B', 'ventilation', 2, 1, 0, '2025-06-11 17:14:59'),
(7, 'Éclairage B', 'lighting', 2, 1, 0, '2025-06-11 17:14:59');

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
(1, 1, 'ON', '0ecf79c1-037d-47bd-a9e0-b94b2ed574b7', '2025-06-11 18:14:25'),
(2, 1, 'OFF', '0ecf79c1-037d-47bd-a9e0-b94b2ed574b7', '2025-06-11 18:14:27'),
(3, 1, 'ON', '0ecf79c1-037d-47bd-a9e0-b94b2ed574b7', '2025-06-11 18:14:28'),
(4, 1, 'OFF', '0ecf79c1-037d-47bd-a9e0-b94b2ed574b7', '2025-06-11 18:14:32'),
(5, 1, 'ON', 'admin-2025-06-11-212535', '2025-06-11 20:10:29'),
(6, 1, 'OFF', 'admin-2025-06-11-212535', '2025-06-11 20:10:30'),
(7, 1, 'ON', 'admin-2025-06-11-212535', '2025-06-11 20:10:31'),
(8, 1, 'OFF', 'admin-2025-06-11-212535', '2025-06-11 20:10:34'),
(9, 4, 'ON', 'admin-2025-06-11-212535', '2025-06-11 20:11:10'),
(10, 4, 'OFF', 'admin-2025-06-11-212535', '2025-06-11 20:11:15'),
(11, 1, 'ON', 'admin-2025-06-11-212535', '2025-06-11 20:27:34'),
(12, 1, 'OFF', 'admin-2025-06-11-212535', '2025-06-11 20:27:43');

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
(12, 1, 0, '2025-06-11 20:27:43');

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
('0ecf79c1-037d-47bd-a9e0-b94b2ed574b7', 'tounzi', '$2y$10$gsgAks/YcfBiHM9.SiGjueRWRuVJFinh1rvvMkA7rpUoxcAX52rOS', 1),
('3c87acae-46f8-11f0-9aa9-d843aebf358c', 'ad', 'admin75011', 2),
('8407d164-98e3-462d-b7cb-fb403cd29a47', 'Guillaume', '$2y$10$gnOxlWuFQm/oGVMOLldbtOAqffh2IRfge0gYAdnXqMjqrTnHbCWi.', 1),
('admin-2025-06-11-212535', 'admin', '$2y$10$NTAO83v0rCfqJ1K.NisrZ.voareG0fASYlL6aIsL8k211BAkhDlBG', 2),
('ba8ffe54-d840-4e14-8da4-28b015958150', 'test', '$2y$10$yle5g2t6cWg0T8GSrd33Q.qoDlBZjH6KBWhRjyuMEq564NzQ3iiq2', 1);

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
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actionneurs`
--
ALTER TABLE `actionneurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `actuator_logs`
--
ALTER TABLE `actuator_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `etats_actionneurs`
--
ALTER TABLE `etats_actionneurs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `mesures`
--
ALTER TABLE `mesures`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actionneurs`
--
ALTER TABLE `actionneurs`
  ADD CONSTRAINT `actionneurs_team_FK` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

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
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_role_FK` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
