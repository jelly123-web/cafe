-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 04:38 PM
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
-- Database: `cafe_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
(1, 'PST', 'Cabang Pusat', '2026-06-01 09:48:09', '2026-06-01 09:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashier_carts`
--

CREATE TABLE `cashier_carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cashier_carts`
--

INSERT INTO `cashier_carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-06-01 19:54:30', '2026-06-01 19:54:30'),
(2, 3, '2026-06-03 00:36:14', '2026-06-03 00:36:14'),
(3, 1, '2026-06-03 03:10:44', '2026-06-03 03:10:44'),
(4, 5, '2026-06-06 01:10:56', '2026-06-06 01:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `cashier_cart_items`
--

CREATE TABLE `cashier_cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cashier_cart_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(14,2) NOT NULL,
  `unit_cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cashier_cart_items`
--

INSERT INTO `cashier_cart_items` (`id`, `cashier_cart_id`, `menu_id`, `qty`, `unit_price`, `unit_cost`, `created_at`, `updated_at`) VALUES
(17, 1, 43, 1, 50000.00, 10000.00, '2026-06-07 00:28:29', '2026-06-07 00:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `cash_flow_entries`
--

CREATE TABLE `cash_flow_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out') NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `happened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `name`, `position`, `phone`, `hire_date`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'EMP-0001', 'satukaki', 'dapur', '081234567890', '2026-06-03', 1, '2026-06-02 22:26:23', '2026-06-02 22:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_packages`
--

CREATE TABLE `food_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `cost_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `free_item` varchar(255) DEFAULT NULL,
  `menu_category_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_packages`
--

INSERT INTO `food_packages` (`id`, `code`, `name`, `description`, `selling_price`, `cost_price`, `image_path`, `created_at`, `updated_at`, `notes`, `free_item`, `menu_category_id`) VALUES
(13, 'PKG-DB2E39', 'paket cinta', '(1x) jonatan luis rikardo\r\n(1x) natalia', 20000.00, 0.00, 'packages/package-7bb05146-122f-4715-b655-69d77fdd7a3d.jpg', '2026-06-02 21:41:14', '2026-06-02 21:41:14', NULL, 'jeslyn', 11),
(14, 'PKG-A64479', 'paket', '(1x) cocacola\r\n(1x) stroberi cake', 20000.00, 0.00, 'packages/package-e5509e9d-530a-48d1-b74c-a00c8857ea40.jpg', '2026-06-04 08:46:51', '2026-06-04 08:46:51', NULL, 'cocacola', 11);

-- --------------------------------------------------------

--
-- Table structure for table `food_package_menu`
--

CREATE TABLE `food_package_menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `food_package_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_package_menu`
--

INSERT INTO `food_package_menu` (`id`, `food_package_id`, `menu_id`, `quantity`, `created_at`, `updated_at`) VALUES
(32, 14, 44, 1, '2026-06-04 08:46:51', '2026-06-05 07:23:59'),
(33, 14, 42, 1, '2026-06-04 08:46:51', '2026-06-05 07:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_categories`
--

CREATE TABLE `inventory_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'bahan',
  `unit` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_categories`
--

INSERT INTO `inventory_categories` (`id`, `name`, `type`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'alat makan', 'barang', 'pack', '2026-06-02 17:52:12', '2026-06-02 17:52:12'),
(2, 'nanas', 'bahan', 'kg', '2026-06-02 18:34:59', '2026-06-02 18:34:59'),
(3, 'apel', 'bahan', 'kg', '2026-06-02 18:36:34', '2026-06-02 18:36:34'),
(4, 'Bahan Kopi', 'bahan', 'kg', '2026-06-02 21:09:39', '2026-06-02 21:09:39'),
(5, 'Bahan Makanan', 'bahan', 'kg', '2026-06-02 21:09:39', '2026-06-02 21:09:39'),
(6, 'Kemasan', 'barang', 'pcs', '2026-06-02 21:09:39', '2026-06-02 21:09:39'),
(7, 'Peralatan', 'barang', 'pcs', '2026-06-02 21:09:39', '2026-06-02 21:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `inventory_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'bahan',
  `unit` varchar(40) NOT NULL DEFAULT 'pcs',
  `stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock_good` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock_less_good` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock_damaged` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('in','out','opname') NOT NULL,
  `stock_condition` varchar(30) DEFAULT NULL,
  `to_stock_condition` varchar(30) DEFAULT NULL,
  `qty` decimal(14,2) NOT NULL,
  `usage_title` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `moved_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `cost_price` decimal(12,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_sold_out` tinyint(1) NOT NULL DEFAULT 0,
  `is_package` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `menu_category_id`, `code`, `barcode`, `name`, `selling_price`, `cost_price`, `image_path`, `is_sold_out`, `is_package`, `created_at`, `updated_at`) VALUES
(41, 12, '8996001600399', '8996001600399', 'le mineral', 20000.00, 0.00, NULL, 0, 0, '2026-06-04 06:37:21', '2026-06-04 06:37:21'),
(42, 13, '3', NULL, 'stroberi cake', 50000.00, 10000.00, 'menus/menu-38afd6eb-116a-4fb6-bf91-b99588d3cb28.jpg', 0, 0, '2026-06-04 08:28:21', '2026-06-04 08:28:21'),
(43, 13, '1', NULL, 'chocolate cake', 50000.00, 10000.00, 'menus/menu-f0d519be-8d1e-4b70-b712-56b7d38855c5.jpg', 0, 0, '2026-06-04 08:30:06', '2026-06-04 08:30:06'),
(44, 12, '2', NULL, 'cocacola', 20000.00, 10000.00, 'menus/menu-74c89641-bbb5-4e58-868a-e56ad7abfb3d.jpg', 0, 0, '2026-06-04 08:32:29', '2026-06-04 21:31:15');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(11, 'paket', 'paket', '2026-06-02 21:17:13', '2026-06-02 21:17:13'),
(12, 'minuman', 'minuman', '2026-06-02 21:17:25', '2026-06-02 21:17:25'),
(13, 'makanan', 'makanan', '2026-06-02 21:19:03', '2026-06-02 21:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `menu_package_items`
--

CREATE TABLE `menu_package_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `item_menu_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_25_090000_add_username_and_role_to_users_table', 1),
(5, '2026_05_25_090001_create_branches_table', 1),
(6, '2026_05_25_090002_create_menus_table', 1),
(7, '2026_05_25_090003_create_sale_transactions_table', 1),
(8, '2026_05_25_090004_create_sale_transaction_items_table', 1),
(9, '2026_05_25_100000_add_access_control_to_users_table', 1),
(10, '2026_05_25_110000_create_menu_categories_table', 1),
(11, '2026_05_25_110001_add_menu_category_and_image_path_to_menus_table', 1),
(12, '2026_05_26_000000_create_system_settings_table', 1),
(13, '2026_05_26_000001_create_tables_table', 1),
(14, '2026_05_26_000002_add_table_id_to_sale_transactions_table', 1),
(15, '2026_05_26_010000_add_notes_to_sale_transactions_table', 1),
(16, '2026_05_26_064717_add_profile_photo_path_to_users_table', 1),
(17, '2026_05_26_140000_add_order_status_to_sale_transactions_table', 1),
(18, '2026_05_26_180000_add_payment_fields_to_sale_transactions_table', 1),
(19, '2026_05_26_183000_add_service_status_to_tables_table', 1),
(20, '2026_05_26_200000_add_is_sold_out_to_menus_table', 1),
(21, '2026_05_26_220000_create_cashier_carts_table', 1),
(22, '2026_05_26_230000_create_employees_table', 1),
(23, '2026_05_26_230100_create_payrolls_table', 1),
(24, '2026_05_27_090000_create_inventory_tables', 1),
(25, '2026_05_27_100000_upgrade_inventory_for_categories_and_stock_conditions', 1),
(26, '2026_05_27_132500_allow_opname_type_in_inventory_movements', 1),
(27, '2026_05_27_140000_create_cash_flow_entries_table', 1),
(28, '2026_06_01_120627_add_indexes_for_performance', 1),
(29, '2026_06_01_143501_add_google_id_and_phone_number_to_users_table', 1),
(30, '2026_06_02_025839_add_is_package_to_menus_table', 2),
(31, '2026_06_02_025842_create_menu_package_items_table', 2),
(32, '2026_06_02_090000_create_food_packages_table', 3),
(33, '2026_06_02_090001_create_food_package_menu_table', 3),
(34, '2026_06_02_161339_add_quantity_to_food_package_menu_table', 4),
(35, '2026_06_02_170428_add_food_package_id_to_sale_transaction_items_table', 5),
(36, '2026_06_03_001915_add_type_to_inventory_categories_table', 6),
(37, '2026_06_03_004901_alter_unit_column_in_inventory_categories_table', 7),
(38, '2026_06_03_023317_add_notes_to_food_packages_table', 8),
(39, '2026_06_03_024104_add_free_item_to_food_packages_table', 9),
(40, '2026_06_03_030935_add_menu_category_id_to_food_packages_table', 10),
(41, '2026_06_03_070000_add_barcode_to_menus_and_inventory_items', 11),
(42, '2026_06_03_122219_create_promos_table', 12),
(43, '2026_06_03_123941_enhance_promos_table_with_scope_and_types', 13),
(44, '2026_06_03_153216_add_min_spend_to_promos_table', 14),
(45, '2026_06_05_043741_increase_payroll_column_size', 15),
(46, '2026_06_05_191500_add_buy_get_targets_to_promos_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `period_month` date NOT NULL,
  `base_salary` decimal(20,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(20,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(20,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(20,2) NOT NULL DEFAULT 0.00,
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `employee_id`, `period_month`, `base_salary`, `allowances`, `deductions`, `net_salary`, `paid_at`, `notes`, `created_at`, `updated_at`) VALUES
(5, 4, '2026-06-01', 100000.00, 100000.00, 0.00, 200000.00, '2026-06-05 04:48:35', NULL, '2026-06-04 21:48:35', '2026-06-04 21:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed_discount','buy_x_get_y','free_shipping') DEFAULT 'percentage',
  `applies_to` enum('all','specific') NOT NULL DEFAULT 'all',
  `value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_spend` decimal(12,2) NOT NULL DEFAULT 0.00,
  `buy_qty` int(11) NOT NULL DEFAULT 0,
  `get_qty` int(11) NOT NULL DEFAULT 0,
  `buy_targets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`buy_targets`)),
  `get_targets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`get_targets`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `banner_path` varchar(255) DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promos`
--

INSERT INTO `promos` (`id`, `name`, `description`, `type`, `applies_to`, `value`, `min_spend`, `buy_qty`, `get_qty`, `buy_targets`, `get_targets`, `is_active`, `banner_path`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES
(7, 'promo a', NULL, 'percentage', 'specific', 10.00, 0.00, 0, 0, '[]', '[]', 1, 'promos/Eai72AQbAgDU3j5FRomnOS4EIlgRNFTmy7cXiYh4.jpg', '2026-06-05 17:00:00', '2026-06-08 17:00:00', '2026-06-05 22:11:27', '2026-06-05 22:12:25'),
(8, 'promo b', NULL, 'fixed_discount', 'specific', 5000.00, 0.00, 0, 0, '[]', '[]', 1, 'promos/KVffY3GJHjV4cnyZE4GoCVrsxnbTvcasfPI9EdVZ.jpg', '2026-06-05 17:00:00', '2026-06-08 17:00:00', '2026-06-05 22:50:42', '2026-06-05 22:50:42'),
(9, 'promo c', NULL, 'buy_x_get_y', 'specific', 0.00, 0.00, 2, 1, '[{\"kind\":\"menu\",\"id\":43,\"qty\":2}]', '[{\"kind\":\"menu\",\"id\":44,\"qty\":1}]', 1, 'promos/iuy9ZGJxs57WfAO5xQJhrEPLC5mAAnh3SIA7vSzx.jpg', '2026-06-05 17:00:00', '2026-06-08 17:00:00', '2026-06-05 23:15:53', '2026-06-05 23:19:16');

-- --------------------------------------------------------

--
-- Table structure for table `promo_food_package`
--

CREATE TABLE `promo_food_package` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `promo_id` bigint(20) UNSIGNED NOT NULL,
  `food_package_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_menu`
--

CREATE TABLE `promo_menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `promo_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo_menu`
--

INSERT INTO `promo_menu` (`id`, `promo_id`, `menu_id`) VALUES
(13, 7, 42),
(14, 8, 42),
(15, 9, 43);

-- --------------------------------------------------------

--
-- Table structure for table `sale_transactions`
--

CREATE TABLE `sale_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `sold_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_transactions`
--

INSERT INTO `sale_transactions` (`id`, `branch_id`, `table_id`, `code`, `sold_at`, `total_amount`, `total_cost`, `notes`, `status`, `cancelled_at`, `cancelled_by`, `paid_at`, `payment_method`, `created_at`, `updated_at`) VALUES
(36, 1, NULL, 'TRX-0001', '2026-06-06 13:11:21', 20000.00, 0.00, 'Scan barcode pembayaran', 'cancelled', '2026-06-06 06:11:21', 5, NULL, NULL, '2026-06-04 19:42:34', '2026-06-06 06:11:21'),
(37, 1, 62, 'TRX-0002', '2026-06-06 12:54:17', 50000.00, 10000.00, 'chocolate cake x1', 'cancelled', '2026-06-06 05:54:17', 5, NULL, NULL, '2026-06-06 05:40:39', '2026-06-06 05:54:17'),
(38, 1, 62, 'TRX-0003', '2026-06-09 11:34:11', 20000.00, 0.00, 'le mineral x1', 'paid', NULL, NULL, '2026-06-09 04:34:10', 'qris', '2026-06-06 06:37:06', '2026-06-09 04:34:10'),
(39, 1, 62, 'TRX-0004', '2026-06-07 06:01:15', 20000.00, 0.00, 'Dengan jelita ini kulit aku lagi pulsa aku pakai apa varian apa itu mimpi yang bisa bikin kulit jadi lebih cerah dan skin tetap terjaga yang tekstur umum boleh didatangkan dan lembut banget tinggal berwali karena kulit ini ph nya ada tiap kali itu tandanya lebih jempol dan aman banget buat sensitif || paket x1', 'processing', NULL, NULL, NULL, NULL, '2026-06-06 18:26:05', '2026-06-06 23:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `sale_transaction_items`
--

CREATE TABLE `sale_transaction_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_transaction_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `food_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `line_cost` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_transaction_items`
--

INSERT INTO `sale_transaction_items` (`id`, `sale_transaction_id`, `menu_id`, `food_package_id`, `qty`, `unit_price`, `unit_cost`, `line_total`, `line_cost`, `created_at`, `updated_at`) VALUES
(62, 36, 41, NULL, 1, 20000.00, 0.00, 20000.00, 0.00, '2026-06-04 19:42:34', '2026-06-04 19:42:34'),
(63, 37, 43, NULL, 1, 50000.00, 10000.00, 50000.00, 10000.00, '2026-06-06 05:40:39', '2026-06-06 05:40:39'),
(64, 38, 41, NULL, 1, 20000.00, 0.00, 20000.00, 0.00, '2026-06-06 06:37:06', '2026-06-06 06:37:06'),
(65, 39, NULL, 14, 1, 20000.00, 0.00, 20000.00, 0.00, '2026-06-06 18:26:05', '2026-06-06 18:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7Q9cL9XmxXJ8SG5OtsYsbTUEji0VbNrwlBAtf3am', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnZ6dXgxRTViaFZwYXlRcnJKUDB0a1BmYjU2SU91eVUzZko2WUdNOSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1780727634),
('nN9tiJ5eBp09rYm2tqwIfJ3VftbagCPOjwfmOHso', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSXAwS1pZMTNGYzNydlRSVk10SVhod1RvUDR5U1NHVWo0YmtqYmRSSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvc3VwZXJhZG1pbi91c2VycyI7czo1OiJyb3V0ZSI7czoyMjoic3VwZXJhZG1pbi51c2Vycy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1780729323),
('ZNPoZmYNsARgV0rMJIN0y8VAtTakUfuSUUb9q4KR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidVJadmJZT1F2MkNBUVFsaHhqOXFOeXJ3ajdGTFhzbEJUekE2bldiYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvc3VwZXJhZG1pbi91c2VycyI7czo1OiJyb3V0ZSI7czoyMjoic3VwZXJhZG1pbi51c2Vycy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1780729755);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'cafe_name', 'cafecaf', '2026-06-01 09:48:06', '2026-06-05 08:51:03'),
(2, 'cafe_logo', 'system/Y4qi6DbBXpIN9oYqx4cAPVCUr071bH88vmdRGvon.jpg', '2026-06-01 09:48:06', '2026-06-06 01:44:22'),
(3, 'cafe_phone', '+62 812-3456-7890', '2026-06-02 21:09:33', '2026-06-02 21:09:33'),
(4, 'hero_banner_tag', 'hello', '2026-06-06 01:20:16', '2026-06-06 01:20:16'),
(5, 'hero_banner_title', 'hari ini ada promo ^^', '2026-06-06 01:20:16', '2026-06-06 01:21:20'),
(6, 'hero_banner_desc', 'ayok di beli di beli ^^', '2026-06-06 01:20:16', '2026-06-06 01:21:20'),
(7, 'hero_banner_button_text', 'Lihat Promo', '2026-06-06 01:20:16', '2026-06-06 01:20:16'),
(8, 'hero_banner_image', 'system/hero/FNTNMyjeczi4nXwQTjyw8tFlu1fFWBZFrYpc3jVt.jpg', '2026-06-06 01:21:21', '2026-06-06 01:21:21');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `qr_token` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `service_status` varchar(255) NOT NULL DEFAULT 'empty',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `number`, `name`, `qr_token`, `is_active`, `service_status`, `created_at`, `updated_at`) VALUES
(62, '1', 'meja 1', '8c86c0e0-6eec-458d-ae06-64b8dba1e4d3', 1, 'occupied', '2026-06-04 23:37:41', '2026-06-06 18:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `name`, `username`, `email`, `phone_number`, `profile_photo_path`, `email_verified_at`, `password`, `role`, `is_active`, `permissions`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super Admin', 'superadmin', 'superadmin@cafe.local', NULL, NULL, '2026-06-02 22:30:45', '$2y$12$Xpgg7ml/IvWK3tzVOENIs.VNS2H2CH7Mmm7kW70MYng9d2PQ3L/u.', 'superadmin', 1, '{\"superadmin_dashboard\":true,\"superadmin_users\":true,\"superadmin_access\":true,\"superadmin_menus\":true,\"superadmin_employees\":true,\"superadmin_payrolls\":true,\"superadmin_menu_categories\":true,\"superadmin_tables\":true,\"superadmin_reports\":true,\"superadmin_settings\":true,\"cashier_orders\":true,\"cashier_transactions\":true,\"cashier_payments\":true,\"cashier_receipts\":true,\"cashier_tables\":true,\"cashier_reports\":true,\"kitchen_orders\":true,\"kitchen_history\":true,\"kitchen_menus\":true,\"inventory_index\":true,\"inventory_movement\":true,\"leader_monitoring\":true,\"leader_cashflow\":true}', NULL, '2026-06-01 09:48:07', '2026-06-02 22:30:46'),
(2, NULL, 'kasir', 'kasir', 'kasir@cafe.local', NULL, NULL, '2026-06-02 22:30:46', '$2y$12$HmqWUD3lfA6m7Umk9x70Du0C/WpYoCf8tyb7kOsTsyFcT8iqwcuDi', 'leader_cashier', 1, '{\"cashier_orders\":true,\"cashier_transactions\":true,\"cashier_payments\":true,\"cashier_receipts\":true,\"cashier_tables\":true,\"cashier_reports\":true}', NULL, '2026-06-01 09:48:07', '2026-06-06 07:33:24'),
(3, NULL, 'dapur', 'dapur', 'dapur@cafe.local', NULL, NULL, '2026-06-01 09:48:07', '$2y$12$Z6TmOYJOkHimbavMJ69hZuDviLNltdpDSTDNSyE6arZzbZkwWAsrS', 'kitchen', 1, '{\"superadmin_dashboard\":false,\"superadmin_users\":false,\"superadmin_access\":false,\"superadmin_menus\":false,\"superadmin_employees\":false,\"superadmin_payrolls\":false,\"superadmin_menu_categories\":false,\"superadmin_tables\":false,\"superadmin_reports\":false,\"superadmin_settings\":false,\"cashier_orders\":false,\"cashier_transactions\":false,\"cashier_payments\":false,\"cashier_receipts\":false,\"cashier_tables\":false,\"cashier_reports\":false,\"kitchen_orders\":true,\"kitchen_history\":true,\"kitchen_menus\":true,\"inventory_index\":false,\"inventory_movement\":false,\"leader_monitoring\":false,\"leader_cashflow\":false}', NULL, '2026-06-01 09:48:08', '2026-06-06 00:15:51'),
(4, NULL, 'gudang', 'gudang', 'gudang@cafe.local', NULL, NULL, '2026-06-01 09:48:08', '$2y$12$OVzXQ8j43ny7Sy0SDGEQUuPiOCQj9DahmuoBWnCTM2mrWQPK/hedy', 'inventory', 1, '{\"superadmin_dashboard\":false,\"superadmin_users\":false,\"superadmin_access\":false,\"superadmin_menus\":false,\"superadmin_employees\":false,\"superadmin_payrolls\":false,\"superadmin_menu_categories\":false,\"superadmin_tables\":false,\"superadmin_reports\":false,\"superadmin_settings\":false,\"cashier_orders\":false,\"cashier_transactions\":false,\"cashier_payments\":false,\"cashier_receipts\":false,\"cashier_tables\":false,\"cashier_reports\":false,\"kitchen_orders\":false,\"kitchen_history\":false,\"kitchen_menus\":false,\"inventory_index\":true,\"inventory_movement\":true,\"leader_monitoring\":false,\"leader_cashflow\":false}', NULL, '2026-06-01 09:48:08', '2026-06-06 00:15:26'),
(5, NULL, 'Leader Kasir', 'leaderkasir', 'leaderkasir@cafe.local', NULL, NULL, '2026-06-01 09:48:08', '$2y$12$wx7wcdv8UWv5YMmQloxF3eZzeNZx4IaLZhVPti4yYmh.iDOvWYwsa', 'leader_cashier', 1, '{\"superadmin_dashboard\":false,\"superadmin_users\":false,\"superadmin_access\":false,\"superadmin_menus\":false,\"superadmin_employees\":false,\"superadmin_payrolls\":false,\"superadmin_menu_categories\":false,\"superadmin_tables\":false,\"superadmin_reports\":false,\"superadmin_settings\":false,\"cashier_orders\":true,\"cashier_transactions\":true,\"cashier_payments\":true,\"cashier_receipts\":true,\"cashier_tables\":true,\"cashier_reports\":true,\"kitchen_orders\":false,\"kitchen_history\":false,\"kitchen_menus\":false,\"inventory_index\":false,\"inventory_movement\":false,\"leader_monitoring\":true,\"leader_cashflow\":true}', NULL, '2026-06-01 09:48:09', '2026-06-06 00:16:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cashier_carts`
--
ALTER TABLE `cashier_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cashier_carts_user_id_unique` (`user_id`);

--
-- Indexes for table `cashier_cart_items`
--
ALTER TABLE `cashier_cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cashier_cart_items_cashier_cart_id_menu_id_unique` (`cashier_cart_id`,`menu_id`),
  ADD KEY `cashier_cart_items_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `cash_flow_entries`
--
ALTER TABLE `cash_flow_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_flow_entries_created_by_foreign` (`created_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `food_packages`
--
ALTER TABLE `food_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `food_packages_code_unique` (`code`),
  ADD KEY `food_packages_menu_category_id_foreign` (`menu_category_id`);

--
-- Indexes for table `food_package_menu`
--
ALTER TABLE `food_package_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `food_package_menu_food_package_id_menu_id_unique` (`food_package_id`,`menu_id`),
  ADD KEY `food_package_menu_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_items_barcode_unique` (`barcode`),
  ADD KEY `inventory_items_inventory_category_id_foreign` (`inventory_category_id`);

--
-- Indexes for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_movements_inventory_item_id_foreign` (`inventory_item_id`),
  ADD KEY `inventory_movements_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_code_unique` (`code`),
  ADD UNIQUE KEY `menus_barcode_unique` (`barcode`),
  ADD KEY `menus_menu_category_id_foreign` (`menu_category_id`),
  ADD KEY `menus_is_sold_out_index` (`is_sold_out`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_categories_name_unique` (`name`),
  ADD UNIQUE KEY `menu_categories_slug_unique` (`slug`);

--
-- Indexes for table `menu_package_items`
--
ALTER TABLE `menu_package_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_package_items_menu_id_foreign` (`menu_id`),
  ADD KEY `menu_package_items_item_menu_id_foreign` (`item_menu_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payrolls_employee_id_period_month_unique` (`employee_id`,`period_month`),
  ADD KEY `payrolls_employee_id_index` (`employee_id`),
  ADD KEY `payrolls_paid_at_index` (`paid_at`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_food_package`
--
ALTER TABLE `promo_food_package`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_food_package_promo_id_foreign` (`promo_id`),
  ADD KEY `promo_food_package_food_package_id_foreign` (`food_package_id`);

--
-- Indexes for table `promo_menu`
--
ALTER TABLE `promo_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_menu_promo_id_foreign` (`promo_id`),
  ADD KEY `promo_menu_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `sale_transactions`
--
ALTER TABLE `sale_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_transactions_code_unique` (`code`),
  ADD KEY `sale_transactions_sold_at_index` (`sold_at`),
  ADD KEY `sale_transactions_table_id_foreign` (`table_id`),
  ADD KEY `sale_transactions_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `sale_transactions_branch_id_status_index` (`branch_id`,`status`);

--
-- Indexes for table `sale_transaction_items`
--
ALTER TABLE `sale_transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_transaction_items_sale_transaction_id_index` (`sale_transaction_id`),
  ADD KEY `sale_transaction_items_menu_id_index` (`menu_id`),
  ADD KEY `sale_transaction_items_food_package_id_foreign` (`food_package_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_number_unique` (`number`),
  ADD UNIQUE KEY `tables_qr_token_unique` (`qr_token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cashier_carts`
--
ALTER TABLE `cashier_carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cashier_cart_items`
--
ALTER TABLE `cashier_cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `cash_flow_entries`
--
ALTER TABLE `cash_flow_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_packages`
--
ALTER TABLE `food_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `food_package_menu`
--
ALTER TABLE `food_package_menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu_package_items`
--
ALTER TABLE `menu_package_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `promo_food_package`
--
ALTER TABLE `promo_food_package`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `promo_menu`
--
ALTER TABLE `promo_menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sale_transactions`
--
ALTER TABLE `sale_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `sale_transaction_items`
--
ALTER TABLE `sale_transaction_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cashier_carts`
--
ALTER TABLE `cashier_carts`
  ADD CONSTRAINT `cashier_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cashier_cart_items`
--
ALTER TABLE `cashier_cart_items`
  ADD CONSTRAINT `cashier_cart_items_cashier_cart_id_foreign` FOREIGN KEY (`cashier_cart_id`) REFERENCES `cashier_carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cashier_cart_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_flow_entries`
--
ALTER TABLE `cash_flow_entries`
  ADD CONSTRAINT `cash_flow_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `food_packages`
--
ALTER TABLE `food_packages`
  ADD CONSTRAINT `food_packages_menu_category_id_foreign` FOREIGN KEY (`menu_category_id`) REFERENCES `menu_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `food_package_menu`
--
ALTER TABLE `food_package_menu`
  ADD CONSTRAINT `food_package_menu_food_package_id_foreign` FOREIGN KEY (`food_package_id`) REFERENCES `food_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `food_package_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_inventory_category_id_foreign` FOREIGN KEY (`inventory_category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `inventory_movements_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_menu_category_id_foreign` FOREIGN KEY (`menu_category_id`) REFERENCES `menu_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_package_items`
--
ALTER TABLE `menu_package_items`
  ADD CONSTRAINT `menu_package_items_item_menu_id_foreign` FOREIGN KEY (`item_menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_package_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promo_food_package`
--
ALTER TABLE `promo_food_package`
  ADD CONSTRAINT `promo_food_package_food_package_id_foreign` FOREIGN KEY (`food_package_id`) REFERENCES `food_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promo_food_package_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promo_menu`
--
ALTER TABLE `promo_menu`
  ADD CONSTRAINT `promo_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promo_menu_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_transactions`
--
ALTER TABLE `sale_transactions`
  ADD CONSTRAINT `sale_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_transactions_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_transactions_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_transaction_items`
--
ALTER TABLE `sale_transaction_items`
  ADD CONSTRAINT `sale_transaction_items_food_package_id_foreign` FOREIGN KEY (`food_package_id`) REFERENCES `food_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_transaction_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_transaction_items_sale_transaction_id_foreign` FOREIGN KEY (`sale_transaction_id`) REFERENCES `sale_transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
