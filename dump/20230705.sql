-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Jul 05, 2023 at 07:48 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_fakturujonline`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `vat_number` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `zip_code` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `name`, `phone`, `email`, `business_id`, `tax_id`, `vat_number`, `city`, `street`, `zip_code`, `country_code`, `created_at`, `updated_at`) VALUES
(1, 'Alza', '601123123', 'ajax770sk@gmail.com', 36562939, 'CZ36562939', '', 'test', 'test', '12345', 'SK', '2023-07-05 19:39:03', '2023-07-05 19:39:03'),
(2, 'Alza', '601123123', 'ajax770sk@gmail.com', 36562939, 'CZ36562939', '', 'test', 'test', '12345', 'AF', '2023-07-05 19:48:31', '2023-07-05 19:48:31'),
(3, 'Hej s.r.o.', '12345678', 'test@test.cz', 2121026006, 'SK2121026006', '', 'Test', 'Test', '12345', 'SK', '2023-07-05 19:48:31', '2023-07-05 19:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `bank_account`
--

CREATE TABLE `bank_account` (
  `id` int(11) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `swift` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bank_account`
--

INSERT INTO `bank_account` (`id`, `account_number`, `iban`, `swift`, `created_at`, `updated_at`) VALUES
(1, '', '', '', '2023-07-05 19:39:03', '2023-07-05 19:39:03'),
(2, '', '', '', '2023-07-05 19:48:31', '2023-07-05 19:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `billing_address_id` int(11) NOT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `billing_same_as_shipping` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `line_item`
--

CREATE TABLE `line_item` (
  `id` int(11) NOT NULL,
  `tax_document_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price_tax_excl` decimal(20,2) NOT NULL,
  `unit_tax_total` decimal(20,2) NOT NULL,
  `total_price_tax_excl` decimal(20,2) NOT NULL,
  `total_tax` decimal(20,2) NOT NULL,
  `tax_rate` decimal(20,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `line_item`
--

INSERT INTO `line_item` (`id`, `tax_document_id`, `name`, `type`, `unit`, `quantity`, `unit_price_tax_excl`, `unit_tax_total`, `total_price_tax_excl`, `total_tax`, `tax_rate`, `created_at`, `updated_at`) VALUES
(1, 1, 'test', 'line_item', '', 1, '20.00', '0.00', '20.00', '0.00', '0.00', '2023-07-05 19:48:31', '2023-07-05 19:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20230604', '2023-07-05 17:35:45'),
('20230618', '2023-07-05 17:35:52'),
('20230702', '2023-07-05 17:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `billing_address_id` int(11) NOT NULL,
  `number` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `locale_code` varchar(255) NOT NULL,
  `total_price_tax_excl` decimal(20,2) NOT NULL,
  `total_price_tax_incl` decimal(20,2) NOT NULL,
  `total_tax` decimal(20,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `_order_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price_tax_excl` decimal(20,2) NOT NULL,
  `unit_tax_total` decimal(20,2) NOT NULL,
  `total_price_tax_excl` decimal(20,2) NOT NULL,
  `total_tax` decimal(20,2) NOT NULL,
  `tax_rate` decimal(20,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `_order_id` int(11) NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `stripe_payment_intent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_data`
--

CREATE TABLE `payment_data` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `paypal_mail` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `bank_account_iban` varchar(255) DEFAULT NULL,
  `bank_account_swift` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payment_data`
--

INSERT INTO `payment_data` (`id`, `type`, `paypal_mail`, `bank_account_number`, `bank_account_iban`, `bank_account_swift`, `created_at`, `updated_at`) VALUES
(1, 'bank_payment', NULL, '', '', '', '2023-07-05 19:48:31', '2023-07-05 19:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `_order_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `start_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `end_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_document`
--

CREATE TABLE `tax_document` (
  `id` int(11) NOT NULL,
  `user_company_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `supplier_billing_address_id` int(11) NOT NULL,
  `subscriber_billing_address_id` int(11) NOT NULL,
  `payment_data_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transfered_tax_liability` tinyint(1) NOT NULL,
  `vat_payer` tinyint(1) NOT NULL,
  `number` varchar(255) NOT NULL,
  `constant_symbol` varchar(255) DEFAULT NULL,
  `specific_symbol` varchar(255) DEFAULT NULL,
  `currency_code` varchar(255) NOT NULL,
  `locale_code` varchar(255) NOT NULL,
  `total_price_tax_excl` decimal(20,2) NOT NULL,
  `total_price_tax_incl` decimal(20,2) NOT NULL,
  `note_above_items` longtext DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `paid_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `issued_by` varchar(255) DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivery_date_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `due_date_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tax_document`
--

INSERT INTO `tax_document` (`id`, `user_company_id`, `contact_id`, `bank_account_id`, `supplier_billing_address_id`, `subscriber_billing_address_id`, `payment_data_id`, `type`, `transfered_tax_liability`, `vat_payer`, `number`, `constant_symbol`, `specific_symbol`, `currency_code`, `locale_code`, `total_price_tax_excl`, `total_price_tax_incl`, `note_above_items`, `note`, `sent_at`, `paid_at`, `issued_by`, `issued_at`, `delivery_date_at`, `due_date_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 2, 2, 3, 1, 'invoice', 1, 1, '1', '', '', 'EUR', 'SK', '20.00', '20.00', '', '', NULL, NULL, 'Test', '2023-07-05 00:00:00', '2023-07-05 00:00:00', '2023-07-19 00:00:00', '2023-07-05 19:48:31', '2023-07-05 19:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_valid_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `phone_number`, `reset_token`, `reset_token_valid_at`) VALUES
(1, 'Jakub', 'Staňo', 'ajax770sk@gmail.com', '$2y$10$iztG5ZXSBKXDtbbvZU3BX.3WrrWy4FojwFuGt16hHEpJMg9Pi/TRC', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_company`
--

CREATE TABLE `user_company` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `billing_address_id` int(11) NOT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `vat_payer` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `billing_same_as_shipping` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_company`
--

INSERT INTO `user_company` (`id`, `user_id`, `billing_address_id`, `shipping_address_id`, `bank_account_id`, `vat_payer`, `name`, `billing_same_as_shipping`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 1, 0, 'Alza', 1, '2023-07-05 19:39:03', '2023-07-05 19:39:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_account`
--
ALTER TABLE `bank_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4C62E638A76ED395` (`user_id`),
  ADD KEY `IDX_4C62E63879D0C0E4` (`billing_address_id`),
  ADD KEY `IDX_4C62E6384D4CFF2B` (`shipping_address_id`),
  ADD KEY `IDX_4C62E63812CB990C` (`bank_account_id`);

--
-- Indexes for table `line_item`
--
ALTER TABLE `line_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9456D6C74C817138` (`tax_document_id`);

--
-- Indexes for table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F5299398A76ED395` (`user_id`),
  ADD KEY `IDX_F529939879D0C0E4` (`billing_address_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F09A35F2858` (`_order_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6D28840DA35F2858` (`_order_id`);

--
-- Indexes for table `payment_data`
--
ALTER TABLE `payment_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A3C664D3A76ED395` (`user_id`),
  ADD KEY `IDX_A3C664D3A35F2858` (`_order_id`);

--
-- Indexes for table `tax_document`
--
ALTER TABLE `tax_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_38FD2BC830FCDC3A` (`user_company_id`),
  ADD KEY `IDX_38FD2BC8E7A1254A` (`contact_id`),
  ADD KEY `IDX_38FD2BC812CB990C` (`bank_account_id`),
  ADD KEY `IDX_38FD2BC8321994A4` (`supplier_billing_address_id`),
  ADD KEY `IDX_38FD2BC847A80B0D` (`subscriber_billing_address_id`),
  ADD KEY `IDX_38FD2BC82EBCAFD6` (`payment_data_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- Indexes for table `user_company`
--
ALTER TABLE `user_company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_17B21745A76ED395` (`user_id`),
  ADD KEY `IDX_17B2174579D0C0E4` (`billing_address_id`),
  ADD KEY `IDX_17B217454D4CFF2B` (`shipping_address_id`),
  ADD KEY `IDX_17B2174512CB990C` (`bank_account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bank_account`
--
ALTER TABLE `bank_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `line_item`
--
ALTER TABLE `line_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_data`
--
ALTER TABLE `payment_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_document`
--
ALTER TABLE `tax_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_company`
--
ALTER TABLE `user_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `FK_4C62E63812CB990C` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`),
  ADD CONSTRAINT `FK_4C62E6384D4CFF2B` FOREIGN KEY (`shipping_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_4C62E63879D0C0E4` FOREIGN KEY (`billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_4C62E638A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `line_item`
--
ALTER TABLE `line_item`
  ADD CONSTRAINT `FK_9456D6C74C817138` FOREIGN KEY (`tax_document_id`) REFERENCES `tax_document` (`id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F529939879D0C0E4` FOREIGN KEY (`billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F09A35F2858` FOREIGN KEY (`_order_id`) REFERENCES `order` (`id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `FK_6D28840DA35F2858` FOREIGN KEY (`_order_id`) REFERENCES `order` (`id`);

--
-- Constraints for table `subscription`
--
ALTER TABLE `subscription`
  ADD CONSTRAINT `FK_A3C664D3A35F2858` FOREIGN KEY (`_order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `FK_A3C664D3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `tax_document`
--
ALTER TABLE `tax_document`
  ADD CONSTRAINT `FK_38FD2BC812CB990C` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`),
  ADD CONSTRAINT `FK_38FD2BC82EBCAFD6` FOREIGN KEY (`payment_data_id`) REFERENCES `payment_data` (`id`),
  ADD CONSTRAINT `FK_38FD2BC830FCDC3A` FOREIGN KEY (`user_company_id`) REFERENCES `user_company` (`id`),
  ADD CONSTRAINT `FK_38FD2BC8321994A4` FOREIGN KEY (`supplier_billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_38FD2BC847A80B0D` FOREIGN KEY (`subscriber_billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_38FD2BC8E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`);

--
-- Constraints for table `user_company`
--
ALTER TABLE `user_company`
  ADD CONSTRAINT `FK_17B2174512CB990C` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`),
  ADD CONSTRAINT `FK_17B217454D4CFF2B` FOREIGN KEY (`shipping_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_17B2174579D0C0E4` FOREIGN KEY (`billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_17B21745A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
