-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 10:02 PM
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
  `condition_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `severity_base` int(11) DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`condition_id`, `name`, `description`, `severity_base`, `created_at`, `updated_at`) VALUES
(1, 'Hypertension', 'High blood pressure condition where blood pressure is consistently too high', 60, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(2, 'Coronary Artery Disease', 'Narrowing of coronary arteries reducing blood flow to the heart', 75, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(3, 'Asthma', 'Chronic condition causing airways to become inflamed, narrow and swell', 55, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(4, 'COPD', 'Chronic inflammatory lung disease causing obstructed airflow', 70, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(5, 'Migraine', 'Severe recurring headache, often with nausea and sensitivity to light', 45, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(6, 'Parkinsons Disease', 'Progressive nervous system disorder affecting movement', 80, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(7, 'Type 2 Diabetes', 'Chronic condition affecting how body metabolizes glucose', 65, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(8, 'Hypothyroidism', 'Condition where thyroid gland doesnt produce enough hormones', 50, '2025-05-22 11:08:30', '2025-05-22 11:08:30'),
(9, 'Rheumatoid Arthritis', 'Autoimmune disorder causing joint inflammation', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(10, 'Lupus', 'Systemic autoimmune disease affecting multiple organs', 75, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(11, 'Multiple Sclerosis', 'Immune system attacks protective covering of nerves', 80, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(12, 'Psoriasis', 'Immune-mediated disease causing skin inflammation', 50, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(13, 'Crohns Disease', 'Inflammatory bowel disease affecting digestive tract', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(14, 'Ulcerative Colitis', 'Inflammatory bowel disease affecting colon and rectum', 65, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(15, 'Celiac Disease', 'Immune reaction to eating gluten', 60, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(16, 'GERD', 'Chronic acid reflux disease', 45, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(17, 'IBS', 'Irritable Bowel Syndrome affecting large intestine', 40, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(18, 'Major Depression', 'Severe depressive disorder affecting daily life', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(19, 'Generalized Anxiety Disorder', 'Persistent and excessive worry', 65, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(20, 'Bipolar Disorder', 'Condition causing extreme mood swings', 75, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(21, 'PTSD', 'Post-traumatic stress disorder', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(22, 'Hyperthyroidism', 'Overactive thyroid gland', 60, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(23, 'Cushings Syndrome', 'High levels of cortisol in the body', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(24, 'Addisons Disease', 'Inadequate production of certain hormones', 75, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(25, 'Pneumonia', 'Infection causing inflammation of air sacs in lungs', 70, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(26, 'Bronchitis', 'Inflammation of bronchial tubes', 55, '2025-05-22 11:10:20', '2025-05-22 11:10:20'),
(27, 'Sleep Apnea', 'Breathing repeatedly stops during sleep', 60, '2025-05-22 11:10:20', '2025-05-22 11:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `condition_symptoms`
--

CREATE TABLE `condition_symptoms` (
  `condition_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `weight` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condition_symptoms`
--

INSERT INTO `condition_symptoms` (`condition_id`, `symptom_id`, `weight`) VALUES
(1, 3, 0.7),
(1, 5, 0.8),
(1, 10, 0.6),
(3, 5, 1),
(3, 6, 0.7),
(3, 7, 0.9),
(7, 2, 0.8),
(7, 4, 0.5),
(7, 9, 0.7),
(9, 2, 0.8),
(9, 14, 1),
(9, 16, 0.7),
(13, 17, 1),
(13, 19, 0.9),
(13, 21, 0.7),
(18, 26, 1),
(18, 27, 0.9),
(18, 28, 0.8);

-- --------------------------------------------------------

--
-- Table structure for table `diagnosis`
--

CREATE TABLE `diagnosis` (
  `flddiagnosisid` int(11) NOT NULL,
  `flduserid` int(11) DEFAULT NULL,
  `fldconditionid` int(11) DEFAULT NULL,
  `flddiagnosisdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diagnosis_history`
--

CREATE TABLE `diagnosis_history` (
  `diagnosis_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `severity` int(11) DEFAULT NULL,
  `main_symptom` varchar(255) DEFAULT NULL,
  `additional_symptoms` text DEFAULT NULL,
  `diagnosis_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnosis_history`
--

INSERT INTO `diagnosis_history` (`diagnosis_id`, `user_id`, `condition_id`, `severity`, `main_symptom`, `additional_symptoms`, `diagnosis_date`) VALUES
(1, 1, 1, 65, 'Headache', 'Dizziness, Shortness of Breath', '2025-05-22 11:08:30'),
(2, 2, 3, 70, 'Shortness of Breath', 'Chest Pain, Chronic Cough', '2025-05-22 11:08:30'),
(3, 3, 7, 55, 'Fatigue', 'Increased thirst, Frequent urination', '2025-05-22 11:08:30');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `recommendation` text NOT NULL,
  `priority` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`recommendation_id`, `condition_id`, `recommendation`, `priority`) VALUES
(1, 1, 'Monitor blood pressure regularly', 1),
(2, 1, 'Reduce sodium intake', 2),
(3, 1, 'Maintain regular physical activity', 3),
(4, 3, 'Keep rescue inhaler readily available', 1),
(5, 3, 'Avoid known triggers', 2),
(6, 3, 'Follow prescribed medication schedule', 3),
(7, 7, 'Monitor blood glucose levels regularly', 1),
(8, 7, 'Maintain a balanced diet', 2),
(9, 7, 'Exercise regularly as advised by healthcare provider', 3),
(10, 9, 'Take prescribed anti-inflammatory medications', 1),
(11, 9, 'Engage in gentle exercise as tolerated', 2),
(12, 9, 'Protect joints during daily activities', 3),
(13, 13, 'Follow prescribed medication regimen', 1),
(14, 13, 'Maintain food diary to identify triggers', 2),
(15, 13, 'Stay hydrated and avoid trigger foods', 3),
(16, 18, 'Continue prescribed antidepressant medication', 1),
(17, 18, 'Engage in regular psychotherapy sessions', 2),
(18, 18, 'Maintain regular sleep schedule', 3);

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `symptom_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`symptom_id`, `name`, `category`, `description`, `created_at`) VALUES
(1, 'Fever', 'general', 'Elevated body temperature above 38°C (100.4°F)', '2025-05-22 11:08:30'),
(2, 'Fatigue', 'general', 'Feeling of tiredness and reduced energy', '2025-05-22 11:08:30'),
(3, 'Headache', 'pain', 'Pain in any region of the head', '2025-05-22 11:08:30'),
(4, 'Nausea', 'digestive', 'Sensation of unease in the stomach with urge to vomit', '2025-05-22 11:08:30'),
(5, 'Shortness of Breath', 'respiratory', 'Difficulty breathing or dyspnea', '2025-05-22 11:08:30'),
(6, 'Chest Pain', 'pain', 'Discomfort or pain in the chest area', '2025-05-22 11:08:30'),
(7, 'Chronic Cough', 'respiratory', 'Persistent cough lasting more than 8 weeks', '2025-05-22 11:08:30'),
(8, 'Irregular Heartbeat', 'cardiovascular', 'Arrhythmia or abnormal heart rhythm', '2025-05-22 11:08:30'),
(9, 'Swelling', 'cardiovascular', 'Edema in extremities', '2025-05-22 11:08:30'),
(10, 'Dizziness', 'neurological', 'Feeling light-headed or unsteady', '2025-05-22 11:08:30'),
(11, 'Memory Loss', 'neurological', 'Difficulty remembering recent or past events', '2025-05-22 11:08:30'),
(12, 'Tremors', 'neurological', 'Involuntary shaking movements', '2025-05-22 11:08:30'),
(13, 'Joint Pain', 'pain', 'Discomfort or pain in any joint', '2025-05-22 11:08:54'),
(14, 'Back Pain', 'pain', 'Pain anywhere in the back or spine', '2025-05-22 11:08:54'),
(15, 'Muscle Pain', 'pain', 'Generalized or localized muscle pain', '2025-05-22 11:08:54'),
(16, 'Abdominal Pain', 'pain', 'Pain in the stomach or abdomen area', '2025-05-22 11:08:54'),
(17, 'Bloating', 'digestive', 'Swelling or distention of the abdomen', '2025-05-22 11:08:54'),
(18, 'Diarrhea', 'digestive', 'Loose, watery stools occurring more frequently', '2025-05-22 11:08:54'),
(19, 'Constipation', 'digestive', 'Difficult or infrequent bowel movements', '2025-05-22 11:08:54'),
(20, 'Acid Reflux', 'digestive', 'Burning sensation in chest caused by stomach acid', '2025-05-22 11:08:54'),
(21, 'Loss of Appetite', 'digestive', 'Reduced desire to eat', '2025-05-22 11:08:54'),
(22, 'Rash', 'skin', 'Area of irritated or swollen skin', '2025-05-22 11:08:54'),
(23, 'Itching', 'skin', 'Irritating sensation causing desire to scratch', '2025-05-22 11:08:54'),
(24, 'Hives', 'skin', 'Raised, itchy welts on the skin', '2025-05-22 11:08:54'),
(25, 'Skin Discoloration', 'skin', 'Changes in skin color or pigmentation', '2025-05-22 11:08:54'),
(26, 'Anxiety', 'mental', 'Feeling of worry, nervousness, or unease', '2025-05-22 11:08:54'),
(27, 'Depression', 'mental', 'Persistent feeling of sadness and loss of interest', '2025-05-22 11:08:54'),
(28, 'Sleep Problems', 'mental', 'Difficulty falling or staying asleep', '2025-05-22 11:08:54'),
(29, 'Mood Changes', 'mental', 'Significant changes in emotional state', '2025-05-22 11:08:54'),
(30, 'Weight Loss', 'general', 'Unexplained decrease in body weight', '2025-05-22 11:08:54'),
(31, 'Weight Gain', 'general', 'Unexplained increase in body weight', '2025-05-22 11:08:54'),
(32, 'Night Sweats', 'general', 'Excessive sweating during night', '2025-05-22 11:08:54'),
(33, 'Weakness', 'general', 'General loss of strength or energy', '2025-05-22 11:08:54'),
(34, 'Joint Pain', 'pain', 'Discomfort or pain in any joint', '2025-05-22 11:10:20'),
(35, 'Back Pain', 'pain', 'Pain anywhere in the back or spine', '2025-05-22 11:10:20'),
(36, 'Muscle Pain', 'pain', 'Generalized or localized muscle pain', '2025-05-22 11:10:20'),
(37, 'Abdominal Pain', 'pain', 'Pain in the stomach or abdomen area', '2025-05-22 11:10:20'),
(38, 'Bloating', 'digestive', 'Swelling or distention of the abdomen', '2025-05-22 11:10:20'),
(39, 'Diarrhea', 'digestive', 'Loose, watery stools occurring more frequently', '2025-05-22 11:10:20'),
(40, 'Constipation', 'digestive', 'Difficult or infrequent bowel movements', '2025-05-22 11:10:20'),
(41, 'Acid Reflux', 'digestive', 'Burning sensation in chest caused by stomach acid', '2025-05-22 11:10:20'),
(42, 'Loss of Appetite', 'digestive', 'Reduced desire to eat', '2025-05-22 11:10:20'),
(43, 'Rash', 'skin', 'Area of irritated or swollen skin', '2025-05-22 11:10:20'),
(44, 'Itching', 'skin', 'Irritating sensation causing desire to scratch', '2025-05-22 11:10:20'),
(45, 'Hives', 'skin', 'Raised, itchy welts on the skin', '2025-05-22 11:10:20'),
(46, 'Skin Discoloration', 'skin', 'Changes in skin color or pigmentation', '2025-05-22 11:10:20'),
(47, 'Anxiety', 'mental', 'Feeling of worry, nervousness, or unease', '2025-05-22 11:10:20'),
(48, 'Depression', 'mental', 'Persistent feeling of sadness and loss of interest', '2025-05-22 11:10:20'),
(49, 'Sleep Problems', 'mental', 'Difficulty falling or staying asleep', '2025-05-22 11:10:20'),
(50, 'Mood Changes', 'mental', 'Significant changes in emotional state', '2025-05-22 11:10:20'),
(51, 'Weight Loss', 'general', 'Unexplained decrease in body weight', '2025-05-22 11:10:20'),
(52, 'Weight Gain', 'general', 'Unexplained increase in body weight', '2025-05-22 11:10:20'),
(53, 'Night Sweats', 'general', 'Excessive sweating during night', '2025-05-22 11:10:20'),
(54, 'Weakness', 'general', 'General loss of strength or energy', '2025-05-22 11:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `flduserid` int(11) NOT NULL,
  `flduserimage` varchar(255) DEFAULT NULL,
  `flduserfirstname` varchar(255) DEFAULT NULL,
  `flduserlastname` varchar(255) DEFAULT NULL,
  `flduserstreetaddress` varchar(255) DEFAULT NULL,
  `flduserlocalarea` varchar(255) DEFAULT NULL,
  `fldusercity` varchar(255) DEFAULT NULL,
  `flduserzone` varchar(255) DEFAULT NULL,
  `fldusercountry` varchar(255) DEFAULT NULL,
  `flduserpostalcode` varchar(10) DEFAULT NULL,
  `flduseremail` varchar(100) DEFAULT NULL,
  `flduserphonenumber` varchar(15) DEFAULT NULL,
  `flduseridnumber` varchar(13) DEFAULT NULL,
  `flduserpassword` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`condition_id`);

--
-- Indexes for table `condition_symptoms`
--
ALTER TABLE `condition_symptoms`
  ADD PRIMARY KEY (`condition_id`,`symptom_id`),
  ADD KEY `symptom_id` (`symptom_id`);

--
-- Indexes for table `diagnosis`
--
ALTER TABLE `diagnosis`
  ADD PRIMARY KEY (`flddiagnosisid`),
  ADD KEY `user_id` (`flduserid`),
  ADD KEY `condition_id` (`fldconditionid`);

--
-- Indexes for table `diagnosis_history`
--
ALTER TABLE `diagnosis_history`
  ADD PRIMARY KEY (`diagnosis_id`),
  ADD KEY `condition_id` (`condition_id`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`recommendation_id`),
  ADD KEY `condition_id` (`condition_id`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`symptom_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`flduserid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conditions`
--
ALTER TABLE `conditions`
  MODIFY `condition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `diagnosis`
--
ALTER TABLE `diagnosis`
  MODIFY `flddiagnosisid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diagnosis_history`
--
ALTER TABLE `diagnosis_history`
  MODIFY `diagnosis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `flduserid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `condition_symptoms`
--
ALTER TABLE `condition_symptoms`
  ADD CONSTRAINT `condition_symptoms_ibfk_1` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`condition_id`),
  ADD CONSTRAINT `condition_symptoms_ibfk_2` FOREIGN KEY (`symptom_id`) REFERENCES `symptoms` (`symptom_id`);

--
-- Constraints for table `diagnosis_history`
--
ALTER TABLE `diagnosis_history`
  ADD CONSTRAINT `diagnosis_history_ibfk_1` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`condition_id`);

--
-- Constraints for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`condition_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
