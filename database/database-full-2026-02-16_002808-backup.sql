-- ==================================================
-- Full Database Backup
-- Generated: 2026-02-16 00:28:08
-- Database Type: MySQL
-- Database: u849062718_timetrack
-- ==================================================

-- ==================================================
-- Table: cache
-- ==================================================

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: cache
LOCK TABLES `cache` WRITE;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_5fc840283aa299692a0ee435437c05915d21c2e4', 'i:1;', 1765231896),
('laravel_cache_5fc840283aa299692a0ee435437c05915d21c2e4:timer', 'i:1765231896;', 1765231896),
('laravel_cache_7076ff1d09f41aed1e901e29cb6b22a0c1e5f99b', 'i:1;', 1764048508),
('laravel_cache_7076ff1d09f41aed1e901e29cb6b22a0c1e5f99b:timer', 'i:1764048508;', 1764048508),
('laravel_cache_801bbe8c56ba7f5fc9a3f46abfed606d032e06af', 'i:1;', 1770186153),
('laravel_cache_801bbe8c56ba7f5fc9a3f46abfed606d032e06af:timer', 'i:1770186153;', 1770186153),
('laravel_cache_app_settings_cache', 'a:18:{s:20:"invoice_company_name";s:12:"NAVJOT SINGH";s:23:"invoice_company_address";s:44:"19 Grand River Ave
Brantford, ON, N3T4W8, CA";s:18:"invoice_tax_number";s:15:"RT0001764382552";s:23:"payment_etransfer_email";s:18:"ns949405@gmail.com";s:17:"payment_bank_info";s:114:"WealthSimple
  Transit number: 00001
   Institution number: 703
   Account: 36126969
   Account name: NAVJOT SINGH";s:20:"payment_instructions";N;s:12:"email_mailer";s:4:"smtp";s:15:"email_smtp_host";s:18:"smtp.hostinger.com";s:15:"email_smtp_port";s:3:"465";s:19:"email_smtp_username";s:24:"noreply@brainandbolt.com";s:19:"email_smtp_password";s:8:"8]xjp@@V";s:21:"email_smtp_encryption";s:3:"ssl";s:18:"email_from_address";s:24:"noreply@brainandbolt.com";s:15:"email_from_name";s:23:"Freelancer Time Tracker";s:14:"stripe_enabled";s:1:"1";s:22:"stripe_publishable_key";s:107:"pk_live_51J7E4oK04LWbovV9FP88DCAXWAGH06StEWufz882ymmpnkvfuJQVV3hzKxvktIKQRZEXsQuSi1RWcBGpd9jjUaAO00D8NX3WxP";s:17:"stripe_secret_key";s:400:"eyJpdiI6Ik5qQWRibmFpTlhlcm1icHZaeTlkSHc9PSIsInZhbHVlIjoiWHBCckxONG1JdmpFYlFEVUwvMGJJbWdPYjZDcXZwenF4UmpBWFNaTlNjcm5lMzlGdEZKOGZ1VEJ2Y0dnWklvYkNveG5MVTZTRlUwamRURUZlTFQ1RUw1elM5VXZjZnJhSm1JV3lrOFB0ejlMZVkwMDJneGEvUDdGRUV5czlDYXpLSlZhL1BkWXZ2amd2b250UXYyV252TFhsMVk1R01HY1huU3Mwa1MvSWJNPSIsIm1hYyI6ImEzNTRkZmMxOTg0ZTZjN2RjMGI3YmFkMTk3OWEwZjlhNGQzN2IzNjQzZDkxNWM3Y2JiOGZmZTRkYzhlMzlhYmYiLCJ0YWciOiIifQ==";s:17:"stripe_product_id";s:19:"prod_TiTj2DAMotqTP8";}', 2082698901),
('laravel_cache_c3095a423442d3f4630d870a521e1f6fa8786753', 'i:1;', 1767339198),
('laravel_cache_c3095a423442d3f4630d870a521e1f6fa8786753:timer', 'i:1767339198;', 1767339198);
UNLOCK TABLES;

-- ==================================================
-- Table: cache_locks
-- ==================================================

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- Table: invoice_history
-- ==================================================

DROP TABLE IF EXISTS `invoice_history`;
CREATE TABLE `invoice_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `action` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_history_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_history_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: invoice_history
LOCK TABLES `invoice_history` WRITE;
INSERT INTO `invoice_history` (`id`, `invoice_id`, `action`, `description`, `metadata`, `created_at`) VALUES
(29, 12, 'created', 'Invoice created as draft', '{"invoice_number":"INV-2025-11-0001","total":"293.80"}', '2025-11-14 02:36:25'),
(30, 12, 'sent', 'Invoice email sent to survailpro@rogers.com', '{"email":"survailpro@rogers.com","subject":"Invoice INV-2025-11-0001 from NAVJOT SINGH"}', '2025-11-14 02:41:01'),
(35, 12, 'viewed', 'Invoice viewed by client', '{"ip":"2607:fea8:99c3:b600:7ae6:ff38:a22d:8fed","user_agent":"Mozilla\/5.0 (Linux; Android 15; SM-S711W Build\/AP3A.240905.015.A2; wv) AppleWebKit\/537.36 (KHTML, like Gecko) Version\/4.0 Chrome\/141.0.7390.122 Mobile Safari\/537.36","view_count":1,"viewed_at":"2025-11-17 11:33:02"}', '2025-11-17 11:33:02'),
(36, 12, 'paid', 'Invoice marked as paid', '{"total":"293.80"}', '2025-11-23 12:33:12'),
(37, 17, 'created', 'Invoice created as draft', '{"invoice_number":"INV-2025-11-0002","total":"67.80"}', '2025-11-25 00:19:04'),
(38, 17, 'sent', 'Invoice email sent to ns949405@gmail.com', '{"email":"ns949405@gmail.com","subject":"Invoice INV-2025-11-0002 from NAVJOT SINGH"}', '2025-11-25 00:20:09'),
(39, 17, 'viewed', 'Invoice viewed by client', '{"ip":"64.233.172.5","user_agent":"Mozilla\/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox\/11.0 (via ggpht.com GoogleImageProxy)","view_count":1,"viewed_at":"2025-11-25 00:21:05"}', '2025-11-25 00:21:05'),
(40, 17, 'scheduled', 'Invoice email scheduled', '{"email":"ns949405@gmail.com","scheduled_time":"2025-11-25 23:26:00"}', '2025-11-25 00:25:40'),
(41, 17, 'scheduled', 'Invoice email scheduled', '{"email":"ns949405@gmail.com","scheduled_time":"2025-11-25 00:28:00"}', '2025-11-25 00:27:28'),
(42, 17, 'cancelled', 'Invoice cancelled', '[]', '2025-11-25 00:30:08'),
(43, 18, 'created', 'Invoice created as draft', '{"invoice_number":"INV-2025-12-0001","total":"67.80"}', '2025-12-01 05:28:34'),
(44, 18, 'sent', 'Invoice email sent to survailpro@rogers.com', '{"email":"survailpro@rogers.com","subject":"Invoice INV-2025-12-0001 from NAVJOT SINGH"}', '2025-12-01 05:29:57'),
(45, 18, 'viewed', 'Invoice viewed by client', '{"ip":"2607:fea8:99c3:b600:5c15:f768:bf48:705b","user_agent":"Mozilla\/5.0 (Linux; Android 16; SM-S711W Build\/BP2A.250605.031.A3; wv) AppleWebKit\/537.36 (KHTML, like Gecko) Version\/4.0 Chrome\/142.0.7444.106 Mobile Safari\/537.36","view_count":1,"viewed_at":"2025-12-01 09:12:07"}', '2025-12-01 09:12:07'),
(46, 18, 'sent', 'Invoice email sent to survailpro@rogers.com', '{"email":"survailpro@rogers.com","subject":"Overdue Invoice INV-2025-12-0001 from NAVJOT SINGH"}', '2025-12-08 17:10:37'),
(50, 20, 'created', 'Invoice created as draft', '{"invoice_number":"INV-2026-01-0001","total":"135.60"}', '2026-01-02 02:27:48'),
(51, 20, 'sent', 'Invoice email sent to survailpro@rogers.com', '{"email":"survailpro@rogers.com","subject":"Invoice INV-2026-01-0001 from NAVJOT SINGH"}', '2026-01-02 02:30:09'),
(52, 20, 'viewed', 'Invoice viewed by client', '{"ip":"69.147.93.12","user_agent":"YahooMailProxy; https:\/\/help.yahoo.com\/kb\/yahoo-mail-proxy-SLN28749.html","view_count":1,"viewed_at":"2026-01-02 10:54:40"}', '2026-01-02 10:54:40'),
(53, 20, 'paid', 'Invoice marked as paid', '{"total":"135.60"}', '2026-01-11 10:08:17'),
(54, 18, 'cancelled', 'Invoice cancelled', '[]', '2026-01-11 10:08:25'),
(55, 21, 'created', 'Invoice created as draft', '{"invoice_number":"INV-2026-02-0001","total":"67.80"}', '2026-02-04 01:02:48'),
(56, 21, 'updated', 'Invoice details updated', '[]', '2026-02-04 01:16:25');
UNLOCK TABLES;

-- ==================================================
-- Table: invoice_items
-- ==================================================

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `time_log_id` bigint(20) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `work_date` date NOT NULL,
  `hours` decimal(8,2) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_index` (`invoice_id`),
  KEY `invoice_items_time_log_id_index` (`time_log_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_time_log_id_foreign` FOREIGN KEY (`time_log_id`) REFERENCES `time_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: invoice_items
LOCK TABLES `invoice_items` WRITE;
INSERT INTO `invoice_items` (`id`, `invoice_id`, `time_log_id`, `description`, `work_date`, `hours`, `rate`, `amount`, `created_at`, `updated_at`) VALUES
(17, 12, NULL, 'Survail website buildup & rewamp', '2025-11-14', 1.00, 200.00, 200.00, '2025-11-14 02:36:25', '2025-11-14 02:36:25'),
(18, 12, NULL, 'Monthy Web server Maintaince and SEO', '2025-11-14', 1.00, 60.00, 60.00, '2025-11-14 02:36:25', '2025-11-14 02:36:25'),
(23, 17, NULL, 'Monthy Website and server Maintenance', '2025-11-30', 1.00, 60.00, 60.00, '2025-11-25 00:19:04', '2025-11-25 00:19:04'),
(24, 18, NULL, 'Monthy Website and server Maintenance', '2025-12-01', 1.00, 60.00, 60.00, '2025-12-01 05:28:34', '2025-12-01 05:28:34'),
(27, 20, NULL, 'November - Monthly Website and server Maintenance', '2025-11-30', 1.00, 60.00, 60.00, '2026-01-02 02:27:48', '2026-01-02 02:27:48'),
(28, 20, NULL, 'December - Monthly Website and server Maintenance', '2025-12-31', 1.00, 60.00, 60.00, '2026-01-02 02:27:48', '2026-01-02 02:27:48'),
(29, 21, NULL, 'January - Monthly Website and server
Maintenance', '2026-01-31', 1.00, 60.00, 60.00, '2026-02-04 01:02:48', '2026-02-04 01:02:48');
UNLOCK TABLES;

-- ==================================================
-- Table: invoices
-- ==================================================

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `client_name` varchar(191) NOT NULL,
  `client_email` varchar(191) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `company_name` varchar(191) DEFAULT NULL,
  `company_email` varchar(191) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('draft','sent','paid','cancelled') NOT NULL DEFAULT 'draft',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stripe_fees_included` tinyint(1) NOT NULL DEFAULT 0,
  `stripe_fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stripe_fee_percentage` decimal(5,2) NOT NULL DEFAULT 2.90,
  `stripe_fee_fixed` decimal(10,2) NOT NULL DEFAULT 0.30,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `stripe_payment_link` varchar(500) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(100) DEFAULT NULL,
  `view_token` varchar(191) DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `opened_count` int(10) unsigned NOT NULL DEFAULT 0,
  `opened_ip` varchar(64) DEFAULT NULL,
  `opened_user_agent` varchar(512) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `scheduled_send_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  UNIQUE KEY `invoices_view_token_unique` (`view_token`),
  KEY `invoices_project_id_index` (`project_id`),
  KEY `invoices_status_index` (`status`),
  KEY `invoices_invoice_date_index` (`invoice_date`),
  KEY `invoices_project_id_status_index` (`project_id`,`status`),
  CONSTRAINT `invoices_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: invoices
LOCK TABLES `invoices` WRITE;
INSERT INTO `invoices` (`id`, `invoice_number`, `project_id`, `client_name`, `client_email`, `client_address`, `company_name`, `company_email`, `company_address`, `invoice_date`, `due_date`, `status`, `subtotal`, `tax_rate`, `tax_amount`, `stripe_fees_included`, `stripe_fee_amount`, `stripe_fee_percentage`, `stripe_fee_fixed`, `total`, `notes`, `stripe_payment_link`, `stripe_payment_intent_id`, `view_token`, `opened_at`, `opened_count`, `opened_ip`, `opened_user_agent`, `description`, `sent_at`, `scheduled_send_at`, `paid_at`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(12, 'INV-2025-11-0001', 4, 'Survail Protection and Investigation Services', 'survailpro@rogers.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', 'NAVJOT SINGH', NULL, '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2025-10-31', '2025-12-20', 'paid', 260.00, 13.00, 33.80, 0, 0.00, 2.90, 0.30, 293.80, NULL, NULL, NULL, 'evVHdujunNv9PEPYT8uvCbrSvDJBqfNeQtclPpcW', '2025-11-17 11:46:05', 2, '2607:fea8:99c3:b600:7ae6:ff38:a22d:8fed', 'Mozilla/5.0 (Linux; Android 15; SM-S711W Build/AP3A.240905.015.A2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.122 Mobile Safari/537.36', NULL, '2025-11-14 02:41:01', NULL, '2025-11-23 12:33:12', NULL, '2025-11-14 02:36:25', '2025-11-23 12:33:12'),
(17, 'INV-2025-11-0002', 4, 'Survail Protection and Investigation Services', 'ns949405@gmail.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', 'NAVJOT SINGH', NULL, '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2025-11-30', '2025-12-07', 'cancelled', 60.00, 13.00, 7.80, 1, 2.33, 2.90, 0.30, 67.80, 'A Stripe processing fee (2.9% + $0.30 CAD) applies only when you choose to pay via Stripe.', 'https://buy.stripe.com/test_7sY6oG4VP1Xfe5wfsQ9AA08', NULL, 'oDeA8Ia0QNq5hIo11RcxmV2W5HcMdRWkSXFktz1D', '2025-11-25 00:21:05', 1, '64.233.172.5', 'Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox/11.0 (via ggpht.com GoogleImageProxy)', NULL, '2025-11-25 00:20:08', NULL, NULL, '2025-11-25 00:30:08', '2025-11-25 00:19:04', '2025-11-25 00:30:08'),
(18, 'INV-2025-12-0001', 4, 'Survail Protection and Investigation Services', 'survailpro@rogers.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', 'NAVJOT SINGH', NULL, '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2025-12-01', '2025-12-07', 'cancelled', 60.00, 13.00, 7.80, 0, 0.00, 2.90, 0.30, 67.80, NULL, 'https://buy.stripe.com/test_eVq7sK0FzgS9f9A4Oc9AA09', NULL, 'aNarrjs3ayzGqSYVlCH3HmRpsqsf48Rzk0fIm3JI', '2025-12-01 09:12:07', 1, '2607:fea8:99c3:b600:5c15:f768:bf48:705b', 'Mozilla/5.0 (Linux; Android 16; SM-S711W Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/142.0.7444.106 Mobile Safari/537.36', NULL, '2025-12-01 05:29:55', NULL, NULL, '2026-01-11 10:08:25', '2025-12-01 05:28:34', '2026-01-11 10:08:25'),
(20, 'INV-2026-01-0001', 4, 'Survail Protection and Investigation Services', 'survailpro@rogers.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', 'NAVJOT SINGH', NULL, '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2026-01-02', '2026-01-10', 'paid', 120.00, 13.00, 15.60, 1, 4.36, 2.90, 0.30, 135.60, 'A Stripe processing fee (2.9% + $0.30 CAD) applies only when you choose to pay via Stripe.', 'https://buy.stripe.com/6oUdRa5Dsed9al4h2NcZa0g', NULL, 'YOkWCKu9l6ZtjFTMlRqsEJTHLc3o5XyjWWLAdpmw', '2026-01-02 10:54:40', 1, '69.147.93.12', 'YahooMailProxy; https://help.yahoo.com/kb/yahoo-mail-proxy-SLN28749.html', NULL, '2026-01-02 02:30:07', NULL, '2026-01-11 10:08:17', NULL, '2026-01-02 02:27:48', '2026-01-11 10:08:17'),
(21, 'INV-2026-02-0001', 4, 'Survail Protection and Investigation Services', 'survailpro@rogers.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', 'NAVJOT SINGH', NULL, '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2026-02-04', '2026-02-07', 'sent', 60.00, 13.00, 7.80, 1, 2.33, 2.90, 0.30, 67.80, 'A Stripe processing fee (2.9% + $0.30 CAD) applies only when you choose to pay via Stripe.', 'https://buy.stripe.com/7sYdRa8PEfhdgJsfYJcZa0h', NULL, 'AL93VukHUVmSGhYSdKGKtOPgIF1jmEzxD4iNRTSh', NULL, 0, NULL, NULL, NULL, '2026-02-04 01:21:33', NULL, NULL, NULL, '2026-02-04 01:02:48', '2026-02-04 01:21:33');
UNLOCK TABLES;

-- ==================================================
-- Table: migrations
-- ==================================================

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: migrations
LOCK TABLES `migrations` WRITE;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_06_23_190318_create_time_logs_table', 1),
(2, '2025_06_23_191616_create_sessions_table', 1),
(3, '2025_10_06_121412_create_projects_table', 2),
(4, '2025_10_06_121511_add_project_id_to_time_logs_table', 2),
(5, '2025_10_11_073816_add_tax_field_to_projects_table', 3),
(6, '2025_10_12_044415_create_test_deployment_table', 4),
(7, '2025_11_06_090641_add_email_and_address_to_projects_table', 5),
(8, '2025_11_06_090656_create_invoices_table', 5),
(9, '2025_11_06_091652_create_invoice_items_table', 5),
(10, '2025_11_06_101101_add_company_fields_to_invoices_table', 5),
(11, '2025_11_07_054324_create_cache_table', 5),
(12, '2025_11_07_120000_create_settings_table', 5),
(13, '2025_11_08_022659_add_cancelled_at_to_invoices_table', 6),
(14, '2025_11_08_072409_add_scheduled_send_at_to_invoices_table', 7),
(15, '2025_11_08_033849_create_invoice_history_table', 8),
(16, '2025_11_12_101420_add_view_tracking_to_invoices_table', 9),
(17, '2025_11_15_060110_add_stripe_settings_to_invoices', 10),
(18, '2025_11_16_220940_add_stripe_fees_to_invoices_table', 10);
UNLOCK TABLES;

-- ==================================================
-- Table: projects
-- ==================================================

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `client_name` varchar(191) DEFAULT NULL,
  `client_email` varchar(191) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `color` varchar(191) NOT NULL DEFAULT '#8b5cf6',
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `has_tax` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_status_index` (`status`),
  KEY `projects_status_created_at_index` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: projects
LOCK TABLES `projects` WRITE;
INSERT INTO `projects` (`id`, `name`, `client_name`, `client_email`, `client_address`, `color`, `hourly_rate`, `has_tax`, `status`, `description`, `created_at`, `updated_at`) VALUES
(3, 'Hey Harper', 'Polaris Tech services', NULL, NULL, '#8b5cf6', 18.00, 0, 'active', NULL, '2025-10-08 23:04:03', '2025-10-08 23:04:03'),
(4, 'Security Guard', 'Survail Protection and Investigation Services', 'survailpro@rogers.com', '148 Henry st. 
Brantford, N3S 5C7, Ontario, CA', '#e0102f', 17.75, 1, 'active', NULL, '2025-10-09 00:39:52', '2025-11-12 04:45:04'),
(5, 'Security Guard', 'Magna Security', NULL, NULL, '#2f0396', 18.00, 0, 'active', NULL, '2025-10-09 00:45:56', '2025-10-09 00:45:56');
UNLOCK TABLES;

-- ==================================================
-- Table: sessions
-- ==================================================

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: sessions
LOCK TABLES `sessions` WRITE;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('02o9TsxNtG7lxq6YiTB15CnOKnlnrp8jhXVeXF9t', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmthYm80aHIwOE9lM0llQkxCQ1doV21XZmpielpnNkVpNmIwQXphNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218905),
('1fARHUE0AvzxPDE5VKSY1b2wmwsZAciyyb7NyOmV', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTTBUeDZ4V0FCMGZYWkh5WmNQYkFKZDI1dmcyS2s2bDZWZkZFU2FUSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219203),
('1fxXH2XkvAPopG3tqhqvUhDJlDIJ7FdxxGxcQggI', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWl0RE9xVFRweTdUSjVmRmVuM2lwNTlkMGVJN3N3cjNDTmVhZjZSYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218182),
('1gtKfwRCXjF0Yl83j7FBG5LDm3Qu2Bjh0oLjuWuZ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXg0Z2VCaWRnZkE1V2tnek9HdnZuZzY0eE43MTNlOE83WkppZG5GViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213083),
('1lgGw3HTHxBtzxVBhyhN1geqTJxVcjaLJA2m82Wk', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDJqU2t4cFlrQTVjWlp3eWxyOE9GbDk0d0hjSWozd1NqV3VXWURCRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215243),
('2nrrB6byop488WDvnQ6h0zG2SNme6i1egATHHnqo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZGZmNlRsTVNIYmRkNmM3OHI2UEhtQ1VjdFE5MFVpajVDamFic0dUbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217103),
('3nwfiNOnkTtrqfDJBQNhVhVoRi0ys178DLmuMxzt', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkJwR1pYUXRBbUdzMWQ5eFMxZFNuWm1MMjVHRnU3V0c1S2N5R0s3WCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216263),
('3qVXFyGYFqaZDcwnv3dnshNFob8F7hxmXk4V6HHs', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRmprUkt1ZVNTMHRBZ3M5bnVpaU9RY3VXZVFjTUtkVEh0Q3RERXg4MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219388),
('3VEfBzE2oO7wzr5sbY5anBrChc0Vm5iXCGbRva0e', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieEJsaXBLWHdhSXRibFUyRDJvVXo4VGhibDIzbW5tWDF5bEwxSE9HUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219682),
('3ZHNEpDpkq1ktI9eg9bDSsTeirLE67Fdqns85ksb', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2pwdXdiUnljYVA2bkNZUW1NQWFiM1pQSkNEUXozN1dtNjZlbE93QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213022),
('4E37HB8V7OhScflIfH2rRtoYgrGwctB6jW5eisG4', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1ZXaGIyd1pzVnpDa0xnTlYwdVducFVmVkxUbVFXZlgzNFEySFBJNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217582),
('4SEPa8lt9QukJjOoiTItHecnZKXevSPXfxJIjK4M', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFZEZDJISzM2elV0eHhtYUxEbWJWekVsU2JsZTV1Mk50TkFkREZ5NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218663),
('4V2DDrpzCMsux3eHMWzYD0xPHQCkZghawTD66j7b', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0psZzlFWUFDTFlVSnlia2w2YTRoU1BJc3htQVowMmdmU0d5b0xIWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215963),
('4ysiyCMfhayzH8tJRE8KRc5MqwsYXcw9L8vRRGpr', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQW9wcnVHNHpmZWdadnQyY3d2a2xUVTV6djUxUm5PYWJtV05LQmZqVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218904),
('52knkL2RUz46TBlzNQPjlHlWCevHU1Phw4lCFwf9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVJxSmtFS2U5Rm5TNllMWnkwRGVDQnZTYURyc3dnQndDYXJnNmhuNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217642),
('5g8GYdq9y6S9UKxgWt5OmbBTBOgRXMF5G0Q9qR07', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVNzY3h2clNvREU1RjFpT2FSWmdPcjNoNVZhRzhIbEhadlRZQ0lSbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217523),
('5hTq6zDSr2WmIAUkxiU3SLYV6ekhDI8qiUVZb6In', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWRQUGNPelRiNllZZE0yZ01UZkxHTGQwNEZ2aTZsU2I0T2lQOTFsaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216083),
('5NbYRrIg1LcCc1AuysgllZBkS6WUqUvgRzgDB1Mz', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEppNXlCbDZqeWZJNU9VdVVtQzFxVzRFU1NRcmpNZEpENkFiUnlRaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213503),
('5pz9Lq49Y0Jo51wvGEr5Wauu0eIehEEBC2sK0fPy', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSktubnE5VVpuSkdmNlpPcWpZVWtONGM4YlVXb211MTh1WGFUWFZrUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213383),
('5wXekDyfkkciG9vRGoPhRoeuBfOTv8vuXNLpFDx0', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmRZT3lzbklNTlZlaDdlN1FKN1llMXlWSUdwUUJXWXlFYUdXa0xMOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218662),
('5ytA4igGCQ6jEGWpf9AbAygWHOdS00oahAN40XsR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXdJMEdUSndhWUFVekpveHVBNzkxV2M4WEZQdUU4NHhwWVhZdkdZSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214163),
('688UGSOs0jj49CUfpd0HhtKr3NwcAWCTEuaPCnEc', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3lRTTE0NVdQUmZyanVqUzVwR3JUSnZSakROdkJmVTk1Y2dYa2Y5VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216743),
('6CdnU64f9Z5L8ZFInLjo9OZhWNsfYVaOFqoSbORH', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielNHRWZCajdubk93cU5sUkV2TmJWNHJwZHhSWng2akNCcmtQNG8yMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215486),
('6PVJGwK19cPGEQ2D6tul4A1M1ZVInb7WBITaYhbM', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNnVSTGdqMTRodGhWN3RwR1ZVNEhneWxXUVY5UXNmb2YzZThVSWprNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217403),
('71YYRhrcUL2kLSDIRgxj5iKFxBPmLroEtbsTyur8', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiazBSSGtMMjkwbjBORWJTenN2ZnY1b3VyTnRuRlRtNUNzS3ZvRnl6USI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213863),
('73jXCYPxo3XoEa6OgdqkkfNWyTSgMdAo15KjiClh', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFZvNFRWWmdobk0zd1NBcFFoMmpYeHFrdHpyNDBqWTloempRTUdlSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216143),
('75ym5FLYkYH3mlLLJXYIav2imj8aB8huWEE2y8oL', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGY5QUxiQ1JveFJrcEpsalpnQVRvRnhBcU54cmplc0FWa1h1d0ZrdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214404),
('77SYAnXnIeDvV9FxnKq5a4fVJwKyMELLONHAj7h9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlhFNTFGRHJSa3M1TGJOUDlhNU9sSTIzcDZhUnlWaU13NkpZanFweSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216083),
('7fmtogVnZhXLhMFiq2DPOYQiTaxKkuRJT7AWRDlZ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidldRWlpBOWwzNDZHTUZWbGtpZ0FVZndhQXlFVGpnOHdEZnRjN051TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212963),
('7ttyndBflGVaIKYC6CjiwC6nTy3rfoAGh3BQcGiG', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVM5MjQ2SmE1TTN1YXd2ZWpENG5zMkdESElxSWswdndRSFNIdVJSaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217163),
('82xHwUn0XejMSVSftJuzQfk6cQGMK28OP1ons75a', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUZoQVNUVHJDYzlBMU5NNDZlbE1EZnM3Qm9wNVdoaEFMV1A2dEk2USI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213923),
('84pZ489xahsKk1Yao4WvWovczumSDPbJjs5n9flo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlRqdFljcFVQZm80Y3NQbHJ1STdtUkhhVVY5bzNLalBpcG5qS2pIbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213562),
('86EXyRnHWGiWBefFbuRmDuFy1OiblfSRpuvbInZT', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicG05dTFObnNJMVNZU29QRVVWWEpHOTNxVnduUUp4bWxXcW9Xd1g0VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216923),
('88qTdM7ZSfUP9onYFtnCoMxrloUIRWs4oFwefhS4', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWMxeUhOSktXS3NUNnZKM2lKYW5HNUlxZGZLRmhITW9HNzJMVmdQYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214223),
('8lIazcSQMuXwetpyTYOlB7R39G08vPSU7pIPzBY1', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNG5Gb2tNbDZHeXlQUVJVZlNEMlBHdEhxOHJIRG1RM2hzM21HNGNpNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212604),
('8R10XNm7lhvHbSn6e6na2jtEGmGQNMcFi0o4DKlR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkxhTjBBR3hEYTMyb1B3S1FuZ3pGZ3NyeHJkNFJZN3p5b1RtVmU2eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219142),
('8yWSkEXPbMmg7dPC9WRUZtzsBdqR2SukimaMtn21', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOE9wOXFzSFJXa2FyVlFkQ2kxODNISXRqS2VNaldLaWd3YVlveDgxcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214042),
('91mqXy4ceCnSUcGaVzdag1gmc6r3tE0aL9Iyv8Ed', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1pSOFMyNXlGYzhsekwxc1VyU1BBZzdUQVYzeGRmbjNMUW1jZm00eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213023),
('9EvfafIrymR2epEUjToG2XwqtlhG6DB3M1niyHJO', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia05teTcyZFdTOFBnOXNNRkNRbXNTUG5EbWRlQkdjVElocGJOV0RzTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217702),
('9mDVpkxYMQGnYNFi1MMoIh89cmnEM3yQp0Ih0ieI', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmJOWVB0ZmNmSlFIZ1MyOGYxbFA3Z3NVWkNiUXNITDVWT0Z5ZUNxNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218003),
('9q3V4VVVWotGWkmZWZdvCe1LjAkNFfO4uUjlBwxf', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTBIaXdKNUZKT3dNQ25ONGtkMUI5eGgwV2lnVERiU2NrUVBBSjI2UiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218543),
('9TmP4YcH1CQqQNbiRZywg6aVi2E4G1fjfzTEgCQE', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialJaQXFwTlRudzF6SEdrNmI5bU1JdjdISUVXeHhWdEc1ZFl1cHZOOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216563),
('9UTsCi8GgvdhBZGZdo7FCmUEgyhW1TkqmY5IEUHm', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlE2djRDSEI0NHVxVnBLNTUxbnlWbHRGZHM5NXlPT0JzbVpDMk9IbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217283),
('9ZEhDnLxyt8RU4yhD6eEs7pyj5hQRlY1t20NjEYn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFdkM25xMlpIN042eVpBbzNpVnNFdHNteUtmSDBBWHptWUZmTkZQMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213623),
('9ZIgpsN3sHmZfshbMBQ4G7gATq01DEh2tj4jYmsX', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3RKUGlyNVJYUlJuVHI3dGxabmVKQmhuMDhmV3FEYTlTWkJkQUl4WCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214582),
('A0iCuBNrJLpY5WiLsMbenSUKdAlHMR24mxPvDMqL', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ0V4YjZncnc4VmlVSWFLeTlNNWJFRFpMcjEzTzhYeGlDTGVNSDZJdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217222),
('A3eAcvf2OZ722Pj3UTmY3wb51ONEzv3OjD4jZSLQ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTDBYSWhrMkxDOVBpZDhTVEdmQXZRRkNFT0o2Mzk5MmZac0dRMWpvYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215003),
('A4TZy1YkNm5eixac2G0LzeHsLRDIdOMYyFHdxX5D', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid1RBZmN3eFVJYWlldjRIRXRDelRRemhoSll0YzE5eDNIbFFLVWl3RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214942),
('A92LdJJ1L8LZcXlPPNXtyHK4xh39KByt3ClcMJZV', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEFOVGlsd09UTnNWUXNWVXg0ck9FTjJGSzBsNDhSWGZreXlDeWFmNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213682),
('ACNe33VFIJNY7tYW4sKESi1wKNQcwNAlamQS6j8s', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzRuRmJzMTg0U2VySkJiYmZjcnI5Z3JreG1mQWVDNFhNa21pUVBnZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214642),
('aDMytNljXQThq0pKL37py9w806wEkesWdsBvyKY9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUZrdFdtTWhoNWVYYjdMeVZmcGx5ejk4WWZ4aTVFSWx4QUQ5b2x2ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216262),
('AMCAMCgWp1YDuv5DVlKKI84ZYyImExME33ThPiVd', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1NPV3pyQXBaWWNZYXVId0NJdzhUTEZHM1UxTnI5N01tcFVoSmR0QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214342),
('aSvFnWZnIouq7cWmh68l7NkgnpmbCWHMhj11xuN7', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid1Y3NURMQ3FvdlJHbDB1R0FRMHdMejNqWXVGcXZTR21uYkQ1R2x5diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218423),
('aWY5tZeDTRwIU3pt7P3uQE0QMAvZGKJJDLRbmOBh', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMEg4dkhnNkd5SGFiZmduN2tOdXZGRnBvb1lXZ1ZwUEFOSXdDdVY2MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218722),
('B8H1jZ2Bs6nEwPCTxhBxgLzT6annszwGuoxWairp', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVFRdTRpTUU5dkVpaVJUS0RVS3F1cGxhd2ZmNkhweGJsaWhKdzBCMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215183),
('BBdUZ7DAR43ETbSy8dF0MPMcsItufnGiPJe5C22Q', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkFOVDM0NU1yNmJJemhlYnpSNldza240bDVheU1WQUVQQTZlQXQ0eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219088),
('bbNwKnT9o5GSsLzc0gqnUbHBClIbYlY5crtffNMJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGpXam5wNGpYcGJZaGdiWnlOblZ6YjBFTUtuWmpGN1hweGN3UjFHTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212484),
('bJjUqpZfy4k0e1fTXRf4sif3gpiiyIa9Ie1E5WUL', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQXNXWGc3YTJ1VFRrdmpCcmdGaGNRbTl5OXZlUGtvcXB2ZDlISEgxYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219088),
('BLu7vFSJSnzrV2M1vyqFXEOPzp4RYXTu8S71dOWr', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3RDV3ZkdW02Z1ZoM1dvMHhPZE5VSmIzelZpaTZvbDl4RGZUV3RqMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218122),
('bmU64GxkMPXp6d6lnbl4GLEziu7q96HTq68fepX8', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmNaVHAzVklEMk44YXBPSVd5NzJwNVdLbW5KZTBGRVZlbTRXS1E0VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214882),
('BR6befBvnqYPuECiaNV7YHYxEFqVEUDy9LrZIZGz', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjZlTUdaRXJTbXJhVDhBWGE1MVdnd2Vva0k3ZlJTM1k2ZUhOVjR5QSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218122),
('bujy8ymm3M7HMlfrVhgeqSfZ94MxdUEP8QzAYxG9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZU5qV2MxWjE5Y0VqbTNGTWhoRGNZc2VFa0kwZEhhWnZHcFBiN2lWQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217822),
('BvOws4vBWqSgkpHr7cmhAK1M1IYsoqgvDziWFSrC', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWhlN3pzdTBlT2t2ck9WTmt0WU5uTGZBa0Nyd0hQdjBwZnYwZmNycSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215003),
('Bxkd1l3dtYamUKEvTJGfm3ca9OQ8GP21Pi8IEKvC', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEtOSE1WeGQzeXlxMVhnN2pLMDY2N3hYWXJxQ3NRS3JYR0VCUjZyUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212663),
('bZvCxxUJJn09JFxwktAdUjVuQrd1CjdRxD0raRi6', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREpqbkVsVzFSOHRFaWNRUTdtTm10cWhTTUJ6eTZQSk5JNlJxRzFOWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218242),
('c1jsn2upU9OZgbhDYGM7flaLuj5xKYO8nwk2jp9r', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXE4SU9sbXpYeWlTNElEcTl2WHBES1JxWEhlbUZTcm1OamtXOFdGSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212902),
('c8m45mYi2R912UqMg6tDaHoSOxiEms9wxAoM72gO', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoienl2MFdyU1Exd2RLR1pnQkdGOU1ZejZ6N3RQT2pnazYwSGh4aU54NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215122),
('cdOngLiDG5csQJ1SMz1MR8o2Rj5FYs63H2OXdnBB', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZW5wbXpDbEJ0Slh2WkR6WlZmVHNpWTVXcHlnQ1UzTnMxZkc1eGpXWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213923),
('CjhYUJpm55l2lpXqQbID6GvWLwg7R44PrdJhEyTq', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidFJPTll6OHJNOGVRTmEzbTFDanVIYjY5djY3REt2RDdqdkc0NGFYZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216382),
('CTrmZcWZHQ9MMDzOdlep2JyOHffDyVuMMfJgmaaM', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVk1qdndxZW9XemdBVjhrMVVPa2xMclBCcHdkY3dBRmRtWXZiWE5wNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218063),
('CwSPqXk8cf1vHoSqJ1r43Rc4YaJXfCT7Gm3ngVCW', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2Fzb2MyOVpJdGx4bURiZjI1eVBTaTZ2WWdvU3Rndk9KOGx5b2V1ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214163),
('CXQ6H5NhRaMDnzo4SqgY57VK0aGxNnfAyObTnOq8', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOW9MNERsSzAwR0RKaktnZDFEdkRrMkZMeWRiRkF6eW1zaEg3cnpQaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214283),
('CZoTRr0IbhTsDkEAvM9HDaGV9uF4CzUpRiFTnPQP', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1dVVUFPWlVDVm1pdHgzZ2hzQnExU0Z3V1NWRFI1ZzRzTkFVQzFhViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215664),
('d989ghTNR3LmSfqqE4jomyR8G5tKOlUBDtNByBhP', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVhlc2hzb3M5dXlPYUhqNWxGNHk0RXpOenltUElDTnBwbVAwYzB5eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212723),
('dagJUqCInNGCG3HcnGpiHkokD9kgJO3O5LOaGbmK', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibUxIUzYybXZBdm9MMzhPYmVkUm5rQUZlMVp5WjJwb2VCcHpVMFBHSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215664),
('DeU33D3akydxsN3Ul3ulIxJGmgkfjkAuNl3miIp6', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUUhISUxTT0NUNG5XelpSMjhjOWEyeTNrVVZ1S29SMHF4QWhEN1hYeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218303),
('dkBwye4UhdbsBtmKoKFzF5LxQaQjV2XgShAYjKOv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidEtNdG81OWt1OWN1SWo0ODNJWUJrWGlhaWxNeWsyQ3F3Z1hWRGJtZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213203),
('dLYp9bE0iOVGxermUpd7vok89EEQBSfT9Or3rzjs', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUdvbExDaFpBS3IzZDloWHAwTFpvNHBxV2J0V1hpN1ZYZTNMaTZSayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216143),
('DmRK8NlN0yhnGYOnSK8Hve9YhqYLKsrFICG7BTvR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFdsNDNBR0Rub0oyaUo1V1M5QzZSYkRWVWd3d0VtTUpxa2M3cHNCWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212723),
('Dq9mVlykqzsBleVgcWDHD44HvoyndRX9jNEUyQlq', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidEszaDJ5WjNCSEo0dmFIVk9wSVhTOVlXbTZ5UTlJeUlQaDdtSkxTbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215062),
('ds9DT75VHt8S80mkJ9DwRBDLZTecYVkSgtnPnSUb', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaWpjYlNiaDNsYkg0aGRJNlFoYWhNWXd0NzkwaVhmQUtoY0FDNVNLeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217582),
('DxrXartbyofZ2vHXUHMdURDPkS77OiuCfC5gvc0R', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicDZjUFIzQkhoU081bjNyS1pUU1V1YmRHV3NPOVZzcFM5eWhpS2xTUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215843),
('E09BSXdRD9ktSJE11vSWD2CtserQruwgUue9sZcj', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZERTRXQ5SUxpaGp0eXk5RW91YlE3VkpCTnFPenJ2aGc4M1ZFZHdvMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214523),
('EckOlJTAqjWJrjBXudhSJx4TLWVsr6FzEPmvi2he', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia2dWWVVKd0d2NWhqbDdRQldObkNhNGRORkVwMUMyMTdJOXpvNHN3UyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213442),
('eClvQn4SzMF1xoXNNON5DkAqWan5yXbbNTnYoI8t', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2Eydmk5QjNtU3ZvTEVZRVNsOEVlRk9VdlFDWEVManZFRURpMnV3SiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215723),
('elSV3jjXuDRIqBqa1PKsiaNo1QMmASDgjGBmRiMP', NULL, '2a02:4780:2c:3::2', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiNVhHZW9BdlAxT3lYZFI1SXR1NGZmOE9WeGRVMUVRREQxZ2ZIMElUNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216787),
('EqybGTW9TrhVAC1AZQTbFcPK1jGmoutSVviqwRoJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVhkSXNoMjdGVUU4YUFsRzN2dmhLcktLYUZHdUtXWnZrUXBFbUhQVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215843),
('ETuzQvJUy0VRrS9ZPv6t0JuahaRrxoOreVjbD6oi', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYTVtMFl4cUpWaGxJaGpadEZmZUo3Z21wdjZlTDJiWW5HZkpsb0dlWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219563),
('euQkSMxQdVNQZ6Skfnao0o3W4GQuegxJc6GYI1cy', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia05WUXFIWU5hejkzYkd0VFlYYWRWSDRjT05zZ2NrQmZncjlxdnJkYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219263),
('eVooWULkGP65p5z8VSNhKNySu3DkfMZBijrcBa3v', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUkRMclJ0RW5nVVJOeHV0VEZjWWZ5Z2Y3Sk5jM0JGclcxMlR4UEZFMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214523),
('EWnEO4DNhP3ZkOyaxWudZuFLYNpW5UFnDg5ujvyj', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRENZQzgzOElMRWNhQjBuOGFtbFlUR0RKU0h2ZEFHc291RW41S0NieCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219142),
('f0C8rI58GtyKmM949gAPddPeyYWzV2PgsB5nhnS5', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRG5YdTVMNDNFM0doc3Q3T051aFB1R0ZhUFViYU5GaVdEU05NcDA3MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214703),
('FJCeVmUm8iszokY01TNyAV5feGY8z31qFxWMS2Gu', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidktCRlZmcHBuQVlYMVhBMkJzeDVtSm05bkg0WE0zblZHTzc4bGQwSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217462),
('ftGhdcWcyA5Qvoi4AT8x0B5npb2rZVZkecGyaAK9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNU9PMjZJbkhQbDBwSEM2MlpmQkdNcjFQV1FMSTFrdTZrbGxRb0cyZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212543),
('fTwr784DF3ZPsSSkSK8uM6fwz1qVMapRpMVyta9b', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaVlNdWlrRkNPclEzUm95MDkyejI2MWQ4N1RvYTFJZ2NDYklTallJUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215423),
('fwlX0rmmEUEZ84uPDFuKe2VBqSU2A9gNT4FCiOt0', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0FsSU9ub1VoanlJUWJlRHZ3eDI3QjBrYndBV1dRMzJRVm04Y0ZvUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217043),
('g5hYCEE26elS7XzszOEWQ4umV02GP67TutfICHjj', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUZkRzZReXYyTnZTVzNOOGVQVUtqT0xCZ2FWRXdwTmxwSWhWSU5NbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215542),
('gIcrEF9izQ6Y9K3ZM7M8u9gPmklQxcxhORnj1Uhj', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFRtRERkY0UwRmJmaEpubTVsbnY5M1JKZElWWFpMUm5SVFJDelA1cSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212783),
('giNgXau5PMof2Cz8OgJslTcaLqhJ8FY5agzIF5OF', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1pwdlA0MmhxN29xek5vVldFZVhSQUZlREZ0SWlqUXpwRzhtMnFCNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215543),
('gUgcNsuQyjlWKeuEoHTrPH7d1QvnhQR9oBjpsSKJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1M1NjN1WW5xRlJzNm9BcVc0aVFyR2o1ekk0ajZZODN1U3ZvbkxYQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214763),
('GwXtPqOPBugLXtqCFJfthTJ1p5IvFnOBvELw8Sy9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDB2RUViQWo2QVRVZnp5bW9lRjZyaUlINjY3SWdFMDRhS3NPWU9lViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219503),
('GzeWVelHnM3cjTgk5QEuTuzxmFFJG47d2dXbDbaT', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVUxOXhEcjE4YmtlM2lRVWlmZlZpZWw0Z0ZhZjEwQ2ZJdWYwU1ZqYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216203),
('H0SPipX1jW3yvQskM4uawfPQyLtj7VCxnDPcKGB6', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0ZIT0R5MXhaWXdmQWFmd0tNQXhNWkhBVTNyN3hJdFB4bXhVMGxUMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219623),
('H3M9ukWSmqIskYARMFyX8iOLh3ISn9CnYwkf7586', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWtpa0JpNzRkc1R5Uk5iUElmNmh6ZHY2QTVTWEhZZ1B0SU5hS3h5byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216743),
('haCCbtT1hyYLHOqXC3wR37L46Y7uUYdQJzuADv0N', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0dsTEpXMHdUTUlqOHVlRlFmTEU4WnZxWk5rWTkzMk1ybFc4M3pCNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217943),
('hDdFB5STzTGrQSG6ufsST3frKzIxeHiv8v8Ci7OX', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV3BlRW11MEpjTDl5NjBCSjVEb2duSXRNd0tXVTE4bHhVR09FMjBmUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213803),
('HKPr18WaDHp8qx2FWMmaHsUh3hXrkYKnmy6nKZ3a', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzZOMlZGa0VqSE5VcG5KY1VWMEtrcVdBUWlHcEN3VWdiWnFWS1FETiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217343),
('HsCKAK6QMrhzrv8AEqlrQlgmutUlAMQ3ympoUEWE', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXJwOWpUcVlvWjBCMFdUOWt0Z2dhbTJOT0RyT0J5SWlYczA1OTRtOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217283),
('hSyRL55IXIWvZTflBK1RegBuo5ZWRpQL4EyenMzl', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicW1STVNoN1dkQko2YXJhbnZrTzEzTmRlNWdPWVZibWhhWFhhaUlveSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214822),
('HWldLYWSIKzJJ4Db6dn1c6u5oVPLKMqH7gS15cxJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXlRZFN3S1ZLbzZGaFFCUjUwcU0wNXh2THY5QUE0WmlqSHN5dXN4NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216442),
('hZ21rkNrGjcuCkmqN8m3nyVzZFbWchV5fP2L17au', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHhPdEU5WXVwWnpyWHBvQXluMmJva0RrY3F0S2s5U2JxVVVxWE1KMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212962),
('iaH7JssWMyH7LSyqWUioKyzlChPfrPrbbUylYtmn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVVSdDVvbDBsY09hT08yYWR0d1RDNzJMeHE5VXJOOGRoejIxS3paeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218243),
('IAzveYpscKA4DQSrvPaweclhJ1sdeoEmY9d5kgmX', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1BzYXM1dW1JRnJmYlphdEdRYkxMODNES0U4RmJGUWlTeGp2UDVxZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219022),
('ibY8jjYoYDyO1PcyEipL3DOeAp4EnUbPSDjnqopJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjA4cEVGb2V5amg1Y3M5M1dIQWtNaXB6cEU4emFqWmZxMWV4b1c5YyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213863),
('iHSR1c0Uh6QoopDfnr4Zwat52Lt9oZekIe6O5evS', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkQ3bUhocHl3aE1abGRXSVRmMXd4V0U5dXJhYlNPQ09peGdvMVBQQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217522),
('IISUnpejojW3Q1pKbI5e7yTkoP4T0HVuKzV25mbW', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidnI0SGdwQXh3NzVFRXBJZnRPZ1pOQ08zb05lV01nUzlWVkZFMzhDeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219622),
('IS6SgLgQQdmGkNnMjaSXemUbrG9AEfTX3POjpQNL', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaThNeGRWaHhoRUI4Nm5pdUE1enVEMFk3TmZ5SnVybG5zRnYyQjB1RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213622),
('iYg2liuAYyftQXEUKYBOjMir1fgRIzr8CTXH7pof', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTcxc1dQOTNtNnM2SHI1c1pVdUc5emVxVTJHaFZxNHFjbWwxTUJyTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216503),
('jA9BJF0dRr6U9yE7GoUZ20eoqrBnrZf3m2TjEfTT', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVA0YUY4R0o1SWxReVI4aWowMUZibk1rMG9sY1dpSEdaZVlPUnk1UiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214703),
('jbteplgQjCTX9FjRoSojE56hJHYHhBG3BEJyxD68', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFZYc0JwUm1kcVh5MWtqUXlVRXd1SlVreXVIYkpMRE9yalVkT3NFMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216325),
('JgQ0yWusmKP4VYOLWAN80WdplRGKRMoOx6bZeRjx', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWphNjY3VEFTVk9tUzBNVFI5OExQUDlxWU1uMmNIRHFXMmE5dldCbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219023),
('JtAPHf2ND7ULcIxfkGZ9NShWlYNNZ297wVlMeAOy', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXZVRkF4VzZaeGJ2WnNoR2l5eFVtd1pXZkVhVWFDM1hhUVA5Tnd2ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215062),
('JYLktG543zSSjjr2kueoHHXecBnPHGor2jSyQMAC', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3pDd09TdWdub2lDR3czSnZjdW5ET2M4eTJsOHZ4RjYyY0xSZnE2eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216324),
('k0C6UcN1K79jfIvZma9tw6RczavZAP7r2OoQpZBG', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFJiTXFrc1hXd3lOWEcxd3NWbDhPSkRaQ0w5d0VWZzB5T013QWx2QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218423),
('k1mNtOf4gWZFq9XTAnS4VMsxPPcep3r9KBl0MKBt', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOTFrSFVhdkNyaW43UDcxQ2tzSFpuOThmTkppMVp5a3d3eExRR3ZLVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215303),
('KC2ksBI6yTicdH4iRaSssZL4pUTPdW3Ux9H3xlyB', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlFUZUZKYTdPTnJFVUU0U3JvOVZueEwwVVNZUnN5eUFNUERKbnE4eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214582),
('kdGa2JdUoWcrbjrRmcZHZS3qEUXVPuDoMVCPGS4W', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2NnYVFzU0hKTGc1bFRlR0FpeVFXMHd2cGZtUTZnbGpROVZFeHVkZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219503),
('kHkoriclhFKpLOTqrlIje2yxZY7M2PdALHjDGEWd', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVlOOTV5WnZNa2VETGlTTGpvM2JIdEVEb290N25XVUtPZWkzY044dyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213503),
('Kiv02Uh67mdawyAJROrmmDCD4wDMTU8JHYuyPbeV', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXA4YUpCbUVTT0ZCM2RiZTZ3bVZRcHdMa2pLdDZsc2M2QmdPeTBwdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213323),
('koauy0lgk67i6hjKZfUDC1qumbQ9Mfw1AtLO7h3s', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidlptN2lPcDh1eEJaYnZjZ3VpVm5ieDBmZVprWmhVcklmNVRKaFI5QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216562),
('kOqFuiJfZIdM0LxXCARQIWIasNAaz6dNurJKqaFR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiakUxN09DYURObW1HNTlha051cnNxekdiMFA3TW5wODdvWXIyYnVBUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214882),
('l2M8iYasjZU3BjxOd17QEVNTcp4FqdU9RFXvjgeT', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUtQT0J4SlNzaDRqN1Z5cDJLV1A2VWkxdDNreWJ2dTl6MWtrS3BtNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218963),
('lAxhTOrdCcyVqjg6meKCxLJoEWHfwtnAfOUHDrEA', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZnVGMTFxUUtTaGx6eVNQYmxBUndzUmNyVElJbFRkdkh2cHBvRWs0NiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215783),
('lbriEQpwaQYoqvNie3iNonf9AnI9NGb8259eF2jP', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkZxQ0IxbjJBa01rRjdRSU54MzVYME51UG1RZTA3RkZGaVdzYzI0ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219323),
('lFIZ7qMShQyZfnwkfcipn07ynrnZp58q2iRUnVFj', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUE5UTGZ2azZOSUhHbXoyVjdDUGxZdHR4TFNQMlZGSUR0Q211RWtPeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214822),
('llU9m2ny7dC42eFMwUTLIa361iJClKS1LxZQ9b1c', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjBoZTVudmxoSFNWcEVNTmIwaUpaZFE0ZERsazJiWEM3SkNiZjZ3TyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215303),
('LMLNxs5GXD7KWnQE2SlZClolygnMy9DKB9esqROx', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXoyaHI3bmUwV05hZmZobFZkV284RkxrSENNa09QSFQ0NFlGYWVqTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215422),
('LMy3i6FTdUkmmokHx0DGP4Hs2NFfJWOzlgfjKGek', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidmtjdW81bVpGY292eTBONFJySWl2ZXc0MW1FZTY0SmV1SnV2bkFsdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213682),
('LnwNJn1A3SPvXSpMqcAJl2X0gA28UZ96j8v2nx7n', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMm9YeDB3ZVVIMkRZd3BwUllYYkU2ekM2WHlocG9ic2hXTEVIZEVsaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215362),
('Ls1X9X8ga7l74djwREoDXmNCQtC1wn1H1sdit2yO', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVno1UmZMakU5Z1J5czlSVjRpWkxTaktqWXJycXBPWkFNUTByTkxVNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218003),
('LZDYUlmZDg4TyYqNIIio10jWN4hJSbpVxAXGhQWR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjZoQVJmZjNocU9jVjQ4aFJ0S3NocFhmRmtXbGk4M0pLNkY0MUx1MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213803),
('LzNwhab7SFbU3M86qBqkPVJnZrBMG4SbJ9oYVs2T', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVTQyR3VqcGJpN2pVeHEwNEo5Mk1NMnJzdWtxdnFYN2l5dVVzYUk2WSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218603),
('m2ScW6z6o98AmgrzuwfN0KqOTepwN2pEUfa4DrVo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3dnNDZNM0luUWh6YzVnUm1zbFU5SlNQV3ZiaE1NWldJU0s0VjJyVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214403),
('MkwDP9kGomyJWGXBfc3rIy3hnecCNMX3Z5Xg87Er', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWxUS2hqSmFzaTdUYW1BTVlFd2FnS1J4RUUzNjh6cmlYRzRMN1ZiUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212903),
('mNdUZxmipHxk5m5qINTgVLa8PEsioc691CcTTCtJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTktPcDZQRmF3MmJ6RHB3OTRlT3kzM0tWSXpETDdsUHNHaktpTVJCMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216442),
('mrQlQBFXdl4Nfky5q1H3xsjit2Zee9Va1sCDp5xN', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmhQbVFtZTZOU2NrTmVJVjk5T1V3N0JVVDlzajNIWFhtcGxDc01FaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216803),
('mWETxeraWsLSJEFSrWMrB1vO7PlQnTPphT3BQLSi', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicGcydEowR2Zzb2NmYTAxc3g3RWZuYmswUmtwM09sWTR4V3Y1ZmJUVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217762),
('MzcYe1ZUbaUhLsd1yzUBXUMSQHdaUjOODlEThSRn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUNsempzYmxiSmhZUmgzV0ZBWHpWNWRaUjNLWHpjanBya05xRFdtMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217343),
('n4cwJQ4CNa9DiodUBWDdBhtz3FxnE7Jng5BI1s9e', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEQwOWtFaHNJSzAyQlZQUTg1NlI4MUo3ejM5M1RxVzRYa0g1SU5KbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213322),
('NBu4GDZeZaKzUTQAHHHzRZD77jz8uVayAo7lAlgT', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0FrS28wVWdWZW1qQ25JMEIzcjNEODBuak1ZY3dQMTUyMDBUQzEwUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217103),
('NcdiK6pe8JWxWduRGTMndRL8qJWE80yKtccfIlOn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0VEMjZYd1J0RXpVMXI2dVhOeG1JYlVKYVd3aXBteXIyM09kQ0FaYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212663),
('Nde0XpWVOK4Z4ny2AH0uMg1bPEgUZRWHUcwDBstb', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMGRDSmdFOUh2Q3kwRkFrUVhkbkMxRExxM3N4Wk5GRmhMQlpkckZjWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213082),
('nEixncKGYTxQMl6PqaNbuWolZtvHRoe2RBVMcea9', NULL, '170.52.69.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFNhQ2syQ05kd29XUjBOOUc1QlNkM1hScXpUNUxsTEVTZHN6YUpVMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771219682),
('nEz74DU0r1OI03xX7ga1fx7NKdxGsNrjRXXNweUQ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMTBwY3NiUVdIOFFTOGx6ckRqblZnVnhmUTFZVFc4bFdUSVBVNWk1RyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213383),
('nhkKn12G6Pi3M4gDKtvWRZHFBYexl0xLnQzYxbw7', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVRKazVVSW03RjFSUzVnaDNYTDFkUm15RDkzZjEwSVNZakJiYXI1SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218483),
('nlEq590Lpg0jrrUf2Emne3y5veIwSH16x8xVcc1h', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnRHM3I5RXExSXFia2k5R0xFVlRERWQwSXFvaldKZllYcnVsTFJtSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213742),
('NN3T7BIthyfn1lUDDSiUnQ8IuhOxAEjT3iAOGNNn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2N4ZHhEZHBwZ3ZPM2pySmFGRU43QXN4QjhiTktvT2RUNm5CUlFUeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217163),
('nNzQteog2UTNosMeVyWClp0Hsbl5IzQp3O2MZhAp', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXVMVXl3NkROTmYyZ3VhMkw3cXBpODBVTVg5ZGlSVTI0RmZOSkJBYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214223),
('nQj4IrXrk14d8rY1gzmwMgWAbZk2zN5KO6BHtJtg', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidmlZSzVwUnVsRFQxcmRwallvbFNpbDQ0cHZQNEtsZE5pY01HWEpWMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217642),
('NUCqbWEvJUALPwa1Y4ptI5ozvCCh4PXc9O8vgQnd', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWtwSWxIa2VFaDdISWhLWHcyQ3NDNEJmT24yelZmelRLNDJaaVdRdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219443),
('nW2Jv3Ea3bGJE2VRNRSGz7l31GLarLcxvPpPAk5Z', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVlU2Nlc5WVZsQ3ZWOHczMWc4TnNUYVphVmR4MG1oYTU2MFN1OHBIUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216982),
('ny0eYWd8tYjISH0ufJndPJrMaYl6lpU85BIySYzS', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFNMUGI5VFJQWlliT0JTU0NwR3owaUZweUJZTmhpTDU3OENwVnE1diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213142),
('o1jzoNyy6gMjMCE6aAY7QBz2CGCzpz5H2Q5vWNjd', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUcxYzc1S0FOWjFoTVFzRWxHTFhha0k3YWlZRFM3TE8xWVlEZGpJNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218843),
('O5lHi64YCJ9LvjsE20RGzMQ8bBIOFWOExgvx3Hdn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDlabUJ3NXFGdXJ1aVpDVWNhRm9sakxpOVZtQzJpYlNPcWR3dnJKayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212842),
('Ob6w3PfzjMnRgsjwKrORiU7X3KykkChSpLsCKvql', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibUg0RUx5aVNyNWtKbE93TWluajZ2ZGFSV1BvMkZIUWI5WGFwOHZQNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217222),
('ofk25NuqGPLX25xtPLl9wrFrMsJJSU2c7JQc9IIJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiemxiWXlRdkFZOUVkcHowVFkyS0M2VnR2akZ4ZmppSm4yc0lSbkNjViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217762),
('OFk98UMeVEkFEsLT4MdyVI5SG69StdDizSdnxDmJ', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2lZWlB1SXNLWnVMVE0xRFFNM1pINEFFQjVmR0JrRXBMdDJqTDNHdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215902),
('OjtfFk1NXYMvA6gAApAZ5MadmqqXDVMO3HgWn4TA', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHdWWE9yVDljV0pVbjZGaWhza01QOTdYU3lXWHZIWVowdzNEdWpneCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214462),
('ovgZRFc69lul8kd9wph2x5TlKhE0DADqEwSz7p8f', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVkx0clNaTktHYXVQTzlHQnY3bDFhNEJZdnBucExrVVllNjFPcGxENSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216683),
('OwjeZEjwnMiKgWFcaavTqB7InPcGDmqfi7DYXpfc', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYklTR056ME5KVnJuYVBDR0gzRm4wTVNORDBpbVhWcjZUWHZkeEplVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216503),
('oYh42lfN6f89LLd9PkgwK9TUDqQmg4zbvWkbwY4P', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzZyMzZLdkJpWHVLWUJuQTZXVnVmMHo1b0JzWXVxWGN0MzBXSVZ3cCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215122),
('oZ9fd6qwdSScbFygZjoI0v1iXhcHF4GOkZlPrgXd', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHd0ckY2T0FiYXdDQ2l1WG45cU5RTTJRdkJ3QkJrZE5BUTJtQm9CSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213143),
('PBxkeMdnQbVTNR2sZ0Mu0ZwINwB9crkp8bQhiC8u', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZzVaVVhlZG5IU0taSEx5YkpyY3U0aUxTeTdabjRFOEx4WG9Ra0RERiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215603),
('PkKYiKY2LoqvVqn3BWW5MiyLSj3Ra8fbroHFohT6', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXQ4OEpRYVJmUnVkcDIzU05PR2dnaElxV3hNdGZOZUQ4d1FRSnAxNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215902),
('PNb42PgmbIUO89ShxWPtepAzu7vAZnoROKVEhlyR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicG5Hd1RpY3oxSVhRdHpscHY3a3dQa1ZNUzE4MXdlVGM5NnlzU0N2MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219442),
('PQFIZafDf3p1SUZEf1eG1yan5HYaME5il66QvWyo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXdjalNyVkRaZmFuSzljV0l5OVJza3hTYUxremZqY2VQbDJMYllPZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214342),
('prsvq1LnTEFAz1p1uLWhIjya2aPmuwsOiRYhJRWv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2dHT2xQN3NPSk9sRjVoRjJqOEpZaUVjR3E5VEhpQUtnY1pWaHJtdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219388),
('PrT4kHvzFSHEQ5tx3cXcBdEjags9HChDNiUG3I1U', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXM4V2tUYTRGSXViMjRvS1BBUXFnalpUbWk0aUZ0WGRCWFVHUE45MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215723),
('pwWgQXkQ97QAOJkVvSf2wolI9iIstKoKkuIJDxW4', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXY5WEVEMTFwbFFNa1hnZkdDYUpSenllV1EyaUxKT1NReGdHbXpLZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212782),
('pzto27dGMmGiNY6EVyXpQyXMgh0uEE1hyc5QMy5u', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibExodmJFaVdkaFpCRTV3T0Z3N3NHUldtaWFkU3haRWtYaEY1YzNteCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213983),
('Q4lwud3drOqrwbXWJhmNoZsQs2eRsrwOyj3z9KVf', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidjhuRHNZQjBnNEZaZ3ZlU0pyQVNBZjVGVjl5ZDVJVDNPYUpjdmdyQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219203),
('qelinBmv66ECBRjZbCYFPepJvjIoc5kxMIydp8gF', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidDJZZTVFVjdqYjFlWElpUVUxMExVbmEzR2c0TlpvV3RHMlZYUG1nVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216022),
('QoPk85prJRHEkSf9BleqV9iEIRy1n6FWuXQSw0LI', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVHhjQ2d3d2VZT1JtYmNiSmVzUWNMUEJ2QUIzZmdvWlhHaElNZGFNeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215603),
('Qoq353QObtu1pAJthtVkkTRmjVOUZQ2uq2vR04v1', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEplSFo4ZWlZeTJVSDZxbXloQ0FteVFWRDNzMldNSG5SamtVT1pXeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218783),
('qUwcfPjdRiw4aNpa6aIgi4CXQZs1bbrVaZh3wdEK', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmhUQ290cEkzR3JQT1V0YnBBTDdVTjBNeG40Sjk0aVhrOVpOVFlZTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217043),
('QXYUlbCLNycmvQtbiEm1CswBLBLl4l7NGjRHvtiS', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1hFV2RGRTVaYmhsbVNnQW9rTE1PQ0NHbkViMXhyVUN3djc0dGF3eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771212483),
('rgZFey1oZ3PJx7hLWBPde1FjoMc3KYnTOnwVFbKI', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamM5Y1NZYTcySEhIWVBOUEdZV2NvUHZscG9oR3lEeFJERXFUSXhlMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219263),
('RiMCnUCxDrCQ4hqHkAebY9lx1mhVj1W3DlNYwqJC', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3B6bEk3bXdvbndYb3dwNlgwbXlvOThqdXk3WGRqSkt1UTRJcmxsSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213563),
('RKHbm0dotdtvNyjp85w8audxnwWYJqX9nwrh6IOB', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnFwUnhDazI5OGZkZTZJZHJQQlJyalY1SUkzaFJRRjI4U3FWRzRMcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216623),
('RKngZWSFo3ib4qhoSuZNeK9wQQaMRBgJhI5EzT5i', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamIxT2czaFB4NW1zRHozb01hUmhtYlBRbElkVzhzTlpsWnB5MDBtViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217462),
('rUCm8olON69Zkc4H7YzWuINtv1nhsmG82Ysz5BWv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3ZyNldmZVg5eUlhYlNEMG9XTmdKTmx0bGRIalZ2MWl4WWRnUnZBRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217822),
('rZf7YE1Ob3MaQHol47eqexvEc8IUJH6vh9AjVT0g', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDJON1o0azA5bzNTdkFjN01hUVNsMHd5MWFNQXJybUpTNjFBb1NZaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219682),
('s6ohtHzKYbuVrACVMkyuFUbGV2cgTzvbQvtMqPAB', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2xweFVjWmIxOHNBYThXOGVVaFVUV0t3N0J2M243a3dGcE5lUDIyaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218182),
('siNNqkQ1J8EccR5RgIFjo8vGMWO9WdZVjhg2GHFo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialhCN0ZFUGxtTjJTd2NjYVFVdDI0YXZKOFdGbGJiellWaEM3UGtHYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216382),
('t5aJLA7x3vGBXk8JTEg6uOFbcySA2vaUS8ovCFRv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTdQRGs2MVVkcU9UcGNmMUxUMU8yNkhJbFFkMGJ5NVd1S3JUTmlLMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771219323),
('TAueNoS6itWSo23SdSH93UHhFeVMNWAweE47IemC', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSklxZ09qV0hDSVZUOVJIbElZZ1JiYkV0V1IxRWRuREV4Y2FkQkRWUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213262),
('tcXRmNAjbsnqj5Pg9FV2NHk12a7bT9KmfmsavWLs', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVlQMkU5dHhJbm1SSmZJYUlEc2FFRXNhZDQ3T1dNQmVQT0R0cU1kRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214103),
('tGR3tf5BYJEpQmTzxXwCjK6scrUtJQLkMunsfxtK', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlFpb0NGc3E4Z3ZGYU9mMW5Kb2VsWUgzR2tMMjJHY0V4SFlwR1V6SSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218963),
('tIL2coNVy7e3ewzij2HGiyMGl3R5NtmHFmGMn9Vi', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1djMXcxRU9BT0JreEdhYWVIQVBMd3dlYzVMaFI5d1I0Slc4WGFyWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215242),
('TjrfF7JaN0Mq7Qjtw4Dmt9LukFCJ97BECUbl61Oe', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoickpHR0lwVm5ORFRlZDlUUWFyN1hNb0pNRjcxUkxDb21LOG5reW4wTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214942),
('tn62tfA9bVyRInCWG9oMrHFOdCCIWRjFYL5bT7Xi', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0xRUjZSbHFJc2laNU5pTXYyMHdSeGt3UG9Pc2lUWE1tUFBiSlpZeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212604),
('Tv4ZAOmTE5SZkN0bvENKn8ohtu4DEB0rNP34pcDn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSE85RWFsWDBtbmM1Qkl4cVd0TUFxN05MOEVVMkJTY1Y1S0hZcDVlNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216022),
('tXWs8hBiTLqIQBHGMmvxrZRAugtBasIAfXJhuX78', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMjVQd1dKSzhSRDJIemZ2R3lYR2xOUmM0cXA4cEZ2SE9uM0k3WG4zSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215183),
('u6kQhpx3cMe2YUbX92ddslEfjt7B8oCcK2I65bDE', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSDlqM3JPNXJMSnAyYnYxYkxPMldpdkxwYW42MXE2elN5N1hvU05JbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215783),
('ucSkxFZjVOxmVfA5tGaoSXstypraVXeVzm5JoJ8e', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHVpZlA3YkRzZGgxME5KQ0w4MFB4U0V0T1ZlcVJSSTBxYmJRR1ZHQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217883),
('uNxuSVRwSmeAUPIRrTwm9izNYr3X0wPDqWEtx6nq', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic09HemtKM1IwNXVqZkxGdGNpd1ByR1VRSDZ3OERJNlZKbWFvOUtkVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213983),
('V9tE7SHdv9yMRsCuvWcQftpnTSpFXWspHcp7Xwyr', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1F5TW9MS3JYVWI1bk9sdkpKa05xV1RXVk5MekZ3WUtrZTQ0UEc2YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213442),
('v9zXk4ER5BEflSiXJxlFJvNAwqUL1o8VS3lm9OTr', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEFpVGpZS0hxYld0M1lmTVZTU3ZjWEppMjJ5SVo0cXVLOG93YjZRRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215363),
('VbvKgq0BHFjVcN7L6CC6oLnnbFadsWk9FfRcYcyU', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibDl5YWpFdHJNQ2xzS055ZVlROVZhOUZtTWhKaHFyZkF2NzNOejNEdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216982),
('VcxZGxpXZGlJC984Ilum3YwIP09tfPbAfJOxQCgv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQUt4UjVaWGJwRjBySXhPb1BKamhSaEpCcVduU0NBV055c3JYcHJKWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217702),
('vivSDheeckeXBRitqMMfvKjCW7SiAqPP3oUL5uhH', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielpoN1N2Z3pXUUg2OWhEdDUyVEs1Sk90WEw5SUE4T0duNnZKa0JLTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771213262),
('Vj3AKJ8BFn9qgsWRco0AMnOVPfnuRBDWmeRAv4Wf', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWV3cjRFS3V2ZDdONHc2OTNxdjJFOHpRTmxzeHF0cmNER2FwTG5adiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217403),
('VPf79OGMK5tJgyw4qGM5FbdCBp05PpIewXRs3GMc', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVlE1NER5R2JsUVdOMjA1QmprSkF0MG1EUnVZamFYQVlGYzA5cmJleCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218603),
('vuZRAGvtgN8wDUS5r8XCiGkgtUQQOyrTp5DtAexH', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTdWV3N0V1NJdkk0MjVQR21qMG85TWhHbXQ5d1NOYXpCUVA5OGhSWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213743),
('W7H5NvkzVAXTKQPlgw6dArngYgAvjD96EzdQkro3', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN1NuTkthZkdDMGM0dWp6aVEwSWNMMlN4Q3pFQUlIRTVRbEJGZjMwVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218543),
('WAIStBiDkJaOifyoYKzuqbDnODZyGUd8D2Tk1dLB', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXp4dHNFanljWVRoUVhMbFFmbWNUVWk1Rmt2bWt5dmNLdFNrc0g3ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218303),
('WC31PUmLUIXiUB9TkaCQoDJm4odZNL8XBq5a0Lpk', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiblROaWlJSXlqY1JkcWxlRkN0Q0lzbVMySkJwTVNGaXp4OXZHUk51biI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212843),
('wH2Qwq1FIR6PqsuZFiZr9X6Gu3UtfCs3J4tpLeBR', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUt4R0NIREhhajVRRnJZUGNzSm5HQnRDTjBWRXlGYnEzNUxMZU9lQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771212543),
('whym5dvS9mqbBbzXPRbWo8oTyhN6FEkfD0LHQzJ9', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmc5eUU0M1hWZGNublFvbnhkbGtaZmJweFJoZzhyOThSU0tDdGFUSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216922),
('wKJj1qYOETW7oe6snI0bcK2DHfZP80WgOF6X2Svv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia3dWcjZPTEE0VDZnV2pFNmdRWDR3Z1B4VkVKRVZtQUo0cEVmM3dBOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216683),
('WMixkUKGRAl4Z2TQhsm5ACEkj7HGGL7dGWYBUGjq', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmdCbkFUTkFucTFBWFpTSFRkT1BqWTFUVEh3ck1TZDhDUXNLQmlkVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214103),
('WrExhar355YOtxrv9ZAYfc7xxDEcJPuubVwSjZI5', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTnVkeEhIaHE3V3VpQjRybWdNWDBWdDZIcFlUbk5NRFFVQ0ZNREhIUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218363),
('WsUx6UH15Lqp6hl1QEb3zZPZZCnaPwrbmfU8p2Db', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic3V6dnN4bFc0eWlpYU5pUU96Sk9seFc5TmFiWUN0Y3ZTV3NybFZPeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218063),
('Wxv0zsrWBP728cZlnfWwdUDdcBJKTH230sNXUaXO', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVNLTVNnbFIwVTFKV2ZHS1JGRUtWM3NMRmlnRmI5RkJCMzM1UkJWUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214763),
('xbq12nmOF4xw2VRpNqp659bT8ESkHUG7Sn7FsLk3', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkZ3bjE4VG5iMmQ2OXJuY3FYTVhGT1BPQlB3SHlvRXJJM1B6eUNwQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214642),
('xGjRGCRopBNZumfBJ7tks6qSSZmPQUiN0Wnt4krv', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEw3TkNnVzJrOG9TWWpmcHR5cGE1ZEFlMlNkNlMzb2Z5aW5iVnFRSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216803),
('XXGMtabytgz5LxjltMXO001ZUhDezGiC9ofXc1XY', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1NuY0dLb0ZQV3luc0FadVFEVGpVRlJ6NFgzZVhwMnQ0VHpidGE1dyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218363),
('y5X65tWLfHUFCnUX78JoE7TxoMZF84niCLAzTzIr', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUFWRlRscERUMGZ2YlZkWnpUOURJTm9YSlN6WTZIOWtFejVqMHhZdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216203),
('yaJEXRqco7oind6H01ySKrh8Cg9bh6QnETjTMQh0', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaExWNTBHZ20yMlpjYkdqN0hGV3NjdW9scmRLNVF3b3VFcklxS25jVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216863),
('yckplgGaCXVAvr5fegDGBb0EsKslcLZK1GNKFbRu', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmJYTTBiWnNWakIyWGVvNkREZTUyaHZETXlCeWpJRFpqcVB1WWRzTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214042),
('YGiFbFEjk95ENppgb80u4tqDa8DMZ0iE6sHhBUon', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXlQcUZ3Sm50UHpBV0ZmZUg0dXB5a01Xb1NFNDNQWmluM2xNUklWQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218842),
('yhMwL8n7HDtebOEUdMfex829OT1xQLQL1oyDk4Bx', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXNpMkZIeThOQkNGN0VPVDVmM2M5RmRHZTBzeXVsMUtjb084Ym1IdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771216863),
('yKlt0Uzh9ehHrcM0wwKasmSRbSxbezeKou2JhBEN', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib25OdXhyTm5YR1JveDYyR2hlRUQwU2M5S0pUd0ZjN21kZnJWVzVKUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771214283),
('YR1HBcGnnetoRe5QSkWSYYZlrllhk6CFbFazIoC5', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYjlOMFB1M1pvVTZ0ejBpQkZadkRxUUpnbEVxTFdyRzRtUU9DQW12bCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771216623),
('YRV1TnhOFsRi1SZ692yeFb6qgHcBRvTXx37OpycW', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjlTSlRyY2hBSVk2Um1VY05Ec2VjMmdsazVHS2pqVXpmOEFHUHUwUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771217942),
('z4AS1OAHVggUEJO9HRhGz1DAg7DoNjEUHfQW8pLY', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYXB2bHRNYnhwSVNYS2F3VjZEU29vcW9XbFdwcjdKSGk3ZHVBY25haCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771215486),
('z98gsxQqfWaaf7n3H1ttmlpC0FNSsyocdRMaXLCV', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajlBWDBySVdXUUNwT2JrR3NPaWNsQzNqWm5jNDFwMTlCbEpNNm1ucSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771213203),
('Zb1I4psbt6cZBgzLoahYFdh27PhCHqqgL73TachY', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUo1TTFqaEpZTkhwZFNucXg2Rm5WalY1ZzhQQWFLNWJJdFQxYTFPQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771217883),
('zF1eDR8Gk7DtpVKaKHxoakuSYJmxSGyY5u47quKI', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXFjRXllVlpXT0RVWVRQb28xMklBWVh5enRldUtxa2VlVE83N3B3ZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771215963),
('ZFZ0n3cBIdGhIWka5IrP1Hpudcr9C1P6bmyFDrTo', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZktzVlFFUkF3TGJlbDc2ZlVaSTdpbEY2MFpwcFVCRGVpa25ITEdnYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771218723),
('zGPz4TpGmkwMMyU2yyeejgxOEh69C9rZcjwYxHZn', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib25jQTJUcmpJSU05UTQ0bDJaMDd5Z3ZxNmNGUUVJMU5nbjYwMFRRZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771214462),
('Zhcwu8YY4YMSZTuIp0SF9M45HJuUZlZVpjZnqJb0', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0hNamlxNE9KT2pqSGVnMmFUOEpZWWxoajRBa0VOQk43T0EwVHRzbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218782),
('zSkJYffBaK5xqYIJqf7r3Ys7wyPmf4cQhT2dnfFA', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHhXWXdRdXVvNzlGbXVtRkR0dzV1MldJSHhUcGdyOVBhcEJoV2xudCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi9iYWNrdXAvMDkxNDZhY2RjYzI0NzljNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771219564),
('ZwRN4jVVy1MILBOxVgmMS5iGfFJCX0f14SMxe6Q1', NULL, '2a02:4780:2b:1234::52', 'Wget/1.21.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTAyckJVclpXaFUxamRqZWxZUkZnQVJxMlVwakVldzN5SHBmbGJkQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHBzOi8vdGltZXRyYWNrLmJyYWluYW5kYm9sdC5jb20vY3Jvbi90ZXN0LXJlbWluZGVycy8wOTE0NmFjZGNjMjQ3OWM0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771218482);
UNLOCK TABLES;

-- ==================================================
-- Table: settings
-- ==================================================

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: settings
LOCK TABLES `settings` WRITE;
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'invoice_company_name', 'NAVJOT SINGH', '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(2, 'invoice_company_address', '19 Grand River Ave
Brantford, ON, N3T4W8, CA', '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(3, 'invoice_tax_number', 'RT0001764382552', '2025-11-07 07:47:22', '2025-11-08 06:21:17'),
(4, 'payment_etransfer_email', 'ns949405@gmail.com', '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(5, 'payment_bank_info', 'WealthSimple
  Transit number: 00001
   Institution number: 703
   Account: 36126969
   Account name: NAVJOT SINGH', '2025-11-07 07:47:22', '2025-11-14 02:40:46'),
(6, 'payment_instructions', NULL, '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(7, 'email_mailer', 'smtp', '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(8, 'email_smtp_host', 'smtp.hostinger.com', '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(9, 'email_smtp_port', 465, '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(10, 'email_smtp_username', 'noreply@brainandbolt.com', '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(11, 'email_smtp_password', '8]xjp@@V', '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(12, 'email_smtp_encryption', 'ssl', '2025-11-07 07:47:22', '2025-11-15 05:11:26'),
(13, 'email_from_address', 'noreply@brainandbolt.com', '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(14, 'email_from_name', 'Freelancer Time Tracker', '2025-11-07 07:47:22', '2025-11-07 07:47:22'),
(15, 'stripe_enabled', 1, '2025-11-17 01:35:00', '2025-11-17 01:35:00'),
(16, 'stripe_publishable_key', 'pk_live_51J7E4oK04LWbovV9FP88DCAXWAGH06StEWufz882ymmpnkvfuJQVV3hzKxvktIKQRZEXsQuSi1RWcBGpd9jjUaAO00D8NX3WxP', '2025-11-17 01:35:00', '2026-01-02 02:23:59'),
(17, 'stripe_secret_key', 'eyJpdiI6Ik5qQWRibmFpTlhlcm1icHZaeTlkSHc9PSIsInZhbHVlIjoiWHBCckxONG1JdmpFYlFEVUwvMGJJbWdPYjZDcXZwenF4UmpBWFNaTlNjcm5lMzlGdEZKOGZ1VEJ2Y0dnWklvYkNveG5MVTZTRlUwamRURUZlTFQ1RUw1elM5VXZjZnJhSm1JV3lrOFB0ejlMZVkwMDJneGEvUDdGRUV5czlDYXpLSlZhL1BkWXZ2amd2b250UXYyV252TFhsMVk1R01HY1huU3Mwa1MvSWJNPSIsIm1hYyI6ImEzNTRkZmMxOTg0ZTZjN2RjMGI3YmFkMTk3OWEwZjlhNGQzN2IzNjQzZDkxNWM3Y2JiOGZmZTRkYzhlMzlhYmYiLCJ0YWciOiIifQ==', '2025-11-17 01:35:00', '2026-01-02 02:23:59'),
(18, 'stripe_product_id', 'prod_TiTj2DAMotqTP8', '2025-11-17 01:36:55', '2026-01-02 02:27:48');
UNLOCK TABLES;

-- ==================================================
-- Table: time_logs
-- ==================================================

DROP TABLE IF EXISTS `time_logs`;
CREATE TABLE `time_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(191) NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `clock_in` timestamp NOT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `total_minutes` int(11) DEFAULT NULL,
  `work_description` text DEFAULT NULL,
  `project_name` varchar(191) DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `time_logs_session_id_unique` (`session_id`),
  KEY `time_logs_status_clock_in_index` (`status`,`clock_in`),
  KEY `time_logs_clock_in_index` (`clock_in`),
  KEY `time_logs_session_id_index` (`session_id`),
  KEY `time_logs_project_id_index` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table: time_logs
LOCK TABLES `time_logs` WRITE;
INSERT INTO `time_logs` (`id`, `session_id`, `project_id`, `status`, `clock_in`, `clock_out`, `total_minutes`, `work_description`, `project_name`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 'session_1747953000_686227eab2d8f', 3, 'completed', '2025-05-23 02:30:00', '2025-05-23 04:00:00', 90, 'Re-arranging folder structure for Controllers and models', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:10', '2025-06-30 10:00:10'),
(2, 'session_1748032200_686227eb3a759', 3, 'completed', '2025-05-24 00:30:00', '2025-05-24 03:00:00', 150, 'Re-arranging folder structure for Resource and views', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(3, 'session_1748549520_686227eb3b190', 3, 'completed', '2025-05-30 00:12:00', '2025-05-30 01:15:00', 63, 'Retrying to run the new upated version of lsmh on localhost - postgres, created list of error word file - no dumpfile found, tried old new_db - fixed porstgres default password complience requirememts', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(4, 'session_1748883900_686227eb3bb4d', 3, 'completed', '2025-06-02 21:05:00', '2025-06-02 21:42:00', 37, 'Reinstall the project to check db migrate - fix migrate issues', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(5, 'session_1749146580_686227eb3c8c7', 3, 'completed', '2025-06-05 22:03:00', '2025-06-05 23:53:00', 110, 'Reintall master branch- and testing accessment workflow', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(6, 'session_1749166560_686227eb3d7da', 3, 'completed', '2025-06-06 03:36:00', '2025-06-06 06:39:00', 183, 'Implimenting Stripe - work in progress....', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(7, 'session_1749243780_686227eb3e206', 3, 'completed', '2025-06-07 01:03:00', '2025-06-07 06:33:00', 330, 'Stripe implimentation cont..+ new sql repo zip testing and migrate stripe in it - branch from sql repo - changes 1. fixed - assessment next issue 2. added stripe and remove insurance 3. added redo assessment - stripe modal 4. fix - progress bar issue 5. fix - admin payment UI and implement stripe and receipt  6. Fix migration sytnax issues', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(8, 'session_1749334080_686227eb3ec08', 3, 'completed', '2025-06-08 02:08:00', '2025-06-08 06:44:00', 276, 'fix route for  stripe receipt and implimenting Claude AI - in progress', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(9, 'session_1749587100_686227eb3f5da', 3, 'completed', '2025-06-11 00:25:00', '2025-06-11 06:39:00', 374, 'fix- Fixed Database Field Names, fix - Enhanced Clinical Findings Generation, created - ClinicalFindingsService for reusable code, fix- Improved Suicidal Ideation Detection, Dynamic Recommendations, fix migration and deleted old files', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(10, 'session_1749673680_686227eb403af', 3, 'completed', '2025-06-12 00:28:00', '2025-06-12 04:31:00', 243, 'fix some miror issue, create word file - create comprehensive report feature', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(11, 'session_1749756480_686227eb40dc6', 3, 'completed', '2025-06-12 23:28:00', '2025-06-13 06:47:00', 439, 'refactor patient , and some minor fix, db cleanup, accessment UI change', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(12, 'session_1749849840_686227eb4183f', 3, 'completed', '2025-06-14 01:24:00', '2025-06-14 07:20:00', 356, 'refactor admin and UI change, bug fixes', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(13, 'session_1749931260_686227eb42296', 3, 'completed', '2025-06-15 00:01:00', '2025-06-15 04:35:00', 274, 'admin/patient controller refactor, bug fixing', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(14, 'session_1750016760_686227eb42d16', 3, 'completed', '2025-06-15 23:46:00', '2025-06-16 06:43:00', 417, 'patient registration restruction - client/company table based on word file', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(15, 'session_1750108800_686227eb438b4', 3, 'completed', '2025-06-17 01:20:00', '2025-06-17 05:27:00', 247, 'fixing, modal popup, and minor migration, creating CURD for User/admin', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(16, 'session_1750200180_686227eb4446b', 3, 'completed', '2025-06-18 02:43:00', '2025-06-18 06:22:00', 219, 'fix - phone, rewamp patient register structure, fixed loophole for direct accessment access', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(17, 'session_1750264380_686227eb44e18', 3, 'completed', '2025-06-18 20:33:00', '2025-06-18 21:40:00', 67, 'fixing - direct accessemnt url', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(18, 'session_1750278060_686227eb457a6', 3, 'completed', '2025-06-19 00:21:00', '2025-06-19 06:45:00', 384, 'restructure dir for accessment, sperate logic and controller, accessemnt tracking, some major or miror fixes', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(19, 'session_1750311600_686227eb4614b', 3, 'completed', '2025-06-19 09:40:00', '2025-06-19 10:30:00', 50, 'fixing term&cond modal', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(20, 'session_1750714200_686227eb46a5f', 3, 'completed', '2025-06-24 01:30:00', '2025-06-24 07:23:00', 353, 'sign old restructure, new css color palet, accessment color change', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(21, 'session_1750801500_686227eb47443', 3, 'completed', '2025-06-25 01:45:00', '2025-06-25 07:59:00', 374, 'Stripe coupon code impliment, js restructure', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(22, 'session_1750737600_686227eb47fc5', 3, 'completed', '2025-06-24 08:00:00', '2025-06-24 10:23:00', 143, 'dynamic PID for product catelog', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(23, 'session_1750885080_686227eb48952', 3, 'completed', '2025-06-26 00:58:00', '2025-06-26 04:30:00', 212, 'Coupon code restriction - rollback due to stripe dashboard issue', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(24, 'session_1750919700_686227eb49327', 3, 'completed', '2025-06-26 10:35:00', '2025-06-26 12:21:00', 106, 'Applied Coupon restriction - using metadata', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(25, 'session_1750966800_686227eb49cc7', 3, 'completed', '2025-06-26 23:40:00', '2025-06-27 04:30:00', 290, 'Accessment portal implimentation admin panel', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(26, 'session_1751057100_686227eb4a687', 3, 'completed', '2025-06-28 00:45:00', '2025-06-28 07:24:00', 399, 'Questionare -  patient accessment and pdf report fix,  Question post to db fixed', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(27, 'session_1751172060_686227eb4b03c', 3, 'completed', '2025-06-29 08:41:00', '2025-06-29 11:34:00', 173, 'Assessment PDF Updates -  top 3 issues display + patient details update also Question Counter: Now updates from "0 / 5 questions" to "1 / 5 questions" etc.   2. Individual Progress: "X of Y completed" updates properly in the submit section   3. Overall Progress: "X of Y sections completed" updates when questionnaires finish   4. Radio Button Colors: All radio buttons now use orange (#EC7132) instead of blue   5. Sidebar Visual States:     - Completed modules: Bright with blue styling     - Active module: Bright white     - Upcoming modules: Greyed out   6. Sidebar Auto-scroll: Automatically scrolls to keep the active questionnaire visible +Sticking header- patient accessment question stick to top when scroll for long list of questions', NULL, '127.0.0.1', 'TimeLogSeeder', '2025-06-30 10:00:11', '2025-06-30 10:00:11'),
(33, 'xyjpFJIyzbEtK9BwIAk9oDxRylMk6Tii', 3, 'completed', '2025-06-30 16:45:00', '2025-06-30 20:31:00', 226, 'Bug fix - Soft delete, hidden question, archieve, scaler- remove unused features', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 16:45:17', '2025-06-30 20:32:54'),
(34, 'r4Ez3Poqe9xKSTEujbm0iYiURKMzRhat', 3, 'completed', '2025-07-01 05:05:00', '2025-07-01 08:08:00', 183, 'Cleaner codebase, better UX, no functionality loss, proper assessment type handling!', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-01 05:05:20', '2025-07-01 08:08:36'),
(36, 'JI1AXCe2nKqixQOW4YxduNmza3lHpOD0', 3, 'completed', '2025-07-02 00:00:00', '2025-07-02 03:28:00', 208, 'batch - operation logic for answer table', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 03:08:13', '2025-07-03 19:33:51'),
(37, '2EaApELvYdSlSHjOtABUvUZmtkewuq8W', 3, 'completed', '2025-07-02 04:18:00', '2025-07-02 07:18:00', 180, 'Restructure - answer table Json Format - Some minor fixes
Admin - Refine structure Json, Payment coupon display, minor fixes', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 04:18:13', '2025-07-02 07:18:39'),
(38, 'DQdgafANu7pSirWky6B7ne9BLOLDpWA4', 3, 'completed', '2025-07-02 14:58:00', '2025-07-02 16:50:00', 112, 'stripe redeem coupon change - implimented -  minor fixes', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:58:41', '2025-07-02 16:50:49'),
(40, 'RCr1kppFden12IUvYC9o3mb62aYJEls7', 3, 'completed', '2025-07-03 19:40:00', '2025-07-04 00:22:00', 282, 'REDIS Research and Redis Implimentation in progress to Assessment...', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-03 19:40:39', '2025-07-04 00:23:14'),
(46, '02TP0zmiNy0MhZ4Z97sPsDz8Vm3YPEdg', 3, 'completed', '2025-07-04 02:39:00', '2025-07-04 05:51:00', 192, 'REDIS implimentation - security db backup ways implimented to prevent data loss, enhance realtime question answer saved. db answers restructured and remodeled', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-04 02:39:14', '2025-07-04 05:53:35'),
(47, '72ocMByP9y1XOaM9DkRhWSHblKM0EkQF', 3, 'completed', '2025-07-04 19:22:00', '2025-07-04 22:41:00', 199, 'removing old legacy code -REDIS OPTIMIZATION - minor fixes-create admin patient accessment tracking - minor fixes-minor fix -admin  login route fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-04 19:22:08', '2025-07-04 22:41:49'),
(48, 'sFTUtMQ1iXN5mShQo2AR8qGYlKqy7e8A', 3, 'completed', '2025-07-05 20:10:00', '2025-07-05 23:48:00', 218, 'old, question deleted and reconfigure the seeder files, New question added reconfigure the seeder files, fixed some bugs and errors', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-05 20:50:42', '2025-07-05 23:49:45'),
(49, 'IRDxqSqUL8mSF0uHFPL25iKWZVzzUQJo', 3, 'completed', '2025-07-06 05:34:00', '2025-07-06 10:12:00', 278, 'Restructure Seeder removing old making correction adding new- fixing admin errors - adding antropic chat storage for prompts', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-06 05:34:47', '2025-07-06 10:14:26'),
(50, 'R4QTztz3Zj6gf9L27e2Sc03FzTRmlvGH', 3, 'completed', '2025-07-07 19:45:00', '2025-07-08 02:26:00', 401, 'Table structure for chat system and Json format -  UI chat enhancement - Minor fixes -  peer support Change layout and payment system added with new product ID - Coupon code added', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-07 19:45:44', '2025-07-08 02:32:36'),
(51, '5t2l8RbWjncxyTgRUurQR3IMYIIr3mHB', 3, 'completed', '2025-07-09 23:04:00', '2025-07-10 05:05:00', 361, 'Peer Support Questionnaire Addition - new migration with json addition in exiting assessment table integration, added UI and enhancement and minor fix, added peer support implimentation in admin payment and  assessment, peer support seeder added', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-09 23:04:32', '2025-07-10 07:09:51'),
(52, 'xSsxPRP2F4neHBXIpT9pUBDetNWTRraY', 3, 'completed', '2025-07-10 07:10:00', '2025-07-10 11:06:00', 236, 'peer support admin implimentation inclung display peer questions UI - convert assessement top progress bar to questionare. other minor fixes', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 07:10:14', '2025-07-10 11:08:22'),
(53, 'okY4g4swzJkG5aGyhvjTuIJnGxsYoKGI', 3, 'completed', '2025-07-13 05:28:00', '2025-07-13 10:29:00', 301, 'fix -  remeber me, updated term&condition modal, add new sweetalert lib and reconfig and added new middleware for signin route for redirect based on roles, updating sweetalert on ?, fixing modals, pin/postal and certificate', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 07:28:08', '2025-07-13 10:30:40'),
(54, 'YDnAXpBbwowijpQXJn6VRtHgsrEhY7ZV', 3, 'completed', '2025-07-15 03:45:00', '2025-07-15 08:44:00', 299, 'Twilio Mock dummy chat impilmented due to SSL vification conflict - new peer migration create for twilio converciation - merging with exiting scoring system', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 05:45:56', '2025-07-14 08:46:32'),
(55, 'ABbhpVXMUGhCrKZLvwhZf8fqqb5mG0lZ', 3, 'completed', '2025-07-17 03:38:00', '2025-07-17 08:02:00', 264, 'TWILIO_CONVERSATIONS_SERVICE_SID=ISb2a14b9fec8c40dbaef4eecfebfecb77, this starts with "IS" which indicates it''s a legacy Chat Service SID, not a Conversations Service SID - require new SID after testing the key', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 05:38:14', '2025-07-15 08:03:28'),
(56, 'JWdZgDbmVcum3zbkDNk9nnUYpOMdql6K', 3, 'completed', '2025-07-19 01:36:00', '2025-07-19 07:53:00', 377, 'FIX - Twilio live conversation, user connect fix, UI enhancement, added file upload feature, restructure the peer chat support DIR, improve peer profile match page, remove unwanted feature and  some minor fix.', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-17 02:36:49', '2025-07-17 07:58:49'),
(57, 'egMj8tYVqjGcbNw9rhua7o1EeEZO4ByD', 3, 'completed', '2025-07-20 02:28:00', '2025-07-20 08:31:00', 363, 'New Harper and echolink logic peer support score implementation and chat peer support bug fix - other minor fix - trigger answer assement bug fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 03:28:38', '2025-07-18 08:37:12'),
(58, 'QqJxgfVqSEBs1cNQgQUIcPqpeI0sSjAN', 3, 'completed', '2025-07-21 20:43:00', '2025-07-22 05:10:00', 507, 'remove hexaco Assessment (Completed) #2 from patient dashboard. Timezone added, updated counsellor - approve/reject, #ID to distinguish - hexaco title updated, hide patient info from pdf for counsellor, fix issue with counsellor login redirect, added patient email invite email, patient counsler can see contact info after approval - other minor fixes', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 22:43:11', '2025-07-22 05:10:32'),
(59, 'UGzdGBTie79SIDA79OkP9PKnBVxAXG27', 3, 'completed', '2025-07-22 21:51:00', '2025-07-23 05:24:00', 453, 'Suicidal flag trigger implemented, email system implemented and queue added - UI upgrade, added major and mirror fix on admin/patient', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 21:51:18', '2025-07-23 05:25:36'),
(60, 'j5O90O9lEp58zTcaTnZn2Lo7vz86GaiH', 3, 'completed', '2025-07-24 14:38:00', '2025-07-24 15:45:00', 67, 'Intercom documentation research for implementation. for messanger installation - web chat - https://app.intercom.com/a/apps/yft5uevs/settings/channels/messenger/install?section=web-messenger-setup&tab=web', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-24 14:38:36', '2025-07-24 15:49:22'),
(61, 'gEZ9nWWZI77ybMCMUxwcNilfKtej2KLe', 3, 'completed', '2025-07-26 13:12:00', '2025-07-26 18:14:00', 302, 'Multiple UI/UX improvements and critical pain scoring fix - Added Intercom chat integration to patient/counsellor dashboards, implemented assessment question highlighting for missed answers, fixed certificate full names,updated PDF branding from "Dr. Mitch Colp" to "Harper Medical Team", added password visibility toggle, corrected McGill Pain Questionnaire boundaries (1,2,3 → 15,30,40), and implemented automatic ECHOLINK selection for first responders in registration form', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-26 15:43:15', '2025-07-26 18:15:16'),
(62, 'T9vWCUNBgmir8pt5yvPzEHP96hAWPgPZ', 3, 'completed', '2025-07-28 15:26:00', '2025-07-28 21:36:00', 370, 'intercom - visitor/user fix - MHP (counselor) name change', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 15:25:54', '2025-07-28 21:37:08'),
(63, 'OrFl8zBEZoegKlhEOVZz8tfg9SrK1nIY', 3, 'completed', '2025-07-31 16:54:00', '2025-07-31 20:22:00', 208, 'fix- assessment sticky header, fix known issues, changes in pdf generations, email format changes, dob format changes and other minor fixes', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 17:54:37', '2025-07-31 20:24:39'),
(64, 'lh3AetaT7FSdTgB7VAQBfWI6zrgWwLC8', 3, 'completed', '2025-08-02 16:42:00', '2025-08-02 22:01:00', 319, 'email format fix,score color change, removed old substance abuse seeders,New substance abuse ASSIST-LITE added - seeders,UI bug fix and  desktop and mobile alignmnt fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 18:42:02', '2025-08-02 22:02:48'),
(65, 'IwSJKWMnMTTymWQj17za7VaEwAiBBRo1', 3, 'completed', '2025-08-06 03:34:00', '2025-08-06 08:12:00', 278, 'fix color code and summary missmatch points in pdfs, intake form implimented with database migration and seeder fix', NULL, '207.210.46.161', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 04:34:23', '2025-08-05 08:13:50'),
(66, 'bGbKplecdFrNNPWKGBBstXvZD3vLZBWW', 3, 'completed', '2025-08-08 21:20:00', '2025-08-09 02:37:00', 317, 'fix - current idenity, chat with harper and other miror fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 21:20:57', '2025-08-09 02:37:21'),
(67, 'HfguLdKX8hXHfR4RM6he0MzJav0dhBOh', 3, 'completed', '2025-08-09 20:46:00', '2025-08-09 23:11:00', 145, 'assessment floating btn % and intercom patient chat conflict fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-09 20:46:52', '2025-08-09 23:12:04'),
(68, 'wQTfPwrw8YtPO7wPQJnU9pLwylsw2zrr', 3, 'completed', '2025-08-13 18:17:00', '2025-08-14 01:28:00', 431, 'Enhance patient chat functionality with OpenAI thread validation and recovery; improve error handling in assessment scripts; add comprehensive assessment scoring guide and patient journey workflow documentation. added forget password functionality and minor fix', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-13 18:17:01', '2025-08-14 01:29:25'),
(69, 'xRwDI9jlP9NTJnVn1H2LQxvX2D9ENQa7', 3, 'completed', '2025-08-19 17:21:00', '2025-08-19 20:37:00', 196, 'US and CA regulated bodies added - pending db and other', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 17:21:26', '2025-08-19 20:38:26'),
(70, 'EX2Zj51wbCJQ2LhhxXd8WXi1jIjz7xo4', 3, 'completed', '2025-08-21 03:43:00', '2025-08-21 07:13:00', 210, 'zip and postal fix - added other field on bodies fix Unexpected token ''< in peer support', NULL, '207.210.46.161', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 04:43:52', '2025-08-20 07:14:15'),
(71, '10qKWRHZkxLJrcEldUzA26OdoB3yY3e9', 3, 'completed', '2025-08-21 16:02:00', '2025-08-21 18:55:00', 173, 'feat: Add secure multi-registration licensing system

  - Added province/state and regulatory body dropdowns
  - Support for multiple professional registrations
  - Enhanced security with input sanitization and file validation
  - Fixed form restoration and validation issues
  - Added admin/counselor and profile display views
  - Implemented rate limiting and comprehensive error handling', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-21 16:02:03', '2025-08-21 18:57:17'),
(72, '4SkhJpvMDOVZW7MoCKjpJFU66gtny4LU', 3, 'completed', '2025-08-22 16:43:00', '2025-08-22 20:01:00', 198, 'counsller other option, upload profile image based on region', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-22 16:43:41', '2025-08-22 20:01:43'),
(73, 'uDKjA751BizvM9Ls67rj8OZ3AJxZJkZP', 3, 'completed', '2025-08-23 01:05:00', '2025-08-23 08:27:00', 442, 'suicidal email fix, added email queue debug route, devided queue system into urgent/default, composer dev to run all at once (email, server, redis)', NULL, '207.210.46.161', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-23 01:05:52', '2025-08-23 08:28:48'),
(74, 'M3Qbe3LblCH5WXOlGxAZUQPQWIaZetR8', 3, 'completed', '2025-08-24 07:56:00', '2025-08-24 10:09:00', 133, 'add, peer support anonmus feature,  remove static us&ca droptown', NULL, '207.210.46.161', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-24 07:56:30', '2025-08-24 10:09:45'),
(76, 'Hxcfs5dzsru7GiNIGKzlTb021j4hFpIK', 3, 'completed', '2025-08-25 22:34:00', '2025-08-26 00:44:00', 130, 'feat: improve registration form UX and validation

  - Make registration number required with validation
  - Reduce delete button size and fix positioning
  - Environment-aware Province/State labels (CA/US)
  - Fix delete button click handler for instant response, Other - Not Listed Above - remove from governing bodies on counsller', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-25 23:44:26', '2025-08-25 23:46:06'),
(77, 'NgHWmPniyq2C1jzHTOT98lauqh02feBx', 3, 'completed', '2025-08-31 14:57:00', '2025-08-31 21:47:00', 410, 'Add 6 mental health questionnaires to clearspace module

  - WHO-5 Wellbeing Index (ID: 941) - 5 questions
  - RSES Self-Esteem Scale (ID: 942) - 10 questions with reverse scoring
  - PSS-10 Stress Scale (ID: 943) - 10 questions with reverse scoring
  - K6 Distress Scale (ID: 944) - 6 questions
  - BRS Resilience Scale (ID: 945) - 6 questions with reverse scoring
  - CD-RISC-10 Resilience Scale (ID: 946) - 10 questions', NULL, '2605:8d80:6a0:750f:19fc:b83e:1a21:a268', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-31 15:57:36', '2025-08-31 21:48:32'),
(78, '4AaA6uvXPkDHp3xXgT6mTyong28gEhaF', 3, 'completed', '2025-09-01 17:50:00', '2025-09-02 03:27:00', 577, 'Add 7 mental health questionnaires to clearspace module

  - PROMIS Fatigue Short Form
  - IGDS9-SF Gaming Disorder Scale
  - SMDS-9 Social Media Disorder Scale
  - PHQ-15 Physical Symptom questionnaire
  - UCLA Loneliness Scale Short Form
  - SCOFF Eating Disorder Screening Tool
  - EAT-26 Eating Attitudes Test', NULL, '2605:8d80:6a0:82e:35f9:bdd9:4c6f:7233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-01 23:50:28', '2025-09-02 03:28:30'),
(79, 'ZzRFBe0DBMo3KIffyixZXssNKYIrpMKz', 3, 'completed', '2025-09-03 16:00:00', '2025-09-03 20:49:00', 289, 'Migrating 13 Questionnaires Clearspace from exisitng to new..work in progress...', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-03 18:42:09', '2025-09-03 20:51:09'),
(80, 'ZBvSZk0t9M1ZpJlmzlPDKotjZef4rSer', 3, 'completed', '2025-09-04 23:46:00', '2025-09-05 07:54:00', 488, 'feat: add PROMIS Sleep and MSPSS questionnaires, ensure all Adolescent data in Module seeders

  - Add PROMIS Sleep Disturbance Check (ID 957): 8 questions with 5-point scale
  - Add MSPSS Social Support Check (ID 958): 12 questions with 7-point Likert scale
  - Verify all Adolescent assessment types moved from original to Module seeders
  - Complete PROMIS Anger implementation (method call and assessment link)
  - All clearspace/Adolescent questionnaires now properly organized in Module files', NULL, '2605:8d80:6c20:ce4:75e6:91f6:1f2e:d0ed', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 04:46:47', '2025-09-05 07:28:00'),
(81, 'jumJkbJD5kRtnFVbEZdGho13bPDTXk0S', 3, 'completed', '2025-09-05 23:26:00', '2025-09-06 07:25:00', 479, 'add 9 mental health questionnaires for Athlete assessment type

  - WHO-5, RSES, PHQ-9, GAD-7, PSS-10, K6, PROMIS Anger, BRS, CD-RISC-10
  - Includes reverse scoring for PSS-10 and BRS items
  - 67 questions and 337 scoring options with traffic light boundaries', NULL, '2605:8d80:6a1:2052:2c5e:34b8:bdb:d074', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-05 05:26:03', '2025-09-05 07:29:24'),
(82, 'OkASX8HJN3hOZjJR5TRwMowUc6eBlQes', 3, 'completed', '2025-09-08 03:00:00', '2025-09-08 09:23:00', 383, 'added: Athlete
Fix: dublicate ID fix
SMTQ
ACSI-28
CSAI-2R
Flow State Scale - Short From (FSS-SF)
PROMIS Sleep Disturbance - Short From', NULL, '2605:8d80:6a0:1fdd:e040:e4bf:587f:383e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-06 05:00:10', '2025-09-06 09:24:58'),
(83, 'o2LKFtpTnqvKl9uQEi5Suf79v57sU44X', 3, 'completed', '2025-09-07 00:54:00', '2025-09-07 09:26:00', 512, '- Added WHO-5 and PSS-10 scoring entries with safe IDs (20100-20179)
  - Fixed PSS-10 reverse scoring for items 4,5,7,8
  - Enabled all Adolescent questionnaire scoring methods in seeder
  - Resolves missing answer options in questionnaire previews
  - Added all remaining - PROMIS Fatigue - Short Form
IGDS-9 SF
SMDS-9
PSQI
Rivermead Post-Comcussion Symptoms (RPQ) - Note: Only if client has answered "yes" to diagnosed cuncussions in the intake form
UCLA Llonliness Scale - Short Form
MSPS
Athlete Burnout Questionaire (ABQ)
SCOFF Questionaire
EAT-26', NULL, '2605:8d80:6a3:3e16:7d50:909a:981:47db', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-07 00:54:32', '2025-09-07 09:26:23'),
(84, 'HWvhiocSTYUWrpIgqWyaXIkAB6baoqFW', 3, 'completed', '2025-09-08 21:10:00', '2025-09-09 03:02:00', 352, '-Conflict between seeders both baseline and clearspace due to same question/score
-separation the Seeder files to prevent conflict
-Rollback due to more bugs.', NULL, '2605:8d80:6a0:a6b6:c43a:d17b:8608:e8dd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 22:10:26', '2025-09-09 05:04:19'),
(85, 'DfcY3EE7esPWR0WH06nukAz0O46pGwLW', 3, 'completed', '2025-09-09 13:46:00', '2025-09-09 17:47:00', 241, 'fix some seeders - clearspace duplicate entry, fix admin questionnaire filter, testing clearspace assessments, pdf generate changes', NULL, '2605:8d80:6c25:46d1:c5f9:420e:3eb3:ca93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 23:46:39', '2025-09-09 23:57:08'),
(86, 'rKJRQpYIQ3Dygrg8Z1N6iWtV60Zbdmxz', 3, 'completed', '2025-09-09 20:02:00', '2025-09-10 02:25:00', 383, 'fix: resolve JavaScript errors on patient registration and RPQ conditional logic

  - Move Stripe script loading to proper order in auth layout
  - Add error handling for Stripe initialization and external resources
  - Add/Fix RPQ questionnaire conditional logic to handle multiple data types
  - Ensure RPQ shows in Athlete assessments when concussion_diagnosis=yes
  - Add graceful fallbacks for failed external script loading', NULL, '2605:8d80:6c25:46d1:85d2:b58c:b08:8b84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 00:57:09', '2025-09-10 02:27:48'),
(87, 'h8x8E4BvvVLIjQkpOAuzsYU6fqx2Ihhg', 3, 'completed', '2025-09-11 02:17:00', '2025-09-11 06:46:00', 269, 'feat: implement parent notifications and fix JS errors

  - Add parent notification system for minor assessments (16-17 years)
  - Create parental notification emails for assessment start/completion
  - Add parental information fields to client registration
  - Fix Stripe initialization and JavaScript errors on registration page
  - Improve error handling for external resources (Intercom, CDN scripts)
  - Remove duplicate parental notification logic from controller
  - Configure event-driven notification system with queue processing', NULL, '2605:8d80:6c24:1176:38dd:59ee:7f8f:827b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 23:17:32', '2025-09-11 06:47:08'),
(88, 'xCECaDXsyxgvx3MmgchiDNO3C45kVhb8', 3, 'completed', '2025-09-12 22:44:00', '2025-09-13 04:50:00', 366, 'feat: implement dynamic assessment-specific PDF templates and fix color consistency

  - Add structured review templates for Athlete (Baseline™) and Adolescent (Clearspace™) assessments
  - Implement dynamic clinical findings based on assessment type with color-coded priority areas
  - Fix score-boundaries page color mapping (yellow/orange swap) and PDF color consistency
  - Add patient-dashboard link to patient sidebar navigation
  - Remove HTML tags from all questionnaire instructions in seeder
  - Fix reverse-scoring logic for Self-Worth & Confidence Check (RSES) questionnaires
  - Make questionnaire names clickable on score-boundaries page to open admin panel
  - Remove unused Max/percentage columns from score-boundaries display', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 20:43:52', '2025-09-13 04:50:17'),
(89, 'QTyiK8vnphqw0Pf2epWRvnT29WIGVAxn', 3, 'completed', '2025-09-14 00:41:00', '2025-09-14 01:45:00', 64, 'parent notification queue + well-being cmd console', NULL, '2605:8d80:6c27:2fcc:e44b:63d6:9fb:133d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-13 21:41:23', '2025-09-15 01:46:37'),
(90, 'A5LgmS1Kggz4SOfs51to3cSsIkWZI6YV', 3, 'completed', '2025-09-17 16:03:00', '2025-09-17 20:56:00', 293, 'feat: replace assessments timeline with interactive donut chart on admin dashboard

  - Convert "Assessments By Type" from timeline list to visual donut chart
  - Add Chart.js implementation with custom colors and legend
  - Fix canvas reuse errors with proper chart instance management
  - Include percentage tooltips and smooth animations
  - fix patient mental health professional url
  - fix the patient MHP switch bug
  - design MHP dashboard', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 17:03:19', '2025-09-17 20:56:32'),
(91, 'DIah8hBcajl0sC76p9pgYcRNm0FWqbdr', 3, 'completed', '2025-09-17 21:00:00', '2025-09-18 04:50:00', 470, '-Add athlete identification questions to The Baseline assessment registration
-Add % based match patient profile with MHP
-Email template url fix
-Admin Provider list bug and adjustments
-admin/MHP Dashboard enhancements UI', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 21:00:20', '2025-09-18 04:51:10'),
(92, 'SpTjhZnbvVy2QHUJz8OJIVS2zRrl2izs', 3, 'completed', '2025-09-18 15:34:00', '2025-09-18 18:51:00', 197, '- Add IFDFW questionnaire (ID 987) with Harper Color Scheme scoring
  - Add 8 financial stress questions (IDs 15447-15454) with custom 5-point scales
  - Link to Athlete assessment (priority 42) as "Financial Stress Check"
  - Consolidate all questionnaires in Module2: IFDFW, SAS-2, AIMS, CTAS

In progress....', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-18 17:34:35', '2025-09-18 18:52:09'),
(93, 'yWq7Wtl2mxIE0bh5cC7QRS9smc1q2KOh', 3, 'completed', '2025-09-19 16:03:00', '2025-09-19 21:09:00', 306, '1. Financial Stress Check - 8 questions (IFDFW)
  2. Sport Anxiety Check - 6 questions (SAS-2)
  3. Athletic Identity Check - 7 questions (AIMS)
  4. Career Transition Check - 5 questions (CTAS)
  5. Social Support Check - 12 questions (MSPSS)
- Binding Questionare with iintake questions
  - Create psqi:remove console command with dry-run and force options
  - Remove questionnaire ID 977, question 15343, scoring options 19399-19402
  - Remove assessment link ID 1397 from Athlete assessment type
  - Clean up existing user responses from assessments table
  - Add transaction handling and comprehensive error logging', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-19 19:47:26', '2025-09-19 21:11:29'),
(94, 'E6pfF0LjhOGW9FDKdCeVKZFqmsduUH8y', 3, 'completed', '2025-09-21 04:55:00', '2025-09-21 08:24:00', 209, 'fixing dublicate emails and parent completion email. fixing in progress....redis queue system not making new changes....', NULL, '2605:8d80:6a20:cb28:bdb2:5f9c:9244:c5c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-21 05:55:55', '2025-09-21 08:25:45'),
(95, 'TjeLIVsPdbt7BLpRUAbzHdj3wtvDW6Iy', 3, 'completed', '2025-09-23 18:05:00', '2025-09-24 02:08:00', 483, '- Eliminate duplicate suicidal ideation emails using Redis atomic locks
  - Add Redis deduplication to prevent duplicate parental notifications
  - Convert listeners to synchronous execution to avoid queue caching issues
  - Filter parental notifications to only Athlete and Adolescent assessments
  - Remove test commands and cleanup codebase
  - Implement UrgentEmailService with atomic locking for critical notifications', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-23 18:05:47', '2025-09-24 02:08:35'),
(96, 'QBBrboEKBt4bs0wJiWnEjBmdyOoRpWqi', 3, 'completed', '2025-09-24 17:00:00', '2025-09-24 20:45:00', 225, 'Add patient profile page and implement peer support age validation

    - Create read-only profile page showing signup information only
    - Display full name including middle name in profile banner and navbar
    - Add profile links to sidebar (above Change Password) and navbar dropdown
    - Implement orange gradient banner (#f47d42) with white text
    - Remove edit functionality and assessment history as requested
    - Add age validation for peer support features (18+ only)
    - Hide peer support links for underage users in UI
    - Create PeerSupportAgeValidation trait for reusable validation
    - Fix route conflicts by using /my-profile path Add patient profile page and implement peer support age validation
- remove test files', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-24 18:58:26', '2025-09-24 22:38:49'),
(98, 'UuSddfXQINdEH18MSfyqZoInvgxhVFsj', 3, 'completed', '2025-09-26 01:08:00', '2025-09-26 05:12:00', 244, 'Enable Clinical Director Access to PDF Reports for Suicidal Ideation Cases in progress...', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-27 18:08:30', '2025-09-27 18:09:41'),
(99, 'i4btRm0LIjL5m4SWhpcE47AkH2mSoVqa', 3, 'completed', '2025-09-27 18:55:00', '2025-09-27 21:58:00', 183, 'implementation clinical director role and permission - work in progress...', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-27 18:55:08', '2025-09-28 16:59:26'),
(100, 'uAiV5aqtE5sgQHabAMB1PZqfvGTuNbRQ', 3, 'completed', '2025-09-28 15:00:00', '2025-09-28 21:47:00', 407, 'feat: Implement automatic permission system with bug fixes

• Add automatic permission assignment for all new users
• Create UserPermissionService and setup:permissions --force command
• Fix SQL errors and HTML5 validation issues
• Redesign no-permissions page and fix UI alignment
• Refine suicidal detection logic and add Clinical Director role
• Implement automatic assignment of suicidal assessments
• Add visual alerts and update PDF access control
• Fix Questionnaire 940 scoring options', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-28 17:00:24', '2025-09-28 21:48:29'),
(102, 'Xl1YINqh6mCb26R5YKQuHyg0kYWLagJk', 3, 'completed', '2025-10-02 02:02:00', '2025-10-02 09:36:00', 454, 'fix: Q940 staging sync - resolve missing scoring for substance abuse questionnaire

Identified: Q940 broken with only 1/9 questions having scoring options due to incomplete QuestionScoringTableSeeder.php missing Q9000 and Q3651-Q3657 data.

Implemented: 
- Extracted exact staging IDs (17584-17593, 20334-20368) 
- Created Q940StagingSyncSeeder with staging-identical ranges
- Built sync:q940-staging command for deployment
- Added verification and safety checks

Tested:
- Verified 45 scoring IDs match staging exactly
- All 73 questionnaires now 100% functional  
- Zero production conflicts using staging ranges

Result: Q940 fully functional with staging synchronization', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 04:02:46', '2025-10-02 09:38:31'),
(103, '13W919JkP4QlPUno8Np2uhGRCqT8Y7QC', 3, 'completed', '2025-10-05 01:41:00', '2025-10-05 02:36:00', 55, 'Testing staging schema on local', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 02:41:53', '2025-10-03 02:43:06'),
(104, 'fOrRpoFrUGvBgGF1sZv0KlipwLyWyl4t', 3, 'completed', '2025-10-05 05:12:00', '2025-10-05 08:16:00', 184, '- resetting the Susidal trigger Question flow
- Bug fixing…
- Resetup the susidal email alert for clicinal director work in progress….', NULL, '2605:8d80:6a1:8b01:d00a:6245:c3d4:6194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-05 08:12:39', '2025-10-05 08:17:20'),
(105, 'UyZJp9SO9aVXgpeulLs7olPctugkOwdS', 3, 'completed', '2025-10-06 20:08:00', '2025-10-07 04:26:00', 498, 'Suicidal ideation detection & director workflow updates

  - PHQ-9 Q9 only detection ("better off dead", score > 0), removed keywords
  - Removed clinical_director role, consolidated to director
  - Removed auto-assignment; email alerts retained
  - Directors view ALL suicidal cases without assignment
  - Suicidal tab: "View Profile" for all statuses
  - Patient profile modal: personal info, location, crisis details
  - "PATIENT SELECTED YOU" badge when assigned to director
  - Show assigned counsellor in approval status
  - Urgent email (🚨) with crisis alert for suicidal cases
  - Fixed sidebar scroll, data display (DOB/country/state/postal/gender)
  - "Awaiting Selection" vs "Pending Approval" status
  - Added /patient-profile route for directors
  - Removed unused seeder', NULL, '2605:8d80:6a3:4306:824:1f5a:a7ec:26a4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-06 01:08:46', '2025-10-06 04:27:04'),
(151, 'XTc9HfE6WnJK8VrVbvrQsbyJXkGvXhFm', 5, 'completed', '2025-10-03 17:00:00', '2025-10-03 19:00:00', 120, 'Traning', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 00:46:42', '2025-10-09 00:48:30'),
(152, 'fcYPk5GdGFwe63P6bxHOF8qpNYhZP3LG', 5, 'completed', '2025-10-03 23:00:00', '2025-10-04 11:00:00', 720, 'Regular 12hr shift', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 00:48:40', '2025-10-09 00:50:21'),
(153, 'MKr1u0NZgwKmltBe1PLd0PgtfhEjVBDN', 5, 'completed', '2025-10-04 23:00:00', '2025-10-05 11:00:00', 720, 'Regular 12hr shift', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 00:49:01', '2025-10-09 00:51:17'),
(154, 'slAE72QTaTb6brCQIONRcpfm88endZ36', 5, 'completed', '2025-10-05 23:00:00', '2025-10-06 11:00:00', 720, 'Regular 12hr shift', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 00:51:28', '2025-10-09 00:52:01'),
(158, 'otuQ8tGh2M5AZGVGObqSG3awsPeohlVs', 3, 'completed', '2025-10-07 18:49:00', '2025-10-07 23:57:00', 308, '- Build admin panel for coupon management (create, edit, import, sync)
- fix bug and error
- remove/modify old coupon 
work in progress.....', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 02:49:40', '2025-10-09 05:59:10'),
(159, 'ctxwhUvYLopiHqHsueVU5p9GAa5z2lIK', 3, 'completed', '2025-10-08 18:22:00', '2025-10-09 03:01:00', 519, '- Add coupons table migration with soft deletes and product restrictions
  - Add Stripe product catalog integration with dynamic product display
  - Implement bulk coupon import from Stripe with duplicate handling
  - Add detailed import feedback showing success/skip/fail status per coupon
  - Update patient registration & peer support to use database coupons
  - Enforce strict product ID-based restrictions (assessment vs peer_support)
  - Handle duplicate coupon names by appending Stripe ID
  - Dynamic "Applies To" field showing actual Stripe product names
  - All Stripe keys configurable via .env (safe for test→live migration)

Testing in progress.....', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-09 05:59:50', '2025-10-09 06:03:53'),
(160, 'XSSzyz2fGKMSejWtuwJBQ3RaQYcqNyY0', 3, 'completed', '2025-10-10 17:06:00', '2025-10-10 22:15:00', 309, 'Testing & Improve coupon system with validation and UX enhancements

  - Fix duplicate alerts on admin coupons index page
  - Update unique constraint to allow name reuse after soft deletion
  - Add comprehensive coupon validation with specific error messages
    - Inactive coupon detection
    - Expiration date validation
    - Max redemption limit enforcement
    - Per-user redemption tracking (prevents duplicate usage)
  - Sync Stripe redemption count before validation
  - Pass user email from frontend for duplicate usage check
  - Enhance confirmation dialogs with Stripe impact warnings
    - Delete: warns about permanent Stripe deletion
    - Toggle: clarifies customer usage impact', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-10 22:11:13', '2025-10-10 22:19:24'),
(161, 'RLXMZ58wLgLGQMBgCqXfftkKjmVi1FjF', 3, 'completed', '2025-10-11 01:30:00', '2025-10-11 06:46:00', 316, 'feat: Add mandatory assessment information popup with type-specific content

  - Implement blocking popup before assessment start (20-30 min message)
  - Custom content for General, PSP, ClearSpace, and Baseline assessments
  - Separate Baseline PRO content for professional athletes
  - Popup persists through refresh until user acknowledges
  - Fully blocking: prevents ESC, F5, back button, outside clicks
  - Responsive design with scrollable content and custom scrollbar
  - Works for both initial and redo assessments
  - Testing all assessments and redo assessments', NULL, '2605:8d80:6a2:a7c1:2122:ae4e:460f:59b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 01:30:38', '2025-10-11 06:46:21'),
(162, 'FJeXGkkqyeZQJFwtfIP3tsBHfFQVdS2W', 5, 'completed', '2025-10-10 23:00:00', '2025-10-11 11:00:00', 720, '12hr shift', NULL, '2605:8d80:681:88e3:8d8c:b57e:4cdb:ead3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 09:54:14', '2025-10-11 09:55:47'),
(163, '1tjhiYVAxH57ejHlSYYha3VlosEFoCPM', 5, 'completed', '2025-10-12 23:00:00', '2025-10-13 11:00:00', 720, '12 hr shift', NULL, '2605:8d80:681:88e3:8d8c:b57e:4cdb:ead3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 09:56:30', '2025-10-11 09:57:54'),
(164, '8OhVuSU1A5XdXVCFG17ebsoUOhK69Y61', 5, 'completed', '2025-10-11 23:00:00', '2025-10-12 11:00:00', 720, '12 hr shift - BMW X3 - $75.85 gas', NULL, '2605:8d80:681:88e3:8d8c:b57e:4cdb:ead3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 09:56:47', '2025-10-21 19:43:03'),
(165, 'V8CkBTCsYkVrhvdachPNiNbTguKLO5EW', 4, 'completed', '2025-09-20 21:45:00', '2025-09-21 12:00:00', 855, '5:45-10pm - brantford civic center 
10:00pm - 8am - applefest', NULL, '2605:8d80:681:2021:acb6:cb49:3659:ecc3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 04:56:00', '2025-10-12 04:58:13'),
(166, 'hUDJscjuZmXT5knlZaCNi58lfN3JFzwm', 4, 'completed', '2025-09-26 21:45:00', '2025-09-27 02:00:00', 255, 'bud stage', NULL, '2605:8d80:681:2021:acb6:cb49:3659:ecc3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 04:59:31', '2025-10-12 05:00:33'),
(167, 'EP3JLC73ZzYGN2Uz4sgR3UfGBpoNEluk', 4, 'completed', '2025-10-09 10:00:00', '2025-10-09 22:45:00', 765, 'October fest - kitchner', NULL, '2605:8d80:681:2021:acb6:cb49:3659:ecc3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 05:00:41', '2025-10-12 05:01:41'),
(168, 'UAHDfXCRwZ8bw54G2Bv8cK2fCKsw27p6', 3, 'completed', '2025-10-12 06:07:00', '2025-10-12 09:58:00', 231, 'Add questionnaire credits system with developer/accreditation display

  - Add migration for developer, accreditation, year_developed columns
  - Create UpdateQuestionnaireCreditsSeeder with 48+ questionnaire credits
  - Add questionnaires:update-credits console command with --force option
  - Create reusable questionnaire-credits Blade component
  - Display credits left of Continue button in patient assessments
  - Update Questionnaire model with new fillable fields
  - Add Credits & Attribution section in questionnaire show view
  - Display developer, year developed, and accreditation info
  - Positioned after Specialities in Basic Information column
  - Only shows when credit data exists (empty credits hidden)', NULL, '2605:8d80:683:165a:c0ac:ad09:b986:cc36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 08:07:26', '2025-10-12 09:59:29'),
(169, '7tCHXdK2H3dat7vuxDOf75U1pgaO5Wwx', 5, 'completed', '2025-10-13 23:00:00', '2025-10-14 11:00:00', 720, '12hr shift', NULL, '170.52.69.37', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/141.0.7390.41 Mobile/15E148 Safari/604.1', '2025-10-13 11:46:04', '2025-10-13 11:47:08'),
(170, 'AwbZQF4cQx7mS6AJs4wT9SIZrQrRrSGH', 3, 'completed', '2025-10-15 03:55:00', '2025-10-15 09:40:00', 345, 'Refactor coupon system to use Stripe API directly with admin enhancements

  Major Changes:
  - Remove local coupons table and migrate to Stripe-only architecture
  - Create migration to drop coupons table, backup old model and CRUD views
  - Simplify routes to only stripe-coupons and products endpoints
  - Update sidebar navigation to point to Stripe coupons page

  Coupon Validation:
  - Implement direct Stripe API validation in payment controllers
  - Add support for coupon lookup by both Stripe ID and friendly name
  - Validate product restrictions using applies_to.products field
  - Add metadata fallback for backward compatibility with older coupons
  - Set Stripe API version to 2024-11-20.acacia for applies_to support
  - Track per-user redemptions to prevent duplicate usage

  Admin Features:
  - Add search by coupon name or ID (case-insensitive)
  - Add filters for status (Valid/Invalid) and product type
  - Implement pagination (20 items per page) for both coupons and redemptions
  - Display color-coded product badges (Assessment/Peer Support)
  - Add clickable redemption count with detailed history modal
  - Show customer name, email, discount, final amount, and redemption date

  Redemption History:
  - Fetch data from Stripe Invoices and Payment Intents
  - Determine payment purpose from invoice line items
  - Implement pagination in modal with Previous/Next navigation
  - Cache customer data to reduce API calls

  Bug Fixes:
  - Fix payment status query (succeeded → completed)
  - Fix route references in products view
  - Fix Stripe API expand depth limit error
  - Fix redemption modal route parameter handling
  - Fix filter button alignment in search form', NULL, '2605:8d80:681:8914:80ee:a4e9:f0a2:5ee2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-14 05:55:44', '2025-10-14 10:07:39'),
(173, 'WTO9HZI0F2wHYs3LmTnvuNX2uWyUv4Dt', 3, 'completed', '2025-10-16 17:28:00', '2025-10-17 03:05:00', 577, 'Add Twilio webhook-based email notifications for unread peer chat messages

  - Implement webhook handler to track message events from Twilio
  - Send email notification after configurable delay (2min test, 30min prod)
  - Skip email if recipient responds before delay expires
  - Use encrypted tokens for secure email deep linking to conversations
  - Add PEER_CHAT_EMAIL_DELAY_MINUTES environment variable
  - Exclude webhook endpoint from CSRF protection
  - Process webhook asynchronously to prevent Twilio timeout
  - Track conversation state (last_message_at, last_message_by, pending_email_job_id)
  - Create UnreadPeerMessageEmail mailable and SendUnreadPeerMessageNotification job', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 19:28:38', '2025-10-17 03:05:21'),
(175, '7de523b3-926e-48f6-a97e-5c3852e7a5f9', 5, 'completed', '2025-10-17 23:00:00', '2025-10-18 11:00:00', 720, '12hr shift', NULL, NULL, NULL, '2025-10-18 07:15:20', '2025-10-18 07:15:20'),
(176, 'fa3c26d2-bb3e-47ab-a0f6-3849fefab80b', 5, 'completed', '2025-10-18 23:00:00', '2025-10-19 11:00:00', 720, '12 hr shift - BMW X3 - $73.35 for gas Tank full', NULL, NULL, NULL, '2025-10-18 07:16:24', '2025-10-21 19:35:51'),
(177, 'a9aa5a70-6e7f-43f7-a5c0-f79fdfce2e39', 5, 'completed', '2025-10-19 23:00:00', '2025-10-20 11:00:00', 720, '12 hr shift - Use own vehicle $30 extra for gas', NULL, NULL, NULL, '2025-10-18 07:16:55', '2025-10-21 19:33:22'),
(179, 'KD1qW5K5jYwccKTYE586QZTnx1lnzg13', 3, 'completed', '2025-10-20 01:17:00', '2025-10-20 06:33:00', 316, 'Pagging and Security measures 
 
 1. XSS Protection - Malicious scripts blocked
  2. Injection Protection - Only whitelisted sources allowed
  3. Clickjacking Protection - Can''t be iframed by attackers
  4. MIME Sniffing Protection - Files execute as declared type
  5. HTTPS Enforcement - Encrypted traffic only (production)
  6. Privacy Protection - Referrer info controlled', NULL, '2605:8d80:6c24:639:bd4b:d7d5:eae6:2c20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 04:17:15', '2025-10-20 05:40:11'),
(180, 'SOfRdS2VKbzLGr0y7HvLnbCO14WRMSLr', 3, 'completed', '2025-10-21 00:13:00', '2025-10-21 05:38:00', 325, 'Fix 14 critical security vulnerabilities in counsellor dashboard

  Security Fixes:
  - IDOR: Add route constraints and assessment scoping
  - SQL Injection: Add numeric validation on route parameters
  - Rate Limiting: Implement throttling on all counsellor endpoints
  - XSS: Remove double encoding, store raw text
  - Mass Assignment: Protect admin fields (is_verified, status, role)
  - Access Control: Restrict directors to assigned assessments only
  - File Upload: Re-encode images, allow JPEG/PNG only, 2MB max
  - CSRF: Enable protection on emergency routes
  - Logging: Add audit trail for PII access and actions
  - Email Enumeration: Add rate limiting and generic responses

  Bug Fixes:
  - Fix "too many redirects" in approve/reject (conflicting $fillable/$guarded)
  - Remove excessive rate limiting causing approval failures', NULL, '2605:8d80:6c23:5729:a984:4236:5e06:a305', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 05:13:16', '2025-10-20 05:39:10'),
(181, '903d3217-83c9-4f6e-bfba-c253d76a47d0', 5, 'completed', '2025-10-21 03:00:00', '2025-10-21 11:00:00', 480, '8 Hr shift - 25$ for using own gas', NULL, NULL, NULL, '2025-10-21 19:31:43', '2025-10-21 19:45:50'),
(182, 'ebfa835b-bebd-45e6-80be-bbb2f6cd7809', 5, 'completed', '2025-10-22 03:00:00', '2025-10-22 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-10-25 11:47:44', '2025-10-25 11:47:44'),
(183, 'bb5cbdde-3811-4808-bc03-4feecfcda0a9', 5, 'completed', '2025-10-23 03:00:00', '2025-10-23 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-10-25 11:48:17', '2025-10-25 11:48:17'),
(184, 'a907ab5e-6bb5-4d44-b655-2a47bbb22364', 5, 'completed', '2025-10-24 03:00:00', '2025-10-24 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-10-25 11:48:43', '2025-10-25 11:48:43'),
(185, '95443f10-93d3-4ef0-9fe9-769b32252cf8', 5, 'completed', '2025-10-25 03:00:00', '2025-10-25 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-10-25 11:49:37', '2025-11-06 21:18:11'),
(186, '973baec9-a3db-44c8-a4ac-7962e9f3bf91', 4, 'completed', '2025-10-26 19:35:00', '2025-10-27 00:35:00', 300, 'Brantford CIVIC center bulldogs', NULL, NULL, NULL, '2025-10-26 00:14:03', '2025-10-26 00:14:03'),
(187, 'oksDWhTE2oYKLfdgzjUil6ddsBbN0w1t', 3, 'completed', '2025-10-27 01:37:00', '2025-10-27 07:45:00', 368, 'Security admin Improvements:
  ✅ XSS protection with proper output escaping
  ✅ Authorization checks on all sensitive operations
  ✅ Rate limiting on all admin endpoints
  ✅ Comprehensive audit logging for compliance
  ✅ Input validation and sanitization
  ✅ Mass assignment protection
  ✅ Admin session timeout (30 minutes)
  ✅ Export size limits
  ✅ Bulk operation limits
  ✅ Protection against admin-on-admin attacks
  ✅ SQL wildcard injection prevention
  ✅ Database transactions for critical operations', NULL, '170.52.69.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 06:37:58', '2025-10-27 07:46:20'),
(188, '1ee49ad4-4810-4523-91ab-0fa7d33b0763', 4, 'completed', '2025-10-29 20:45:00', '2025-10-30 01:15:00', 270, 'Brantford bulldogs - civic center', NULL, NULL, NULL, '2025-10-29 20:32:42', '2025-10-30 01:21:46'),
(189, 'ceb1477f-317c-49a2-a2aa-d81b17c55acb', 5, 'completed', '2025-10-28 03:00:00', '2025-10-28 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:20:03', '2025-11-06 21:20:03'),
(190, '603be75f-fc49-4557-b5f6-cdbfe4af1444', 5, 'completed', '2025-10-29 03:00:00', '2025-10-29 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:20:43', '2025-11-06 21:20:43'),
(191, '3966713e-ad3b-4db6-a995-a5974ccb36fc', 5, 'completed', '2025-10-31 03:00:00', '2025-10-31 11:00:00', 480, '8hr shift + 70$ gas', NULL, NULL, NULL, '2025-11-06 21:22:15', '2025-11-06 21:40:12'),
(192, 'ccf103de-19dc-4019-ac8b-c0bf12799e7e', 5, 'completed', '2025-10-31 03:00:00', '2025-10-31 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:23:20', '2025-11-06 21:23:20'),
(193, '9702d9d7-4b25-4afd-aff0-566a1fddea9f', 5, 'completed', '2025-11-01 03:00:00', '2025-11-01 11:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:24:45', '2025-11-06 21:24:45'),
(194, 'a24017a5-5dd4-47e2-9000-a54b3dcd6423', 5, 'completed', '2025-11-04 04:00:00', '2025-11-04 12:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:25:21', '2025-11-06 21:25:21'),
(195, '4a0a5ab0-9b90-452d-9661-340aa887b054', 5, 'completed', '2025-11-05 04:00:00', '2025-11-05 12:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:26:28', '2025-11-06 21:26:28'),
(196, '04e6e296-ee28-4ad4-b20d-151be675b8f6', 5, 'completed', '2025-11-06 04:00:00', '2025-11-06 12:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:27:23', '2025-11-06 21:27:23'),
(197, 'c4ed9684-bb96-4770-a0d0-9cb52e5e5b37', 5, 'completed', '2025-11-07 04:00:00', '2025-11-07 12:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:37:48', '2025-11-06 21:37:48'),
(198, '8055ba7c-0acd-4d47-bde9-ed7e5ae75a97', 5, 'completed', '2025-11-08 04:00:00', '2025-11-08 12:00:00', 480, '8hr shift', NULL, NULL, NULL, '2025-11-06 21:38:28', '2025-11-06 21:38:28'),
(199, '8cc1dfd0-c1f2-44fc-a29a-2297d2c386e8', 4, 'completed', '2025-11-10 17:50:00', '2025-11-10 22:30:00', 280, 'brantford civic centre bulldogs 12:50 pm to 5:30pm', NULL, NULL, NULL, '2025-11-09 20:08:42', '2025-11-11 04:29:23'),
(227, '5ec25f4b-92a5-4b06-ba54-6bdfdbd392d4', 4, 'completed', '2025-11-14 22:45:00', '2025-11-15 03:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2025-11-15 01:28:21', '2025-11-21 23:14:11'),
(231, 'be0bb4ed-f9bf-45b6-a2f6-44b1774e86af', 5, 'completed', '2025-11-11 04:00:00', '2025-11-11 12:00:00', 480, '8hr', NULL, NULL, NULL, '2025-11-15 05:52:35', '2025-11-15 05:52:35'),
(232, 'b78b29e5-4634-4503-8df1-70d19f253a0c', 5, 'completed', '2025-11-12 04:00:00', '2025-11-12 12:00:00', 480, '8hr', NULL, NULL, NULL, '2025-11-15 05:52:58', '2025-11-15 05:52:58'),
(233, '1679bf3b-82f2-414a-b54d-245754b6d03e', 5, 'completed', '2025-11-13 04:00:00', '2025-11-13 12:00:00', 480, '8hr', NULL, NULL, NULL, '2025-11-15 05:53:21', '2025-11-15 05:53:21'),
(234, 'd2246e33-a154-4bbd-b721-76ae20bbd166', 5, 'completed', '2025-11-14 04:00:00', '2025-11-14 12:00:00', 480, '8hr', NULL, NULL, NULL, '2025-11-15 05:53:43', '2025-11-15 05:53:43'),
(235, '9c9a94ed-33f8-4cea-8bd3-bcc52d5d478a', 5, 'completed', '2025-11-15 04:00:00', '2025-11-15 12:00:00', 480, '8hr', NULL, NULL, NULL, '2025-11-15 05:54:03', '2025-11-15 05:54:03'),
(236, 'f795b9b0-dc71-4f68-bd11-26c7848dd2d9', 4, 'completed', '2025-11-15 19:45:00', '2025-11-16 00:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2025-11-15 13:58:44', '2025-11-15 13:58:44'),
(237, 'd31f4ba6-7743-49dd-8d16-537e1bf493ea', 4, 'completed', '2025-11-21 22:45:00', '2025-11-22 03:30:00', 285, 'bulldogs 5:45pm to 10:30pm', NULL, NULL, NULL, '2025-11-21 23:13:44', '2025-11-21 23:13:44'),
(238, '3799a801-98a0-4b93-824f-9bbee3967a92', 4, 'completed', '2025-11-29 22:55:00', '2025-11-30 03:30:00', 275, 'bulldogs', NULL, NULL, NULL, '2025-11-30 00:14:35', '2025-11-30 00:14:35'),
(239, 'de14b0df-20b0-404a-80c6-737b817966ae', 4, 'completed', '2025-12-03 22:45:00', '2025-12-04 03:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2025-12-03 22:21:29', '2025-12-03 22:21:29'),
(240, '567bdf09-a039-4734-80a8-19bbdc25cd24', 4, 'completed', '2025-12-06 19:45:00', '2025-12-07 00:15:00', 270, 'bulldogs', NULL, NULL, NULL, '2025-12-07 02:13:12', '2025-12-07 02:13:12'),
(241, '5577bebd-aea1-4a88-a74e-ba77881d97ac', 4, 'completed', '2025-12-14 17:45:00', '2025-12-14 22:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2025-12-14 17:50:26', '2025-12-14 17:50:26'),
(242, '077a9a9e-e6ae-4d63-98d4-7babaa45a534', 4, 'completed', '2025-12-30 19:45:00', '2025-12-31 00:15:00', 270, 'bulldogs', NULL, NULL, NULL, '2025-12-30 19:06:02', '2025-12-30 19:06:02'),
(243, '5d022c43-f21f-415e-9507-eb82fa3fd675', 4, 'completed', '2026-01-07 22:45:00', '2026-01-08 03:15:00', 270, 'bulldogs', NULL, NULL, NULL, '2026-01-08 08:35:46', '2026-01-08 08:35:46'),
(244, 'b77186d0-0b32-4c99-b566-5ecf391c1c75', 4, 'completed', '2026-01-16 22:45:00', '2026-01-17 03:15:00', 270, 'bulldogs', NULL, NULL, NULL, '2026-01-16 23:54:11', '2026-01-16 23:54:11'),
(245, '95cb95d3-b5f4-4f88-87d2-376dbfe2b07e', 4, 'completed', '2026-01-17 19:45:00', '2026-01-18 01:45:00', 360, 'bulldogs', NULL, NULL, NULL, '2026-01-17 21:02:49', '2026-01-17 21:02:49'),
(246, '5b6a390c-3153-4e63-8b2f-c1accd12ad88', 4, 'completed', '2026-01-25 17:45:00', '2026-01-25 22:15:00', 270, 'bulldogs', NULL, NULL, NULL, '2026-01-26 00:55:20', '2026-01-26 00:55:20'),
(247, '4f85ada4-d0a8-4406-bb1a-de9ea592fded', 4, 'completed', '2026-02-01 17:45:00', '2026-02-01 22:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2026-02-01 18:08:01', '2026-02-01 18:08:01'),
(248, '33a8f0f8-b068-49ef-9eab-2bf5870884de', 4, 'completed', '2026-02-04 22:45:00', '2026-02-05 03:30:00', 285, 'bulldogs', NULL, NULL, NULL, '2026-02-05 04:31:01', '2026-02-05 04:31:01');
UNLOCK TABLES;

-- ==================================================
-- Backup completed successfully
-- ==================================================
