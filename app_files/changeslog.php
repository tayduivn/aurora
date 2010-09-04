<?php
// DB
// 12 oct 2009 Product Image Bridge =>  Image Bridge
// 13 oct 2009 kbase.Saludation Language Key -> Language Code
// ALTER TABLE `Salutation Dimension` CHANGE `Language Key` `Language Code` VARCHAR( 3 ) NOT NULL DEFAULT 'en'
// UPDATE `kbase`.`Salutation Dimension` SET `Language Code` = 'en' WHERE `Salutation Dimension`.`Language Code` =1 ;
//  UPDATE `kbase`.`Salutation Dimension` SET `Language Code` = 'es' WHERE `Salutation Dimension`.`Language Code` =2 ;

// Edited Country dimsion (regex postal codes)
/*


ALTER TABLE `Purchase Order Dimension` ADD `Purchase Order Cancel Note`  TEXT NULL DEFAULT ''


ALTER TABLE `Purchase Order Dimension` CHANGE `Purchase Order Estimated Receiving Date` `Purchase Order Estimated Receiving Date` DATE NULL DEFAULT NULL 


ALTER TABLE `Purchase Order Dimension` CHANGE `Purchase Order Current Dispatch State` `Purchase Order Current Dispatch State` ENUM( 'In Process', 'Submitted', 'Matched With DN', 'Received', 'Checking', 'Placing in the Warehouse', 'Done', 'Unknown', 'Cancelled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'In Process'
ALTER TABLE `Purchase Order Dimension` CHANGE `Purchase Order Current Dispatch State` `Purchase Order Current Dispatch State` ENUM( 'In Process', 'Submitted', 'Partially Matched With DN', 'Matched With DN', 'Received', 'Checking', 'Placing in the Warehouse', 'Done', 'Unknown', 'Cancelled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'In Process'
ALTER TABLE `Supplier Delivery Note Item Part Bridge` ADD `Done` ENUM( 'Yes', 'No' ) NOT NULL DEFAULT 'No',
ADD `Notes` TEXT NOT NULL ;
ALTER TABLE `Supplier Delivery Note Item Part Bridge` CHANGE `Done` `Done` ENUM( 'Yes', 'No' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'No',
CHANGE `Notes` `Notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `Contact Dimension` ADD `Contact Tax Number` VARCHAR( 64 ) NULL AFTER `Contact Title` ;

0.9.2
ALTER TABLE `Inventory Transaction Fact` ADD `Picked` FLOAT NOT NULL DEFAULT '0' AFTER `Required` ,ADD `Packed` FLOAT NOT NULL DEFAULT '0' AFTER `Picked` ;
ALTER TABLE `Customer Dimension` ADD `Customer Last Payment Method` ENUM( 'Credit Card', 'Cash', 'Paypal', 'Check', 'Bank Transfer', 'Other', 'Unknown' ) NOT NULL DEFAULT 'Unknown' AFTER `Customer Tax Category` , ADD `Customer Usual Payment Method` ENUM( 'Credit Card', 'Cash', 'Paypal', 'Check', 'Bank Transfer', 'Other', 'Unknown' ) NOT NULL DEFAULT 'Unknown' AFTER `Customer Last Payment Method` ;
ALTER TABLE `Inventory Transaction Fact` ADD `Map To Order Transaction Fact` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `Invoice Dimension` ADD `Invoice Tax Shipping Code` VARCHAR( 16 ) NULL DEFAULT NULL AFTER `Invoice Tax Code` ,ADD `Invoice Tax Charges Code` VARCHAR( 16 ) NULL DEFAULT NULL AFTER `Invoice Tax Shipping Code` ;
ALTER TABLE `Invoice Dimension` CHANGE `Invoice Tax Code` `Invoice Tax Code` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'UNK';
ALTER TABLE `Order Transaction Fact` CHANGE `Scheduled Shpping Date` `Scheduled Shipping Date` DATETIME NULL DEFAULT NULL ;
ALTER TABLE `Order Transaction Fact` DROP `Order Line` ,DROP `Delivery Note Line` ,DROP `Invoice Line` ;
ALTER TABLE `Shipping Dimension` CHANGE `Shipping Price Method` `Shipping Price Method` ENUM( 'Parent', 'Flat', 'Step Weight', 'Step Volume', 'Step Order Items Gross Amount', 'On Request' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Flat';
ALTER TABLE `Shipping Dimension` CHANGE `Shipping Destination Code` `Shipping Destination Code` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `Order Transaction Fact` ADD `Payment Method` ENUM( 'Credit Card', 'Cash', 'Paypal', 'Check', 'Bank Transfer', 'Other', 'Unknown', 'NA' ) NOT NULL DEFAULT 'NA' AFTER `Invoice Transaction Outstanding Refund Tax Balance` , ADD `Refund Method` ENUM( 'Credit Card', 'Cash', 'Paypal', 'Check', 'Bank Transfer', 'Other', 'Unknown', 'NA' ) NOT NULL DEFAULT 'NA' AFTER `Payment Method` ;
ALTER TABLE `Invoice Dimension` ADD `Invoice Outstanding Net Balance` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0' AFTER `Invoice Total Amount` , ADD `Invoice Outstanding Tax Balance` DECIMAL( 16, 3 ) NOT NULL DEFAULT '0' AFTER `Invoice Outstanding Net Balance` ;
ALTER TABLE `Order Dimension` CHANGE `Order Current Dispatch State` `Order Current Dispatch State` ENUM( 'In Process by Customer', 'In Process', 'Submitted by Customer', 'Ready to Pick', 'Picking', 'Ready to Pack', 'Ready to Ship', 'Dispatched', 'Unknown', 'Packing', 'Cancelled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Unknown';
ALTER TABLE `Order Transaction Fact` CHANGE `Current Dispatching State` `Current Dispatching State` ENUM( 'In Process by Customer', 'Submitted by Customer', 'In Process', 'Ready to Pick', 'Picking', 'Ready to Pack', 'Ready to Ship', 'Dispatched', 'Unknown', 'Packing', 'Cancelled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Unknown';
ALTER TABLE `Order Dimension` CHANGE `Order Current Dispatch State` `Order Current Dispatch State` ENUM( 'In Process by Customer', 'In Process', 'Submitted by Customer', 'Ready to Pick', 'Picking & Packing', 'Ready to Ship', 'Dispatched', 'Unknown', 'Packing', 'Cancelled' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Unknown';
ALTER TABLE `Tax Category Dimension` DROP PRIMARY KEY ,ADD UNIQUE (`Tax Category Code`);
ALTER TABLE `Tax Category Dimension` ADD `Tax Category Key` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `Store Dimension` ADD `Store Tax Category Code` VARCHAR( 64 ) NULL DEFAULT NULL AFTER `Store Currency Code` ;
ALTER TABLE `Customer Dimension` CHANGE `Customer Tax Category` `Customer Tax Category Code` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Default';
ALTER TABLE `Customer Dimension` CHANGE `Customer Tax Category Code` `Customer Tax Category Code` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `Order Transaction Fact` ADD `Order Transaction Type` ENUM( 'Order', 'Sample', 'Donation', 'Unknown', 'Replacement', 'Other', 'Missing' ) NOT NULL DEFAULT 'Unknown' AFTER `Invoice Date` ;

0.9.3
ALTER TABLE `Campaign Dimension` ADD `Campaign Code` VARCHAR( 64 ) NOT NULL AFTER `Campaign Key` 
*/
?>