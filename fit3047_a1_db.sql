-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 02:15 PM
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
    (4, 'Nemobyte', 'team071', 'team071@gmail.com', '$2y$10$2Nx/9hEpu4xqwbp7euCiWeO2YhJ5bU8cFveL2yg2UX5FrXOWIq.zO', NULL, NULL, '2025-03-24 02:43:16', '2025-03-24 02:43:16', 'admin', NULL);

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
                            `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_name`, `booking_date`, `total_cost`, `remaining_cost`, `customer_id`) VALUES
                                                                                                                 (7, 'Test Booking', '2025-04-03', 10.00, 10.00, NULL),
                                                                                                                 (8, 'Double Test', '2025-04-06', 10.00, 10.00, NULL),
                                                                                                                 (9, 'Fong', '2025-04-17', 0.00, 0.00, NULL),
                                                                                                                 (10, 'Booking for Chay Fong Hong', '2025-04-09', 6.50, 6.50, NULL),
                                                                                                                 (12, 'Fong', '2025-04-03', 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings_stylists`
--

CREATE TABLE `bookings_stylists` (
                                     `id` int(11) NOT NULL,
                                     `stylist_date` date NOT NULL,
                                     `start_time` time NOT NULL,
                                     `end_time` time NOT NULL,
                                     `selected_cost` decimal(10,2) NOT NULL,
                                     `booking_id` int(11) NOT NULL,
                                     `stylist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings_stylists`
--

INSERT INTO `bookings_stylists` (`id`, `stylist_date`, `start_time`, `end_time`, `selected_cost`, `booking_id`, `stylist_id`) VALUES
                                                                                                                                  (5, '2025-04-03', '21:00:00', '22:00:00', 10.00, 7, 2),
                                                                                                                                  (6, '2025-04-06', '21:00:00', '22:00:00', 10.00, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
                            `id` int(11) NOT NULL,
                            `first_name` varchar(255) NOT NULL,
                            `last_name` varchar(255) NOT NULL,
                            `email` varchar(255) NOT NULL,
                            `phone_number` varchar(20) NOT NULL,
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
                                                                                                                                                  (1, 'Lila Kramer', 'Zephr Cobb', 'l_zephrcobb3506@hotmail.net', '(02) 0651 1347', 'imperdiet non, vestibulum nec, euismod in, dolor. Fusce feugiat. Lorem', 0, 1, '2024-09-18 04:31:12', '2024-04-09 01:29:10'),
                                                                                                                                                  (2, 'Astra Rollins', 'Kaitlin Burris', 'a.kaitlinburris1979@protonmail.net', '(06) 2475 6465', 'malesuada. Integer id magna et ipsum cursus vestibulum. Mauris', 1, 1, '2024-08-31 17:21:36', '2025-12-16 22:05:16'),
                                                                                                                                                  (3, 'Dominic Ramirez', 'Dillon Glenn', 'ddillonglenn@google.edu', '(02) 8511 8654', 'ullamcorper. Duis at lacus. Quisque', 0, 0, '2025-01-15 06:55:09', '2024-11-02 23:12:24'),
                                                                                                                                                  (4, 'Hyatt Horton', 'Alden Figueroa', 'haldenfigueroa@google.ca', '(05) 7620 0389', 'ut dolor dapibus gravida. Aliquam tincidunt, nunc ac', 1, 0, '2025-10-21 20:20:27', '2025-07-26 05:26:18'),
                                                                                                                                                  (5, 'Carolyn Heath', 'Rachel Bates', 'r_carolynheath@hotmail.org', '(08) 5077 8073', 'adipiscing lobortis risus. In mi pede, nonummy ut,', 0, 1, '2026-03-04 10:05:47', '2025-11-26 18:37:41'),
                                                                                                                                                  (6, 'Steel Henson', 'Charity Bowen', 'charitybowensteelhenson@protonmail.edu', '(03) 3121 3211', 'erat volutpat. Nulla facilisis. Suspendisse commodo tincidunt nibh.', 1, 0, '2024-08-23 14:37:11', '2024-05-11 02:36:32'),
                                                                                                                                                  (7, 'Ivor Guerra', 'Thomas Dotson', 'ivorguerra_thomasdotson4228@icloud.org', '(09) 9488 2883', 'Pellentesque ut ipsum ac mi eleifend egestas. Sed', 1, 0, '2025-12-27 16:32:13', '2025-08-23 21:40:17'),
                                                                                                                                                  (8, 'Hayley Weaver', 'Rhea Ballard', 'rhayleyweaver@outlook.com', '(07) 3791 5842', 'dui. Fusce diam nunc, ullamcorper', 0, 1, '2026-02-14 09:44:17', '2025-10-21 10:19:10'),
                                                                                                                                                  (9, 'Dana Kinney', 'Isadora Burt', 'isadoraburt_danakinney@icloud.couk', '(04) 7116 8632', 'cubilia Curae Phasellus ornare. Fusce mollis.', 1, 0, '2025-04-23 22:38:49', '2025-01-31 19:54:41'),
                                                                                                                                                  (10, 'Karly Goff', 'Mufutau Mcgee', 'karlygoffmufutaumcgee998@protonmail.com', '(07) 2302 5822', 'Maecenas iaculis aliquet diam. Sed diam lorem,', 0, 1, '2024-07-28 15:34:35', '2026-01-08 19:39:17'),
                                                                                                                                                  (11, 'Bethany Whitaker', 'Walter Osborn', 'walterosborn-bethanywhitaker@protonmail.org', '(02) 9056 9328', 'Fusce dolor quam, elementum at, egestas a, scelerisque sed,', 1, 1, '2025-02-06 22:33:50', '2024-05-09 13:25:03'),
                                                                                                                                                  (12, 'Adrian Bell', 'Emi Mccullough', 'adrianbell-emimccullough@hotmail.net', '(01) 5570 7769', 'velit. Cras lorem lorem, luctus ut,', 1, 1, '2025-05-30 11:27:10', '2025-07-22 08:37:47'),
                                                                                                                                                  (13, 'Callum Whitehead', 'Lara Dyer', 'laradyercallumwhitehead3323@protonmail.com', '(05) 1646 2952', 'magna, malesuada vel, convallis in, cursus et, eros.', 0, 1, '2025-05-09 02:07:38', '2025-10-29 19:30:28'),
                                                                                                                                                  (14, 'Knox Hicks', 'Upton Holland', 'uptonhollandknoxhicks@google.org', '(04) 9425 6466', 'Suspendisse non leo. Vivamus nibh dolor, nonummy ac,', 0, 1, '2026-01-19 07:06:34', '2024-04-13 06:07:57'),
                                                                                                                                                  (15, 'Elvis Compton', 'Ryan Valencia', 'ryanvalencia.elviscompton7502@protonmail.net', '(01) 2608 2341', 'et tristique pellentesque, tellus sem mollis dui, in', 1, 1, '2024-07-24 02:12:20', '2024-07-24 20:38:45'),
                                                                                                                                                  (16, 'Tashya Macias', 'Galvin Goodwin', 'galvingoodwin_tashyamacias1519@icloud.couk', '(04) 4212 2956', 'lectus, a sollicitudin orci sem eget massa. Suspendisse', 0, 0, '2024-09-13 22:44:02', '2024-11-27 20:17:37'),
                                                                                                                                                  (17, 'Risa Robles', 'Keelie Rodgers', 'keelierodgers.risarobles5822@outlook.ca', '(07) 4891 9234', 'torquent per conubia nostra, per inceptos hymenaeos.', 1, 0, '2024-08-05 18:24:43', '2025-02-19 18:36:47'),
                                                                                                                                                  (18, 'Isaac Fischer', 'Paloma Bates', 'ipalomabates801@icloud.edu', '(07) 4151 2955', 'Integer urna. Vivamus molestie dapibus ligula. Aliquam erat', 1, 1, '2025-12-23 21:04:29', '2025-03-03 11:30:19'),
                                                                                                                                                  (19, 'Edan Parks', 'Kai Cabrera', 'kaicabrera_edanparks@yahoo.ca', '(09) 0924 3812', 'vestibulum lorem, sit amet ultricies', 1, 0, '2025-03-21 00:03:26', '2025-04-26 22:33:34'),
                                                                                                                                                  (20, 'Macy Alvarado', 'Selma Nunez', 'macyalvarado.selmanunez@aol.edu', '(04) 4894 4223', 'metus facilisis lorem tristique aliquet. Phasellus', 0, 1, '2025-01-13 05:45:03', '2025-05-29 20:44:39'),
                                                                                                                                                  (21, 'Adam Michael', 'Merrill Allen', 'a.merrillallen@icloud.net', '(01) 2317 4423', 'quis, pede. Suspendisse dui. Fusce diam', 1, 0, '2024-12-26 13:53:42', '2025-01-02 22:06:57'),
                                                                                                                                                  (22, 'Linus Anderson', 'Cole Vinson', 'colevinson_linusanderson9643@aol.org', '(05) 4612 0845', 'consequat purus. Maecenas libero est,', 0, 0, '2025-12-10 23:18:29', '2024-07-13 00:23:38'),
                                                                                                                                                  (23, 'Denise Reyes', 'Jordan Poole', 'jdenisereyes@hotmail.net', '(07) 6585 6315', 'odio. Nam interdum enim non nisi. Aenean', 0, 0, '2025-07-14 01:06:40', '2024-12-10 01:47:16'),
                                                                                                                                                  (24, 'Felix Yang', 'Fulton Sellers', 'fultonsellersfelixyang@google.edu', '(09) 7682 4570', 'scelerisque neque sed sem egestas blandit. Nam nulla magna,', 0, 0, '2025-03-07 08:30:35', '2024-10-15 11:45:40'),
                                                                                                                                                  (25, 'Kylee Hughes', 'Thane Finley', 'k-thanefinley@icloud.org', '(05) 9434 8767', 'ipsum. Suspendisse sagittis. Nullam vitae diam. Proin dolor. Nulla semper', 0, 1, '2024-11-16 08:53:31', '2026-01-31 06:23:53'),
                                                                                                                                                  (26, 'Hayden Gordon', 'Drake Butler', 'd-haydengordon@yahoo.net', '(03) 0016 7464', 'pede, ultrices a, auctor non, feugiat nec,', 0, 1, '2026-01-21 03:30:55', '2025-08-01 01:04:24'),
                                                                                                                                                  (27, 'Norman Klein', 'Angela Brock', 'n_angelabrock5677@aol.net', '(08) 1387 6826', 'at lacus. Quisque purus sapien, gravida non,', 1, 1, '2025-05-13 23:57:09', '2024-08-08 06:53:22'),
                                                                                                                                                  (28, 'Reagan Wise', 'Idola Mcintosh', 'idolamcintoshreaganwise@outlook.couk', '(03) 4130 2527', 'non, lobortis quis, pede. Suspendisse dui.', 0, 1, '2026-01-07 22:13:24', '2025-12-24 07:38:37'),
                                                                                                                                                  (29, 'Ima Melendez', 'Dorian Torres', 'imamelendez.doriantorres@yahoo.org', '(05) 1226 4343', 'mattis velit justo nec ante. Maecenas mi felis,', 1, 1, '2024-12-26 21:55:37', '2024-11-15 18:21:00'),
                                                                                                                                                  (30, 'Len York', 'Shay Benson', 'shaybenson-lenyork2859@aol.org', '(08) 6188 8798', 'enim consequat purus. Maecenas libero est, congue a, aliquet', 1, 1, '2025-10-12 07:09:29', '2025-09-14 15:12:15'),
                                                                                                                                                  (31, 'Allen Marsh', 'Giselle England', 'giselleenglandallenmarsh@yahoo.net', '(06) 7222 6778', 'ante. Vivamus non lorem vitae odio sagittis', 1, 1, '2025-02-07 22:16:49', '2024-11-25 05:12:34'),
                                                                                                                                                  (32, 'Chancellor Roman', 'Jelani Pruitt', 'c-jelanipruitt9126@google.com', '(08) 6158 3897', 'mollis nec, cursus a, enim. Suspendisse', 0, 1, '2024-07-21 13:25:39', '2025-06-10 19:01:27'),
                                                                                                                                                  (33, 'Zephania Roberson', 'Prescott Landry', 'prescottlandry.zephaniaroberson@protonmail.com', '(04) 1255 8843', 'mauris sagittis placerat. Cras dictum ultricies ligula.', 0, 1, '2024-09-05 18:34:13', '2024-04-15 04:06:00'),
                                                                                                                                                  (34, 'Graham Neal', 'Iliana Rowe', 'ilianarowe.grahamneal9123@protonmail.couk', '(07) 1618 1442', 'aliquet libero. Integer in magna. Phasellus dolor elit, pellentesque a,', 1, 0, '2026-03-09 18:21:32', '2025-09-14 14:03:20'),
                                                                                                                                                  (35, 'Dorian Gallegos', 'Zelenia Hale', 'zeleniahale_doriangallegos@hotmail.edu', '(06) 9127 4694', 'ligula. Aenean gravida nunc sed pede. Cum sociis natoque', 0, 0, '2024-04-19 15:59:38', '2024-06-16 21:22:06'),
                                                                                                                                                  (36, 'Candace Craig', 'Claudia Luna', 'claudialuna-candacecraig@google.ca', '(04) 6992 3267', 'ac, eleifend vitae, erat. Vivamus nisi.', 1, 1, '2024-10-17 06:00:22', '2024-06-25 06:47:58'),
                                                                                                                                                  (37, 'Preston Conway', 'Sandra Tyson', 'sprestonconway@protonmail.ca', '(03) 4836 2552', 'metus urna convallis erat, eget tincidunt dui', 1, 1, '2025-12-30 08:38:16', '2025-12-10 05:42:23'),
                                                                                                                                                  (38, 'Aretha Velazquez', 'Piper Barton', 'arethavelazquez.piperbarton822@yahoo.org', '(04) 1875 5802', 'diam lorem, auctor quis, tristique ac, eleifend vitae, erat. Vivamus', 0, 1, '2024-07-22 08:56:27', '2026-03-06 10:19:57'),
                                                                                                                                                  (39, 'Blake Hoover', 'Vance Nielsen', 'v-blakehoover199@outlook.ca', '(04) 7458 2037', 'nulla. Integer urna. Vivamus molestie dapibus ligula. Aliquam erat', 0, 1, '2025-10-11 21:41:36', '2024-09-22 11:15:01'),
                                                                                                                                                  (40, 'Mohammad Morrison', 'Lars Lowe', 'm.larslowe@protonmail.edu', '(01) 8672 2323', 'montes, nascetur ridiculus mus. Proin vel arcu', 0, 1, '2024-10-18 23:20:13', '2026-02-20 10:15:41'),
                                                                                                                                                  (41, 'Olga Bennett', 'Mohammad Erickson', 'mohammaderickson_olgabennett@hotmail.couk', '(05) 5018 3186', 'Donec luctus aliquet odio. Etiam ligula tortor, dictum eu, placerat', 0, 1, '2025-08-22 00:06:17', '2024-09-05 14:15:55'),
                                                                                                                                                  (42, 'Cara Weiss', 'Madeline Adkins', 'c_madelineadkins6379@outlook.net', '(06) 9777 0682', 'Ut semper pretium neque. Morbi quis', 1, 0, '2025-02-15 18:59:34', '2026-03-04 03:06:38'),
                                                                                                                                                  (43, 'Blake George', 'Wilma Webster', 'wilmawebster_blakegeorge@icloud.org', '(07) 0878 4492', 'ultrices iaculis odio. Nam interdum enim', 0, 0, '2024-05-10 00:39:22', '2026-01-07 00:32:15'),
                                                                                                                                                  (44, 'Neil Sanford', 'Coby Robertson', 'c_neilsanford2816@icloud.couk', '(01) 7757 5663', 'et malesuada fames ac turpis egestas.', 1, 0, '2025-04-28 16:26:49', '2024-10-13 06:19:53'),
                                                                                                                                                  (45, 'Quentin Robinson', 'Mohammad Rollins', 'mohammadrollinsquentinrobinson@protonmail.org', '(07) 3613 1449', 'nec, eleifend non, dapibus rutrum, justo. Praesent luctus. Curabitur egestas', 0, 1, '2025-03-04 21:42:40', '2024-05-15 13:34:14'),
                                                                                                                                                  (46, 'Ruth Robinson', 'Shafira Dudley', 'r.shafiradudley@yahoo.net', '(06) 2637 3659', 'arcu. Sed eu nibh vulputate mauris sagittis', 1, 1, '2025-01-05 20:35:20', '2026-02-20 03:39:05'),
                                                                                                                                                  (47, 'Brielle Santana', 'Fuller Parsons', 'briellesantana-fullerparsons@hotmail.couk', '(03) 7723 3324', 'tortor, dictum eu, placerat eget, venenatis a, magna. Lorem ipsum', 1, 0, '2024-04-08 15:53:00', '2024-10-05 15:35:52'),
                                                                                                                                                  (48, 'Dane Glenn', 'Janna Wise', 'd-jannawise@aol.edu', '(06) 3265 7009', 'imperdiet ullamcorper. Duis at lacus. Quisque purus sapien, gravida', 0, 0, '2024-06-23 19:10:29', '2024-05-13 23:34:01'),
                                                                                                                                                  (49, 'Ulysses Wooten', 'Jeremy Oneil', 'jeremyoneilulysseswooten1118@aol.couk', '(06) 9175 3877', 'Cum sociis natoque penatibus et magnis dis parturient montes,', 1, 0, '2025-03-27 12:20:02', '2025-06-15 12:29:50'),
                                                                                                                                                  (50, 'Veronica Guy', 'Bruno Brooks', 'brunobrooks_veronicaguy@yahoo.com', '(05) 5235 7611', 'in lobortis tellus justo sit amet nulla. Donec non justo.', 0, 1, '2024-09-01 16:56:07', '2025-04-16 14:22:43');

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
                                                                                                                                                              (1, 'Chay Fong', 'Hong', 'chayfong9009@gmail.com', '$2y$10$OEDCACaiUaYKhHkeHFuQmOLwDX9oJ4HiTD6K3bmAhkODBpG7zI6t6', NULL, NULL, '2025-03-26 11:10:12', '2025-03-26 11:10:12', 'customer', NULL),
                                                                                                                                                              (2, 'Christian', 'Cochrane', 'cakephp@example.com', '$2y$10$4oCG2ResnEQbYk2rgtdTGe1faLZPOu29GZma4EfRmQ.B6vyHOk7u6', NULL, NULL, '2025-04-04 02:31:18', '2025-04-05 02:17:37', 'customer', '45568_Capture.PNG');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
                            `id` int(11) NOT NULL,
                            `service_name` varchar(255) NOT NULL,
                            `service_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_cost`) VALUES
                                                                  (1, 'Makeup', 6.50),
                                                                  (2, 'Hair Dressing', 7.50),
                                                                  (3, 'Dress Making', 8.50);

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
                                                                                                                                                             (2, 'Lucy', 'Reig', 'lucyr@chiccharm.com.au', '$2y$10$UaR/Z4ljlvG1LztxsODz8uWljiN0Hv8MpN8mKZjZ5NEBOfchCFwei', NULL, NULL, '2025-04-05 11:51:05', '2025-04-05 15:24:22', 'stylist', NULL),
                                                                                                                                                             (4, 'Michael ', 'Jackson', 'hehe@gmail.com', '$2y$10$QAXdlH9eRlnPjL83P300BuDDNzHyDjchXuSxfoJTVNg3Suv8Me/UW', NULL, NULL, '2025-04-05 04:23:35', '2025-04-05 04:23:35', 'stylist', NULL),
                                                                                                                                                             (5, 'Michelle', 'Yang', 'Michelleyang01@gmail.com', '$2y$10$/2Ya/xrRlHOry9uwG8Nh3OtfmSj3pCQJ/phKdkbt0KAVz0kCRaCza', NULL, NULL, '2025-04-05 14:48:30', '2025-04-05 14:48:30', 'stylist', NULL);

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
                                                                       (4, 2, 3),
                                                                       (7, 4, 1),
                                                                       (8, 4, 2),
                                                                       (12, 5, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
    ADD PRIMARY KEY (`id`),
  ADD KEY `customerForeignKey` (`customer_id`);

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
-- Indexes for table `customers`
--
ALTER TABLE `customers`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
  ADD UNIQUE KEY `email` (`email`);

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
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stylists`
--
ALTER TABLE `stylists`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stylists_services`
--
ALTER TABLE `stylists_services`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
    ADD CONSTRAINT `customerForeignKey` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
    ADD CONSTRAINT `bookingForeignKey` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stylistBookingForeignKey` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
