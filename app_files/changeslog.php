<?php
// DB
// 12 oct 2009 Product Image Bridge =>  Image Bridge
// 13 oct 2009 kbase.Saludation Language Key -> Language Code
// ALTER TABLE `Salutation Dimension` CHANGE `Language Key` `Language Code` VARCHAR( 3 ) NOT NULL DEFAULT 'en'
// UPDATE `kbase`.`Salutation Dimension` SET `Language Code` = 'en' WHERE `Salutation Dimension`.`Language Code` =1 ;
//  UPDATE `kbase`.`Salutation Dimension` SET `Language Code` = 'es' WHERE `Salutation Dimension`.`Language Code` =2 ;

// Edited Country dimsion (regex postal codes)
/*

ALTER TABLE `Product Department Dimension` ADD `Product Department Total Customers` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department Total Days Available` 
ALTER TABLE `Product Department Dimension` ADD `Product Department Total Invoices` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department Total Days Available` 
ALTER TABLE `Product Department Dimension` ADD `Product Department Total Pending Orders` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department Total Customers` 

ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Year Acc Customers` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Year Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Year Acc Invoices` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Year Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Year Acc Pending Orders` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Year Acc Customers` ;

ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Quarter Acc Customers` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Quarter Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Quarter Acc Invoices` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Quarter Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Quarter Acc Pending Orders` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Quarter Acc Customers` ;

ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Month Acc Customers` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Month Acc Days Available` ;
ALTER TABLE `Product Department Dimension;` ADD `Product Department 1 Month Acc Invoices` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Month Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Month Acc Pending Orders` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Month Acc Customers` ;

ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Week Acc Customers` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Week Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Week Acc Invoices` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Week Acc Days Available` ;
ALTER TABLE `Product Department Dimension` ADD `Product Department 1 Week Acc Pending Orders` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `Product Department 1 Week Acc Customers` ;

 */


?>