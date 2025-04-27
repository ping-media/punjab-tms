<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
    public function up()
    {
        // Create trip invoices table
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "trip_invoices` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_number` varchar(100) NOT NULL,
            `trip_reference` varchar(100) NOT NULL,
            `invoice_date` date NOT NULL,
            `due_date` date NOT NULL,
            `status` varchar(20) NOT NULL DEFAULT 'draft',
            `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
            `total_tax` decimal(15,2) NOT NULL DEFAULT '0.00',
            `total` decimal(15,2) NOT NULL DEFAULT '0.00',
            `notes` text,
            `created_at` datetime NOT NULL,
            `created_by` int(11) NOT NULL,
            `updated_at` datetime DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `invoice_number` (`invoice_number`),
            KEY `trip_reference` (`trip_reference`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Create trip invoice items table
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "trip_invoice_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `item` varchar(255) NOT NULL,
            `description` text,
            `qty` decimal(15,2) NOT NULL DEFAULT '0.00',
            `rate` decimal(15,2) NOT NULL DEFAULT '0.00',
            `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
            `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
            PRIMARY KEY (`id`),
            KEY `invoice_id` (`invoice_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Add foreign key constraint
        $this->db->query("ALTER TABLE `" . db_prefix() . "trip_invoice_items`
            ADD CONSTRAINT `trip_invoice_items_ibfk_1` 
            FOREIGN KEY (`invoice_id`) 
            REFERENCES `" . db_prefix() . "trip_invoices` (`id`) 
            ON DELETE CASCADE");
    }

    public function down()
    {
        // Drop trip invoice items table
        $this->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "trip_invoice_items`");
        
        // Drop trip invoices table
        $this->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "trip_invoices`");
    }
} 