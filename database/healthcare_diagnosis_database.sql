-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2025 at 12:16 PM
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
-- Database: `healthcare_diagnosis_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE `conditions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`id`, `name`, `description`, `symptoms`, `recommendations`) VALUES
(1, 'Common Cold', 'A viral infection of the upper respiratory tract', 'Runny nose, sneezing, congestion, sore throat, cough, mild fatigue', 'Rest, stay hydrated, over-the-counter cold medications, saltwater gargle'),
(2, 'Influenza', 'A viral infection that attacks your respiratory system', 'High fever, severe body aches, fatigue, headache, dry cough, chills', 'Rest, fluids, antiviral medications if prescribed, avoid contact with others'),
(3, 'Migraine', 'A severe headache disorder causing intense throbbing pain', 'Severe headache, sensitivity to light and sound, nausea, visual auras', 'Rest in dark room, stay hydrated, prescribed medications, identify triggers'),
(4, 'Gastroenteritis', 'Inflammation of the digestive system', 'Nausea, vomiting, diarrhea, stomach cramps, mild fever', 'Stay hydrated, bland diet, rest, probiotics'),
(5, 'Hypertension', 'High blood pressure condition', 'Headaches, shortness of breath, nosebleeds, chest pain', 'Regular exercise, low-sodium diet, medication if prescribed, stress management'),
(6, 'Type 2 Diabetes', 'Metabolic disorder affecting blood sugar levels', 'Increased thirst, frequent urination, fatigue, blurred vision', 'Regular blood sugar monitoring, balanced diet, exercise, medications'),
(7, 'Asthma', 'Chronic respiratory condition', 'Wheezing, shortness of breath, chest tightness, coughing', 'Use prescribed inhalers, avoid triggers, regular check-ups'),
(8, 'Acid Reflux', 'Digestive disorder causing stomach acid backflow', 'Heartburn, chest pain, difficulty swallowing, regurgitation', 'Avoid trigger foods, eat smaller meals, maintain healthy weight'),
(9, 'Allergic Rhinitis', 'Allergic reaction affecting nasal passages', 'Sneezing, runny nose, itchy eyes, congestion', 'Avoid allergens, antihistamines, nasal sprays'),
(10, 'Bronchitis', 'Inflammation of bronchial tubes', 'Persistent cough, chest congestion, fatigue, mild fever', 'Rest, increased fluid intake, humidifier use, over-the-counter medications'),
(11, 'Anxiety Disorder', 'Mental health condition causing excessive worry', 'Excessive worry, restlessness, difficulty concentrating, sleep problems', 'Therapy, stress management, medication if prescribed, regular exercise'),
(12, 'Depression', 'Mental health disorder affecting mood', 'Persistent sadness, loss of interest, sleep changes, fatigue', 'Professional counseling, medication if prescribed, regular exercise, social support'),
(13, 'Osteoarthritis', 'Degenerative joint disease', 'Joint pain, stiffness, reduced flexibility, swelling', 'Physical therapy, exercise, weight management, pain medications'),
(14, 'Sinusitis', 'Inflammation of sinus cavities', 'Facial pain, nasal congestion, headache, reduced smell', 'Nasal irrigation, decongestants, antibiotics if bacterial'),
(15, 'Urinary Tract Infection', 'Infection in urinary system', 'Frequent urination, burning sensation, cloudy urine, pelvic pain', 'Antibiotics, increased fluid intake, cranberry supplements'),
(16, 'Insomnia', 'Sleep disorder', 'Difficulty falling asleep, waking up frequently, daytime fatigue', 'Regular sleep schedule, relaxation techniques, avoid caffeine'),
(17, 'Dermatitis', 'Skin inflammation', 'Itching, rash, redness, skin inflammation', 'Moisturize regularly, avoid triggers, topical treatments'),
(18, 'Vertigo', 'Balance disorder', 'Dizziness, spinning sensation, nausea, balance problems', 'Vestibular rehabilitation, medications, lifestyle modifications'),
(19, 'Hypothyroidism', 'Underactive thyroid condition', 'Fatigue, weight gain, cold sensitivity, dry skin', 'Thyroid hormone replacement, regular monitoring, healthy diet'),
(20, 'Carpal Tunnel Syndrome', 'Nerve compression in wrist', 'Wrist pain, numbness, tingling in fingers, weakness', 'Wrist splints, ergonomic adjustments, physical therapy'),
(21, 'Irritable Bowel Syndrome', 'Digestive disorder affecting bowel function', 'Abdominal pain, bloating, diarrhea or constipation', 'Dietary changes, stress management, fiber supplements'),
(22, 'Tension Headache', 'Common headache type', 'Dull head pain, tight sensation, neck stiffness', 'Pain relievers, stress management, proper posture'),
(23, 'Conjunctivitis', 'Eye inflammation', 'Red eyes, itching, discharge, tearing', 'Eye drops, warm compresses, avoid touching eyes'),
(24, 'Gout', 'Form of inflammatory arthritis', 'Severe joint pain, swelling, redness, limited movement', 'Medications, dietary changes, weight management'),
(25, 'Peptic Ulcer', 'Sores in digestive tract lining', 'Stomach pain, bloating, heartburn, nausea', 'Antibiotics if H. pylori present, acid reducers, dietary changes'),
(26, 'Tonsillitis', 'Inflammation of tonsils', 'Sore throat, difficulty swallowing, fever, swollen lymph nodes', 'Rest, warm liquids, pain relievers, antibiotics if bacterial'),
(27, 'Sciatica', 'Nerve pain condition', 'Lower back pain, leg pain, numbness, weakness', 'Physical therapy, pain management, exercise, proper posture'),
(28, 'Hypoglycemia', 'Low blood sugar condition', 'Shakiness, sweating, confusion, hunger', 'Regular meals, glucose monitoring, balanced diet'),
(29, 'Plantar Fasciitis', 'Foot inflammation condition', 'Heel pain, arch pain, morning foot stiffness', 'Stretching exercises, supportive footwear, ice therapy'),
(30, 'Anemia', 'Blood disorder affecting red blood cells', 'Fatigue, weakness, shortness of breath, pale skin', 'Iron supplements, dietary changes, vitamin B12 if needed');

-- --------------------------------------------------------

--
-- Table structure for table `diagnoses`
--

CREATE TABLE `diagnoses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `diagnosis_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `condition_id` (`condition_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conditions`
--
ALTER TABLE `conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `diagnoses`
--
ALTER TABLE `diagnoses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD CONSTRAINT `diagnoses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `diagnoses_ibfk_2` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
