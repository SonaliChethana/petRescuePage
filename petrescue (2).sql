-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2024 at 04:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petrescue`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoptdog`
--

CREATE TABLE `adoptdog` (
  `id` int(11) NOT NULL,
  `dog_name` varchar(100) NOT NULL,
  `your_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoptdog`
--

INSERT INTO `adoptdog` (`id`, `dog_name`, `your_name`, `email`, `submission_date`) VALUES
(1, 'lll', 'mike', 'kk@gmail.com', '2024-07-16 12:15:57'),
(2, 'Lucy', 'Kasun', 'kasun@gmail.com', '2024-07-16 12:30:51'),
(3, 'Rocky', 'Deshan', 'deshan@gmail.com', '2024-07-20 09:23:04'),
(4, 'Max', 'Himesh', 'himesh@gmail.com', '2024-07-21 12:01:17'),
(5, 'Rocky', 'Gayan', 'gayan@gmail.com', '2024-07-22 15:05:01'),
(6, 'Lucy', 'pavi', 'pavi@gmail.com', '2024-07-24 06:42:33'),
(7, 'Max', 'Raveena', 'ravi@gmail.com', '2024-07-24 06:43:43');

-- --------------------------------------------------------

--
-- Table structure for table `animal_shelters`
--

CREATE TABLE `animal_shelters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shelter_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `description` text NOT NULL,
  `services` text NOT NULL,
  `open_hours` varchar(255) NOT NULL,
  `photos` varchar(255) DEFAULT NULL,
  `videos` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dogprofile`
--

CREATE TABLE `dogprofile` (
  `id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `dog_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dogprofile`
--

INSERT INTO `dogprofile` (`id`, `photo`, `age`, `dog_name`, `description`, `video`, `gender`, `created_at`) VALUES
(1, 'buddy.jpg', 5, 'Buddy', 'Brown color', '561f25ba2c53b44c2e7e303deb243c72.mp4', 'male', '2024-07-19 12:16:18'),
(2, 'ella.jpg', 3, 'Ella', 'Cute adorable', '561f25ba2c53b44c2e7e303deb243c72.mp4', 'female', '2024-07-19 12:21:30'),
(3, 'lucy.jpg', 6, 'Lucy', 'While and brown color', '561f25ba2c53b44c2e7e303deb243c72.mp4', 'male', '2024-07-19 12:25:57'),
(4, 'Rocky.jpg', 3, 'Rocky', 'Small puppy', '', 'male', '2024-07-19 12:34:19'),
(5, 'maxdog.jpg', 5, 'Max', 'Friendly', '', 'male', '2024-07-19 12:35:45'),
(6, 'max.jpg', 3, 'Max Puppy', 'Samll puppy', '', 'male', '2024-07-19 14:37:02'),
(7, 'snovy.jpg', 2, 'Snovy', 'White color puppy.', '', 'male', '2024-07-21 12:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `donor_email` varchar(255) NOT NULL,
  `donation_amount` decimal(10,2) NOT NULL,
  `frequency` enum('one-time','monthly') NOT NULL,
  `additional_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergencyreport`
--

CREATE TABLE `emergencyreport` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `photos` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Status',
  `priority` varchar(20) NOT NULL DEFAULT 'low',
  `responsible_entity` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lostandfound`
--

CREATE TABLE `lostandfound` (
  `id` int(11) NOT NULL,
  `dog_name` varchar(100) NOT NULL,
  `dog_age` varchar(20) DEFAULT NULL,
  `dog_description` text DEFAULT NULL,
  `last_seen_location` varchar(255) DEFAULT NULL,
  `photos` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `status` varchar(10) DEFAULT 'missing',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `card_type` varchar(50) NOT NULL,
  `card_name` varchar(100) NOT NULL,
  `card_number` varchar(16) NOT NULL,
  `expiry_date` varchar(5) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `zip_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `card_type`, `card_name`, `card_number`, `expiry_date`, `cvv`, `billing_address`, `zip_code`) VALUES
(4, 'visa', 'feffeg11', '1111111111111111', '11/11', '111', 'ferfre', '11111'),
(5, 'amex', 'fewtretrt', '1111111111111111', '11/11', '111', 'frgfr', '22222'),
(6, 'visa', 'fgg', '1111111111111111', '11/11', '111', 'vfdgfdg', '44444'),
(7, 'mastercard', 'vvvvvv', '3333333333333333', '33/33', '333', 'dddd', '33333'),
(8, 'visa', 'fbdfg', '3333333333333333', '33/33', '333', 'frgr', '55555'),
(9, 'visa', 'Pramodya Athauda', '1111111111111111', '23/25', '361', 'NO.12, WAWEGEDARA', '11200'),
(10, 'visa', 'pramodya', '2222222222222222', '22/25', '333', 'NO.12, WAWEGEDARA', '11200');

-- --------------------------------------------------------

--
-- Table structure for table `profilesettings`
--

CREATE TABLE `profilesettings` (
  `profile_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `preferences` text DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `user_role` enum('dogLover','vetClinic','animalShelter') NOT NULL,
  `clinic_name` varchar(255) DEFAULT NULL,
  `shelter_name` varchar(255) DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `account_created` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `has_vet_clinic` tinyint(1) DEFAULT 0,
  `has_animal_shelter` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `full_name`, `email`, `username`, `password`, `location`, `preferences`, `gender`, `user_role`, `clinic_name`, `shelter_name`, `verification_code`, `is_verified`, `account_created`, `reset_token`, `reset_expiry`, `has_vet_clinic`, `has_animal_shelter`) VALUES
(1, 'Imesha', 'imesha@gmail.com', 'Imesha', '$2y$10$uTpsGf2CaqJRmo24yThnm.IgrbqrdmwR9YZ8Uu14Q8QRr.rBXXnqO', 'colombo', 'Email', NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(2, 'Kasun', 'kasun@gmail.com', 'Kasun', '$2y$10$X6Y94NPpwHFObjeQy2XjiuE2Q1rS2wRGuMcOybkpVTxNNFDoyLj7G', 'Galle', 'Email', NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(4, 'dilshan', 'dilshan@gmail.com', 'Dilshan', '$2y$10$cjABWweriHl5frUv5fYo4uL9EwvHqU3VaL6ObdewncfiRdkO5gEj6', 'Negombo', '', NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(8, 'Sara', 'sara@gmail.com', 'Sara', '$2y$10$mWNzGw/vYmYB5r2kNf74memrGkDRlYGN1xnMm5lFZOX4ZLZOQL2LG', NULL, 'Email', 'Female', 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(13, 'amal perera', 'amalp@gmail.com', 'amalp', '$2y$10$pINCzR5DVbjvTmbWZwepwOJbOezvROTtKGWeBj59b7UcYcYmt7sha', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(15, 'gihan', 'gihan@gmail.com', 'gihan33', '$2y$10$gnQhP0lCuhnT1UYymOp7n.FcZ6fNNkQaGZutMCVNAr54tXt.e50s2', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(17, 'emma', 'emma@gmail.com', 'emma22', '$2y$10$sBNt2dulu7VP8.ZV4/flauTAeezCxRGBsKLft2F5txRjJCC.ocEhe', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(18, 'emily', 'emily@gmail.com', 'emily', '$2y$10$JqF/j7Nf6426xVQKBVGWW.liaL0VqDqC7VDPmXvDLPzi6cfg4O5QC', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(19, 'terry', 'terry@gmail.com', 'terry666', '$2y$10$dSUX6YS6paHJwpLA.ogHRunlN1dEUreN/BqzIXZdFwaBohWLEaN3u', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(20, 'jane', 'jane@gmail.com', 'jane233', '$2y$10$lS36/IdTFhT3w8pJwiDEeeEQynM7rURKlnW/VHoWfP8DaqlWlkLDK', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(21, 'joanne', 'joanne@gmail.com', 'joanne', '$2y$10$O4MjpqUaloCGRCOUM4hvgOvnxdPBr5vPr8o1.nM3yN1oIsY3k79Xe', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(22, '', 'dogstar@gmail.com', 'dogstar', '$2y$10$5cJKQ1wC4Hh9a6g3wZnlyeIOJx1fvjIard/KQ6Oxay4IGuMFD2Svi', NULL, NULL, NULL, 'animalShelter', NULL, 'dogstar', NULL, 0, 0, NULL, NULL, 0, 0),
(23, 'erik', 'erik@gmail.com', 'erik', '$2y$10$xI/tOqby3jsq7hKNWmUYJeyyXqLh0M33IvfSV3XIlYaS6Ulj22A3W', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(24, '', 'vision@gmail.com', 'vision', '$2y$10$fBXi7aALbawGEPL8bW9eIOpmH1v6cT96q3puvBVB3Uu6mRXHCTjJu', NULL, NULL, NULL, 'vetClinic', 'vision animal hospital', NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(26, '', 'bawbaw@gmail.com', 'BawBaw animal shelter', '$2y$10$hl80UMBht6/wD.liQi9KmO0Tx2FDOPTZnj03FFxk7zF1Raa7TPVhW', NULL, NULL, NULL, 'animalShelter', NULL, 'bawbaw animal shelter', NULL, 0, 0, NULL, NULL, 0, 0),
(27, 'Kushani Athukorala', 'kushani1988@gmail.com', 'Kushani88', '$2y$10$/bz.QAWebi7h5Tf6SNK16uXtdY10l.NwwaqICEIXmZKw1SblzOUcq', NULL, NULL, NULL, 'dogLover', NULL, NULL, NULL, 0, 0, NULL, NULL, 0, 0),
(71, '', 'pramodya511@gmail.com', 'vision vet clinic', '$2y$10$7.yht0gX4w67iMjkcZlgheDN5WadVHugYFutDpnS029d.nZOGTRaK', NULL, NULL, NULL, 'vetClinic', 'vision animal hospital', NULL, 'd01d27559532d65340165324a4eb424c', 1, 1, NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reportstray`
--

CREATE TABLE `reportstray` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `photos` text DEFAULT NULL,
  `behaviour` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Status',
  `claimed_by` varchar(255) DEFAULT NULL,
  `responsible_entity` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shelterfoster`
--

CREATE TABLE `shelterfoster` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `geolocation` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `availability` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelterfoster`
--

INSERT INTO `shelterfoster` (`id`, `name`, `type`, `contact_person`, `phone_number`, `email`, `address`, `geolocation`, `capacity`, `availability`) VALUES
(5, 'Dog shelter', 'Animal Shelter', 'Nimal', '075-56932154', 'nimal@gmail.com', 'colombo', 'colombo', 50, '12'),
(6, 'Shelter', '', 'Nimali', '011-2596325', 'nimali@gmail.com', 'galle', '', 32, '10'),
(7, 'Shelter pet', 'Animal Shelter', 'Sunil', '076-5469325', 'sunil@gmail.com', 'Dehiwala', '', 42, '20'),
(8, 'Foster home', 'Foster Home', 'Danush', '072-2563987', 'danush@gmail.com', 'Ampara', '', 56, '15'),
(17, 'Dog shelter ', 'Animal Shelter', 'Nimal', ' 075-56932154', 'nimal@gmail.com', 'colombo', '', 22, '14'),
(18, 'Paw shelter', 'Foster Home', 'Samadhi', '072-663594', 'paw@gmail.com', 'Colombo-7', '', 25, '12');

-- --------------------------------------------------------

--
-- Table structure for table `vet_clinics`
--

CREATE TABLE `vet_clinics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clinic_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `description` text NOT NULL,
  `services` text NOT NULL,
  `open_hours` varchar(255) NOT NULL,
  `photos` varchar(255) DEFAULT NULL,
  `videos` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vet_clinics`
--

INSERT INTO `vet_clinics` (`id`, `user_id`, `clinic_name`, `location`, `contact_number`, `description`, `services`, `open_hours`, `photos`, `videos`, `created_at`) VALUES
(23, 71, 'vision vet clinic', 'mirigama', '0778546345', 'vision vet clinic', 'all services', '{\"open\":\"08:00 AM\",\"close\":\"08:00 PM\",\"additional_info\":\"\"}', '[\"uploads\\/vet_clinic_photos\\/Adoptdod.jpeg\",\"uploads\\/vet_clinic_photos\\/AdoptIcon.jpg\",\"uploads\\/vet_clinic_photos\\/adoption.jpeg\",\"uploads\\/vet_clinic_photos\\/adoption.jpg\",\"uploads\\/vet_clinic_photos\\/assess.png\"]', '[]', '2024-09-10 14:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `experience` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`id`, `name`, `age`, `location`, `experience`, `created_at`) VALUES
(1, 'Savi', 20, 'Colombo', 'Not any', '2024-07-15 09:58:29'),
(2, 'Amashi', 19, 'Kurunagale', 'Assistant worker in a vet clinic', '2024-07-15 10:09:28'),
(3, 'pramodya', 22, 'No.12, Wawegedara, kosatadeniya, Mirigama', 'a volunteer at embark', '2024-09-09 05:17:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoptdog`
--
ALTER TABLE `adoptdog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `animal_shelters`
--
ALTER TABLE `animal_shelters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dogprofile`
--
ALTER TABLE `dogprofile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergencyreport`
--
ALTER TABLE `emergencyreport`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_registration` (`user_id`);

--
-- Indexes for table `lostandfound`
--
ALTER TABLE `lostandfound`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_lost` (`user_id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profilesettings`
--
ALTER TABLE `profilesettings`
  ADD PRIMARY KEY (`profile_id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `reportstray`
--
ALTER TABLE `reportstray`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_claim` (`id`,`claimed_by`),
  ADD KEY `fk_user_id_registration` (`user_id`);

--
-- Indexes for table `shelterfoster`
--
ALTER TABLE `shelterfoster`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vet_clinics`
--
ALTER TABLE `vet_clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoptdog`
--
ALTER TABLE `adoptdog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `animal_shelters`
--
ALTER TABLE `animal_shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `dogprofile`
--
ALTER TABLE `dogprofile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `emergencyreport`
--
ALTER TABLE `emergencyreport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `lostandfound`
--
ALTER TABLE `lostandfound`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `reportstray`
--
ALTER TABLE `reportstray`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `shelterfoster`
--
ALTER TABLE `shelterfoster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `vet_clinics`
--
ALTER TABLE `vet_clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animal_shelters`
--
ALTER TABLE `animal_shelters`
  ADD CONSTRAINT `animal_shelters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`);

--
-- Constraints for table `emergencyreport`
--
ALTER TABLE `emergencyreport`
  ADD CONSTRAINT `fk_user_id_emergency` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lostandfound`
--
ALTER TABLE `lostandfound`
  ADD CONSTRAINT `fk_user_id_lost` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profilesettings`
--
ALTER TABLE `profilesettings`
  ADD CONSTRAINT `profilesettings_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reportstray`
--
ALTER TABLE `reportstray`
  ADD CONSTRAINT `fk_user_id_registration` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vet_clinics`
--
ALTER TABLE `vet_clinics`
  ADD CONSTRAINT `vet_clinics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
