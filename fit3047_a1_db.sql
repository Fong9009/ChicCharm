-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 10:31 AM
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
-- Database: `cake_project_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `nonce_expiry` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'admin',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`, `profile_picture`) VALUES
(4, 'Nemobyte', 'team071', 'team071@gmail.com', '$2y$10$2Nx/9hEpu4xqwbp7euCiWeO2YhJ5bU8cFveL2yg2UX5FrXOWIq.zO', NULL, NULL, '2025-03-24 02:43:16', '2025-04-10 12:24:57', 'admin', '60176_Test.PNG'),
(7, 'Chay Fong', 'Hong', 'chayfonghong1@gmail.com', '$2y$10$yRLMe7NvgLTlUIeqz/MIEuz3uLLgLgurAeKybzNLbcAsBc.ulgnAq', NULL, NULL, '2025-04-12 10:28:11', '2025-04-13 13:45:11', 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_name` varchar(255) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `remaining_cost` decimal(10,2) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_name`, `booking_date`, `total_cost`, `remaining_cost`, `customer_id`, `status`, `notes`) VALUES
(168, 'Booking for Christian Cochrane', '2025-05-03', 370.00, 370.00, 2, 'finished', ''),
(169, 'Booking for Chay Fong Hong', '2025-05-03', 150.00, 150.00, 1, 'finished', 'Makeup For a Friend'),
(170, 'Booking for Christian Cochrane', '2025-05-07', 150.00, 150.00, 2, 'active', '');

-- --------------------------------------------------------

--
-- Table structure for table `bookings_services`
--

CREATE TABLE `bookings_services` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL COMMENT 'Cost of this specific service at time of booking',
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stylist_id` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL COMMENT 'Start time specific to this service within the booking',
  `end_time` time DEFAULT NULL COMMENT 'End time specific to this service within the booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings_services`
--

INSERT INTO `bookings_services` (`id`, `booking_id`, `service_id`, `service_cost`, `created`, `modified`, `stylist_id`, `start_time`, `end_time`) VALUES
(325, 168, 1, 150.00, '2025-05-02 08:56:02', '2025-05-02 08:56:02', 2, '10:00:00', '11:00:00'),
(326, 168, 2, 220.00, '2025-05-02 08:56:02', '2025-05-02 08:56:02', 2, '11:00:00', '12:30:00'),
(327, 169, 1, 150.00, '2025-05-02 08:56:28', '2025-05-02 08:56:28', 2, '12:30:00', '13:30:00'),
(328, 170, 1, 150.00, '2025-05-06 05:03:55', '2025-05-06 05:03:55', 2, '10:30:00', '11:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `bookings_stylists`
--

CREATE TABLE `bookings_stylists` (
  `id` int(11) NOT NULL,
  `stylist_date` date NOT NULL,
  `selected_cost` decimal(10,2) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `stylist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings_stylists`
--

INSERT INTO `bookings_stylists` (`id`, `stylist_date`, `selected_cost`, `booking_id`, `stylist_id`) VALUES
(291, '2025-05-03', 370.00, 168, 2),
(292, '2025-05-03', 150.00, 169, 2),
(293, '2025-05-07', 150.00, 170, 2);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `replied` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `message`, `replied`, `is_archived`, `created`, `modified`) VALUES
(1, 'Lila Kramer', 'Zephr Cobb', 'l_zephrcobb3506@hotmail.net', '0206511347', 'imperdiet non, vestibulum nec, euismod in, dolor. Fusce feugiat. Lorem', 0, 0, '2024-09-18 04:31:12', '2024-04-09 01:29:10'),
(2, 'Astra Rollins', 'Kaitlin Burris', 'a.kaitlinburris1979@protonmail.net', '0624756465', 'malesuada. Integer id magna et ipsum cursus vestibulum. Mauris', 0, 0, '2024-08-31 17:21:36', '2025-12-16 22:05:16'),
(3, 'Dominic Ramirez', 'Dillon Glenn', 'ddillonglenn@google.edu', '0285118654', 'ullamcorper. Duis at lacus. Quisque', 0, 0, '2025-01-15 06:55:09', '2024-11-02 23:12:24'),
(4, 'Hyatt Horton', 'Alden Figueroa', 'haldenfigueroa@google.ca', '0576200389', 'ut dolor dapibus gravida. Aliquam tincidunt, nunc ac', 0, 0, '2025-10-21 20:20:27', '2025-07-26 05:26:18'),
(6, 'Steel Henson', 'Charity Bowen', 'charitybowensteelhenson@protonmail.edu', '0331213211', 'erat volutpat. Nulla facilisis. Suspendisse commodo tincidunt nibh.', 0, 0, '2024-08-23 14:37:11', '2024-05-11 02:36:32'),
(7, 'Ivor Guerra', 'Thomas Dotson', 'ivorguerra_thomasdotson4228@icloud.org', '0994882883', 'Pellentesque ut ipsum ac mi eleifend egestas. Sed', 0, 0, '2025-12-27 16:32:13', '2025-08-23 21:40:17'),
(8, 'Hayley Weaver', 'Rhea Ballard', 'rhayleyweaver@outlook.com', '0737915842', 'dui. Fusce diam nunc, ullamcorper', 0, 0, '2026-02-14 09:44:17', '2025-10-21 10:19:10'),
(9, 'Dana Kinney', 'Isadora Burt', 'isadoraburt_danakinney@icloud.couk', '0471168632', 'cubilia Curae Phasellus ornare. Fusce mollis.', 0, 0, '2025-04-23 22:38:49', '2025-01-31 19:54:41'),
(10, 'Karly Goff', 'Mufutau Mcgee', 'karlygoffmufutaumcgee998@protonmail.com', '0723025822', 'Maecenas iaculis aliquet diam. Sed diam lorem,', 0, 0, '2024-07-28 15:34:35', '2026-01-08 19:39:17'),
(11, 'Bethany Whitaker', 'Walter Osborn', 'walterosborn-bethanywhitaker@protonmail.org', '0290569328', 'Fusce dolor quam, elementum at, egestas a, scelerisque sed,', 0, 0, '2025-02-06 22:33:50', '2024-05-09 13:25:03'),
(12, 'Adrian Bell', 'Emi Mccullough', 'adrianbell-emimccullough@hotmail.net', '0155707769', 'velit. Cras lorem lorem, luctus ut,', 0, 0, '2025-05-30 11:27:10', '2025-07-22 08:37:47'),
(13, 'Callum Whitehead', 'Lara Dyer', 'laradyercallumwhitehead3323@protonmail.com', '0516462952', 'magna, malesuada vel, convallis in, cursus et, eros.', 0, 0, '2025-05-09 02:07:38', '2025-10-29 19:30:28'),
(14, 'Knox Hicks', 'Upton Holland', 'uptonhollandknoxhicks@google.org', '0494256466', 'Suspendisse non leo. Vivamus nibh dolor, nonummy ac,', 0, 0, '2026-01-19 07:06:34', '2024-04-13 06:07:57'),
(15, 'Elvis Compton', 'Ryan Valencia', 'ryanvalencia.elviscompton7502@protonmail.net', '0126082341', 'et tristique pellentesque, tellus sem mollis dui, in', 0, 0, '2024-07-24 02:12:20', '2024-07-24 20:38:45'),
(16, 'Tashya Macias', 'Galvin Goodwin', 'galvingoodwin_tashyamacias1519@icloud.couk', '0442122956', 'lectus, a sollicitudin orci sem eget massa. Suspendisse', 0, 0, '2024-09-13 22:44:02', '2024-11-27 20:17:37'),
(17, 'Risa Robles', 'Keelie Rodgers', 'keelierodgers.risarobles5822@outlook.ca', '0748919234', 'torquent per conubia nostra, per inceptos hymenaeos.', 0, 0, '2024-08-05 18:24:43', '2025-02-19 18:36:47'),
(18, 'Isaac Fischer', 'Paloma Bates', 'ipalomabates801@icloud.edu', '0741512955', 'Integer urna. Vivamus molestie dapibus ligula. Aliquam erat', 0, 0, '2025-12-23 21:04:29', '2025-03-03 11:30:19'),
(19, 'Edan Parks', 'Kai Cabrera', 'kaicabrera_edanparks@yahoo.ca', '0909243812', 'vestibulum lorem, sit amet ultricies', 0, 0, '2025-03-21 00:03:26', '2025-04-26 22:33:34'),
(20, 'Macy Alvarado', 'Selma Nunez', 'macyalvarado.selmanunez@aol.edu', '0448944223', 'metus facilisis lorem tristique aliquet. Phasellus', 0, 0, '2025-01-13 05:45:03', '2025-05-29 20:44:39'),
(21, 'Adam Michael', 'Merrill Allen', 'a.merrillallen@icloud.net', '0123174423', 'quis, pede. Suspendisse dui. Fusce diam', 0, 0, '2024-12-26 13:53:42', '2025-01-02 22:06:57'),
(22, 'Linus Anderson', 'Cole Vinson', 'colevinson_linusanderson9643@aol.org', '0546120845', 'consequat purus. Maecenas libero est,', 0, 0, '2025-12-10 23:18:29', '2024-07-13 00:23:38'),
(23, 'Denise Reyes', 'Jordan Poole', 'jdenisereyes@hotmail.net', '0765856315', 'odio. Nam interdum enim non nisi. Aenean', 0, 0, '2025-07-14 01:06:40', '2024-12-10 01:47:16'),
(24, 'Felix Yang', 'Fulton Sellers', 'fultonsellersfelixyang@google.edu', '0976824570', 'scelerisque neque sed sem egestas blandit. Nam nulla magna,', 0, 0, '2025-03-07 08:30:35', '2024-10-15 11:45:40'),
(25, 'Kylee Hughes', 'Thane Finley', 'k-thanefinley@icloud.org', '0594348767', 'ipsum. Suspendisse sagittis. Nullam vitae diam. Proin dolor. Nulla semper', 0, 0, '2024-11-16 08:53:31', '2026-01-31 06:23:53'),
(26, 'Hayden Gordon', 'Drake Butler', 'd-haydengordon@yahoo.net', '0300167464', 'pede, ultrices a, auctor non, feugiat nec,', 1, 1, '2026-01-21 03:30:55', '2025-08-01 01:04:24'),
(27, 'Norman Klein', 'Angela Brock', 'n_angelabrock5677@aol.net', '0813876826', 'at lacus. Quisque purus sapien, gravida non,', 1, 1, '2025-05-13 23:57:09', '2024-08-08 06:53:22'),
(28, 'Reagan Wise', 'Idola Mcintosh', 'idolamcintoshreaganwise@outlook.couk', '0341302527', 'non, lobortis quis, pede. Suspendisse dui.', 1, 1, '2026-01-07 22:13:24', '2025-12-24 07:38:37'),
(29, 'Ima Melendez', 'Dorian Torres', 'imamelendez.doriantorres@yahoo.org', '0512264343', 'mattis velit justo nec ante. Maecenas mi felis,', 1, 1, '2024-12-26 21:55:37', '2024-11-15 18:21:00'),
(30, 'Len York', 'Shay Benson', 'shaybenson-lenyork2859@aol.org', '0861888798', 'enim consequat purus. Maecenas libero est, congue a, aliquet', 1, 1, '2025-10-12 07:09:29', '2025-09-14 15:12:15'),
(31, 'Allen Marsh', 'Giselle England', 'giselleenglandallenmarsh@yahoo.net', '0672226778', 'ante. Vivamus non lorem vitae odio sagittis', 1, 1, '2025-02-07 22:16:49', '2024-11-25 05:12:34'),
(32, 'Chancellor Roman', 'Jelani Pruitt', 'c-jelanipruitt9126@google.com', '0861583897', 'mollis nec, cursus a, enim. Suspendisse', 1, 1, '2024-07-21 13:25:39', '2025-06-10 19:01:27'),
(33, 'Zephania Roberson', 'Prescott Landry', 'prescottlandry.zephaniaroberson@protonmail.com', '0412558843', 'mauris sagittis placerat. Cras dictum ultricies ligula.', 1, 1, '2024-09-05 18:34:13', '2024-04-15 04:06:00'),
(34, 'Graham Neal', 'Iliana Rowe', 'ilianarowe.grahamneal9123@protonmail.couk', '0716181442', 'aliquet libero. Integer in magna. Phasellus dolor elit, pellentesque a,', 1, 1, '2026-03-09 18:21:32', '2025-09-14 14:03:20'),
(35, 'Dorian Gallegos', 'Zelenia Hale', 'zeleniahale_doriangallegos@hotmail.edu', '0691274694', 'ligula. Aenean gravida nunc sed pede. Cum sociis natoque', 1, 1, '2024-04-19 15:59:38', '2024-06-16 21:22:06'),
(36, 'Candace Craig', 'Claudia Luna', 'claudialuna-candacecraig@google.ca', '0469923267', 'ac, eleifend vitae, erat. Vivamus nisi.', 1, 1, '2024-10-17 06:00:22', '2024-06-25 06:47:58'),
(37, 'Preston Conway', 'Sandra Tyson', 'sprestonconway@protonmail.ca', '0348362552', 'metus urna convallis erat, eget tincidunt dui', 1, 1, '2025-12-30 08:38:16', '2025-12-10 05:42:23'),
(38, 'Aretha Velazquez', 'Piper Barton', 'arethavelazquez.piperbarton822@yahoo.org', '0418755802', 'diam lorem, auctor quis, tristique ac, eleifend vitae, erat. Vivamus', 1, 1, '2024-07-22 08:56:27', '2026-03-06 10:19:57'),
(39, 'Blake Hoover', 'Vance Nielsen', 'v-blakehoover199@outlook.ca', '0474582037', 'nulla. Integer urna. Vivamus molestie dapibus ligula. Aliquam erat', 1, 1, '2025-10-11 21:41:36', '2024-09-22 11:15:01'),
(40, 'Mohammad Morrison', 'Lars Lowe', 'm.larslowe@protonmail.edu', '0186722323', 'montes, nascetur ridiculus mus. Proin vel arcu', 1, 1, '2024-10-18 23:20:13', '2026-02-20 10:15:41'),
(41, 'Olga Bennett', 'Mohammad Erickson', 'mohammaderickson_olgabennett@hotmail.couk', '0550183186', 'Donec luctus aliquet odio. Etiam ligula tortor, dictum eu, placerat', 1, 1, '2025-08-22 00:06:17', '2024-09-05 14:15:55'),
(42, 'Cara Weiss', 'Madeline Adkins', 'c_madelineadkins6379@outlook.net', '0697770682', 'Ut semper pretium neque. Morbi quis', 1, 1, '2025-02-15 18:59:34', '2026-03-04 03:06:38'),
(43, 'Blake George', 'Wilma Webster', 'wilmawebster_blakegeorge@icloud.org', '0708784492', 'ultrices iaculis odio. Nam interdum enim', 1, 1, '2024-05-10 00:39:22', '2026-01-07 00:32:15'),
(44, 'Neil Sanford', 'Coby Robertson', 'c_neilsanford2816@icloud.couk', '0177575663', 'et malesuada fames ac turpis egestas.', 1, 0, '2025-04-28 16:26:49', '2025-04-17 03:46:26'),
(45, 'Quentin Robinson', 'Mohammad Rollins', 'mohammadrollinsquentinrobinson@protonmail.org', '0736131449', 'nec, eleifend non, dapibus rutrum, justo. Praesent luctus. Curabitur egestas', 1, 1, '2025-03-04 21:42:40', '2024-05-15 13:34:14'),
(46, 'Ruth Robinson', 'Shafira Dudley', 'r.shafiradudley@yahoo.net', '0626373659', 'arcu. Sed eu nibh vulputate mauris sagittis', 1, 1, '2025-01-05 20:35:20', '2026-02-20 03:39:05'),
(47, 'Brielle Santana', 'Fuller Parsons', 'briellesantana-fullerparsons@hotmail.couk', '0377233324', 'tortor, dictum eu, placerat eget, venenatis a, magna. Lorem ipsum', 1, 1, '2024-04-08 15:53:00', '2024-10-05 15:35:52'),
(48, 'Dane Glenn', 'Janna Wise', 'd-jannawise@aol.edu', '0632657009', 'imperdiet ullamcorper. Duis at lacus. Quisque purus sapien, gravida', 1, 1, '2024-06-23 19:10:29', '2024-05-13 23:34:01'),
(49, 'Ulysses Wooten', 'Jeremy Oneil', 'jeremyoneilulysseswooten1118@aol.couk', '0691753877', 'Cum sociis natoque penatibus et magnis dis parturient montes,', 1, 1, '2025-03-27 12:20:02', '2025-06-15 12:29:50'),
(50, 'Veronica Guy', 'Bruno Brooks', 'brunobrooks_veronicaguy@yahoo.com', '0552357611', 'in lobortis tellus justo sit amet nulla. Donec non justo.', 1, 1, '2024-09-01 16:56:07', '2025-04-16 14:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL,
  `parent` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` text DEFAULT NULL,
  `previous_value` text DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_blocks`
--

INSERT INTO `content_blocks` (`id`, `parent`, `slug`, `label`, `description`, `type`, `value`, `previous_value`, `modified`) VALUES
(3, 'Landing', 'web-title', 'Web Title', 'The main name of Chiccharm', 'text', 'Australia\'s Go-To For Fashion & Entertainment Services', NULL, '2025-04-11 00:26:03'),
(4, 'Landing', 'title-catch', 'Title Description', 'Description under the main page attraction', 'text', '           Out with the old and in with the new, ChicCharm\'s diverse range of services are sure to\r\n                            assist your needs in makeup artistry, wig styling, fashion design and hairstyling', NULL, '2025-04-11 00:27:40'),
(5, 'Landing', 'discover-button', 'Discover Button', 'This is for the first call to action button', 'text', 'Discover the World of ChicCharm', NULL, '2025-04-11 00:31:10'),
(6, 'Landing', 'about-chiccharm-title', 'About Chiccharm Title', 'ChicCharm about title', 'text', 'ChicCharm', NULL, '2025-04-11 00:38:05'),
(7, 'Landing', 'about-catch', 'About Catch', 'Catch for the about section', 'text', 'A New Era', NULL, '2025-04-11 00:35:37'),
(8, 'Landing', 'about-desc', 'Description in about', 'Description in about', 'text', '                            ChicCharm is commited to providing a fresh experience to returning\r\n                            customers all while attracting new clients far and wide.\r\n                            We are committed to providing the highest quality and expertise for your fashion shoots and\r\n                            theatre entertainments\r\n                            Even so we still provide the same loved services that our customers adore With a new\r\n                            Business direction our services are only going to get wider!', NULL, '2025-04-11 00:47:11'),
(9, 'Landing', 'owner-title-text', 'Owner Title', 'Title of the owner', 'text', 'Meet Michonne', NULL, '2025-04-11 00:48:06'),
(10, 'Landing', 'desc-owner', 'Description of Owner', 'Description of Owner', 'text', 'The Owner and Visionary of ChicCharm\'s Future Creativity', NULL, '2025-04-11 00:50:52'),
(11, 'Landing', 'owner-quote', 'Quote from Owner', 'Quote from Owner', 'text', '\"Creativity is what drives ChicCharm and that is what I am about\"', NULL, '2025-04-11 00:52:41'),
(12, 'Landing', 'vision-statement', 'Vision Statement', 'Statement of ChicCharms Vision', 'text', 'Michonne had envisioned a future business that would take the fashion and entertainment industry by storm. <br />Seeing that the fashion and entertainment industry had yet to take its shape in Australia, Michonne had saw an opportunity. <br />An opportunity to provide fashion and makeup services to cater for modelling and the entertainment industry <br />Michonne believes that with enough dedication and a new makeover of ChicCharm she can bring it onto the not just the local stage<br />But the whole of Australia.', NULL, '2025-04-11 00:52:20'),
(13, 'Landing', 'service-title', 'Service Title', 'The Title for landing page service', 'text', 'ChicCharm At Your Service', NULL, '2025-04-11 00:55:39'),
(14, 'Landing', 'service-desc', 'Service Description', 'Description of services provided at ChicCharm', 'text', 'Here at ChicCharm we have many services that will suit your needs.', NULL, '2025-05-06 07:48:08'),
(15, 'Landing', 'service-one-title', 'Service one title', 'First service title', 'text', 'Makeup Artistry', NULL, '2025-04-11 01:06:58'),
(16, 'Landing', 'service-one-desc', 'Service one description', 'Description Text', 'text', 'ChicCharm can make sure your stars of the show shine as bright as they should be. Our experienced Makeup artists are sure to bring the life to your show. ', 'ChicCharm can make sure your stars of the show shine.', '2025-05-06 17:28:30'),
(17, 'Landing', 'service-two-title', 'Service two title', 'Service Title', 'text', 'Wig Styling', NULL, '2025-04-11 01:02:10'),
(18, 'Landing', 'service-two-desc', 'Service two description', 'Description Text', 'text', 'ChicCharm can design custom wigs for all your needs.', NULL, '2025-04-11 01:03:07'),
(19, 'Landing', 'service-three-title', 'Service three title', 'Service title', 'text', 'Fashion Design', NULL, '2025-04-11 01:03:49'),
(20, 'Landing', 'service-three-desc', 'Service three Description', 'Description text', 'text', 'ChicCharm can design custom clothes for all your theatre needs.', NULL, '2025-04-11 01:04:26'),
(21, 'Landing', 'service-four-title', 'Service Four Title', 'Service Title', 'text', 'Hair Styling', NULL, '2025-04-11 01:07:38'),
(22, 'Landing', 'service-four-desc', 'Service Four Description', 'Description Text', 'text', 'ChicCharm can make sure your stars hairs are stunning and fabulous.', NULL, '2025-04-11 01:05:41'),
(23, 'Landing', 'booking-button', 'Booking Button', 'Button for booking', 'text', 'Make a Booking with ChicCharm', NULL, '2025-04-11 01:09:07'),
(24, 'Landing', 'past-work-title', 'Past work title', 'Title', 'text', 'Some of ChicCharms latest works', NULL, '2025-04-11 01:10:08'),
(25, 'Landing', 'past-text', 'Past text', 'Text description', 'text', 'ChicCharm is dedicated to helping make sure that your show is ready on the stage or a model show,\r\n                    Our Business is nothing without our fabulous customers who continue to work with us', NULL, '2025-04-11 01:10:49'),
(26, 'Photos', 'photo-about', 'Photo about', 'Photo about', 'image', '/content-blocks/uploads/photo-test.0c5a460258f8dd77008ecac8d5de80eb.jpg', NULL, '2025-05-06 14:52:01'),
(28, 'Photos', 'photo-carousel-1', 'Carousel Photo 1', 'This is for Carousel Photo 1', 'image', '/content-blocks/uploads/photo-carousel-1.2114f8243a35576002022f9220065902.jpg', '/content-blocks/uploads/photo-carousel-1.f6e0bf8fceba4b989d1cc9db68312cf8.jpg', '2025-05-06 17:27:33'),
(29, 'Photos', 'photo-carousel-2', 'Carousel photo 2', 'This is for Carousel Photo 2', 'image', '/content-blocks/uploads/photo-carousel-2.c0df9c4cbd9a2bfbaae2d61b36e49b4b.jpg', NULL, '2025-05-06 17:33:25'),
(30, 'Photos', 'photo-carousel-3', 'Carousel Photo 3', 'This is for Carousel Photo 3', 'image', '/content-blocks/uploads/photo-carousel-3.d2c03447d8f885a788dd71a11d6ff5bf.jpg', NULL, '2025-05-06 17:35:11'),
(31, 'Photos', 'photo-carousel-4', 'Carousel Photo 4', 'This is for Carousel Photo 4', 'image', '/content-blocks/uploads/photo-carousel-4.044531ea1224ce7c9f8b8fb0d64c6c66.jpg', NULL, '2025-05-06 17:42:30'),
(32, 'Photos', 'photo-masthead', 'Mast Photo', 'The front page photo', 'image', '/content-blocks/uploads/photo-masthead.5df4a72c41c007d45e39de70d5396d4d.jpg', NULL, '2025-05-06 18:03:34'),
(33, 'Portfolio Images', 'portfolio-1', 'Portfolio Image 1', 'Portfolio Image 1', 'image', '/content-blocks/uploads/portfolio-1.7114e8566fd96e6babbe1d4ea6f39cb6.jpg', NULL, '2025-05-06 18:10:03'),
(34, 'Portfolio Images', 'portfolio-2', 'Portfolio Image 2', 'Portfolio Image 2', 'image', '/content-blocks/uploads/portfolio-2.390ab119c46bf74ca6ea7a1ac2c0bdf4.jpg', NULL, '2025-05-06 18:10:14'),
(35, 'Portfolio Images', 'portfolio-3', 'Portfolio Image 3', 'Portfolio Image 3', 'image', '/content-blocks/uploads/portfolio-3.723ce5fabd08d9165e8c3981ba077736.jpg', NULL, '2025-05-06 18:13:36'),
(36, 'Photos', 'photo-meet', 'Meet Photo', 'Meet photo for Michonne', 'image', '/content-blocks/uploads/photo-meet.9218833aca3b2d5fd7b7d5ff1c1396ba.jpg', NULL, '2025-05-06 18:17:17'),
(37, 'Services MotTo', 'services-motto-1', 'Services Motto 1', 'Services Motto 1', 'text', 'The Finest of Makeup Stylists', NULL, '2025-05-06 08:28:46'),
(38, 'Services Motto', 'services-motto-2', 'Services Motto 2', 'Services Motto 2', 'text', 'The Finest of Wig Stylists', NULL, '2025-05-06 08:28:02'),
(39, 'Services Motto', 'services-motto-3', 'Services Motto 3', 'Services Motto 3', 'text', 'The Finest of Dress Makers', NULL, '2025-05-06 08:31:33'),
(40, 'Services Motto', 'services-motto-4', 'Services Motto 4', 'Services Motto 4', 'text', 'The Finest of Hair Stylists', NULL, '2025-05-06 08:29:50');

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks_phinxlog`
--

CREATE TABLE `content_blocks_phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_blocks_phinxlog`
--

INSERT INTO `content_blocks_phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20230402063959, 'ContentBlocksMigration', '2025-04-10 23:49:57', '2025-04-10 23:49:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `nonce_expiry` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'customer',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`, `profile_picture`) VALUES
(1, 'Chay Fong', 'Hong', 'chayfong9009@gmail.com', '$2y$10$hIXeX/RjXv4ox29mSuSZiuxclpjRXzyhjO9bgG5is13uBubV8gbqq', 'cc15950b7cef51a668b0f471703d014cb0951e29a7774c49847a669a719ad4bf972d19fdc3cf2ba6ab3adfa14ec3b63a49047e642ebca46469bd429cd37b8471', '2025-04-20 13:28:57', '2025-03-26 11:10:12', '2025-04-18 04:50:05', 'customer', '11662_sung-jin-woo.png'),
(2, 'Christian', 'Cochrane', 'cakephp@example.com', '$2y$10$4oCG2ResnEQbYk2rgtdTGe1faLZPOu29GZma4EfRmQ.B6vyHOk7u6', '', NULL, '2025-04-04 02:31:18', '2025-04-16 13:25:10', 'customer', NULL),
(61, 'Guest', 'Account', 'guest@chiccharm.com', '$2y$10$331arAgxUzMQjrnxAAh91uxGonTi.NiZm/1C8upN6cYRIu/66zqfG', NULL, NULL, '2025-05-02 18:55:30', '2025-05-02 19:00:00', 'guest', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phinxlog`
--

INSERT INTO `phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20250418044655, 'RemoveNotesFromBookingsServices', '2025-04-18 04:47:08', '2025-04-18 04:47:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_cost` decimal(10,2) NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_cost`, `duration_minutes`) VALUES
(1, 'Makeup', 150.00, 60),
(2, 'Hair Dressing', 220.00, 90),
(3, 'Dress Making', 500.00, 120);

-- --------------------------------------------------------

--
-- Table structure for table `stylists`
--

CREATE TABLE `stylists` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nonce` datetime DEFAULT NULL,
  `nonce_expiry` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'stylist',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stylists`
--

INSERT INTO `stylists` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`, `profile_picture`) VALUES
(2, 'Lucy', 'Reig', 'lucyr@chiccharm.com.au', '$2y$10$UaR/Z4ljlvG1LztxsODz8uWljiN0Hv8MpN8mKZjZ5NEBOfchCFwei', NULL, NULL, '2025-04-05 11:51:05', '2025-04-18 06:09:00', 'stylist', NULL),
(4, 'Michael ', 'Jackson', 'hehe@gmail.com', '$2y$10$QAXdlH9eRlnPjL83P300BuDDNzHyDjchXuSxfoJTVNg3Suv8Me/UW', NULL, NULL, '2025-04-05 04:23:35', '2025-04-18 06:09:03', 'stylist', NULL),
(5, 'Michelle', 'Yang', 'Michelleyang01@gmail.com', '$2y$10$fAokVzWnNpCP.yZ7VkmsxeKgXY7tJ0l0UNFZZQ4RJfioBRwS0eIyW', NULL, NULL, '2025-04-05 14:48:30', '2025-04-18 06:09:07', 'stylist', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stylists_services`
--

CREATE TABLE `stylists_services` (
  `id` int(11) NOT NULL,
  `stylist_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stylists_services`
--

INSERT INTO `stylists_services` (`id`, `stylist_id`, `service_id`) VALUES
(3, 2, 2),
(7, 4, 1),
(8, 4, 2),
(15, 5, 1),
(16, 2, 3),
(17, 5, 3),
(18, 2, 1),
(19, 4, 3),
(20, 5, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customerForeignKey` (`customer_id`);

--
-- Indexes for table `bookings_services`
--
ALTER TABLE `bookings_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookingForeignKey` (`booking_id`),
  ADD KEY `stylistBookingForeignKey` (`stylist_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content_blocks_phinxlog`
--
ALTER TABLE `content_blocks_phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stylists`
--
ALTER TABLE `stylists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `stylists_services`
--
ALTER TABLE `stylists_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stylistForeignKey` (`stylist_id`),
  ADD KEY `serviceForeignKey` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `bookings_services`
--
ALTER TABLE `bookings_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

--
-- AUTO_INCREMENT for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stylists`
--
ALTER TABLE `stylists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stylists_services`
--
ALTER TABLE `stylists_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `customerForeignKey` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bookings_services`
--
ALTER TABLE `bookings_services`
  ADD CONSTRAINT `bookings_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
  ADD CONSTRAINT `bookingForeignKey` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stylistBookingForeignKey` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stylists_services`
--
ALTER TABLE `stylists_services`
  ADD CONSTRAINT `serviceForeignKey` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `stylistForeignKey` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
