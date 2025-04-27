<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'vehicles')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'vehicles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `vehicle_number` varchar(50) NOT NULL,
        `vehicle_type` varchar(50) NOT NULL,
        `model` varchar(100) NOT NULL,
        `capacity` varchar(50) NULL,
        `status` varchar(20) DEFAULT "active",
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'drivers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'drivers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `license_number` varchar(50) NOT NULL,
        `license_type` varchar(50) DEFAULT NULL,
        `license_expiry` date DEFAULT NULL,
        `experience_years` int(11) DEFAULT 0,
        `joining_date` date DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        `emergency_contact` varchar(191) DEFAULT NULL,
        `emergency_phone` varchar(50) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

if (!$CI->db->table_exists(db_prefix() . 'trips')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'trips` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `trip_number` varchar(20) NOT NULL,
        `consignor_id` int(11) NOT NULL,
        `driver_id` int(11) NOT NULL,
        `vehicle_id` int(11) DEFAULT NULL,
        `start_location` varchar(191) NOT NULL,
        `end_location` varchar(191) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `status` varchar(20) DEFAULT "planned",
        `distance` decimal(10,2) DEFAULT 0.00,
        `fuel_cost` decimal(15,2) DEFAULT 0.00,
        `other_expenses` decimal(15,2) DEFAULT 0.00,
        `total_cost` decimal(15,2) DEFAULT 0.00,
        `notes` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `consignor_id` (`consignor_id`),
        KEY `driver_id` (`driver_id`),
        KEY `vehicle_id` (`vehicle_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

if (!$CI->db->table_exists(db_prefix() . 'consignors')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'consignors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `company` varchar(191) NOT NULL,
        `contact_person` varchar(191) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `state` varchar(100) DEFAULT NULL,
        `postal_code` varchar(20) DEFAULT NULL,
        `country` varchar(100) DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

// Add foreign key constraints
$CI->db->query('ALTER TABLE `' . db_prefix() . 'drivers`
    ADD CONSTRAINT `' . db_prefix() . 'drivers_ibfk_1` 
    FOREIGN KEY (`staff_id`) 
    REFERENCES `' . db_prefix() . 'staff` (`staffid`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;');

$CI->db->query('ALTER TABLE `' . db_prefix() . 'trips`
    ADD CONSTRAINT `' . db_prefix() . 'trips_ibfk_1` 
    FOREIGN KEY (`consignor_id`) 
    REFERENCES `' . db_prefix() . 'consignors` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;');

$CI->db->query('ALTER TABLE `' . db_prefix() . 'trips`
    ADD CONSTRAINT `' . db_prefix() . 'trips_ibfk_2` 
    FOREIGN KEY (`driver_id`) 
    REFERENCES `' . db_prefix() . 'drivers` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;');

// Add options
add_option('next_trip_number', 1); 