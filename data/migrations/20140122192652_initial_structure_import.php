<?php
// @codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;

class InitialStructureImport extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = <<<HEREDOC

            SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `categories` (
  `categoryId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




CREATE TABLE `category_images` (
  `categoryImageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` datetime NOT NULL,
  `categoryId` int(11) UNSIGNED NOT NULL,
  `sort` int(11) UNSIGNED NOT NULL,
  `extension` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`categoryImageId`),
  KEY `category_images_categoryId_FK` (`categoryId`),
  CONSTRAINT `category_images_categoryId_FK` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`categoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




CREATE TABLE `destination_images` (
  `destinationImageId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` datetime NOT NULL,
  `destinationId` int(10) UNSIGNED NOT NULL,
  `sort` int(10) UNSIGNED NOT NULL,
  `extension` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`destinationImageId`),
  KEY `destinationImageId` (`destinationImageId`),
  KEY `destination_images_categoryId_FK` (`destinationId`),
  CONSTRAINT `destination_images_categoryId_FK` FOREIGN KEY (`destinationId`) REFERENCES `destinations` (`destinationId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




CREATE TABLE `destinations` (
  `destinationId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `destination` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `categoryId` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`destinationId`),
  KEY `destinationId` (`destinationId`),
  KEY `DestinationsStatus_INDEX` (`status`),
  KEY `destinations_categoryId_FK` (`categoryId`),
  CONSTRAINT `destinations_categoryId_FK` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`categoryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




CREATE TABLE `package_images` (
  `packageImageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `createdAt` datetime NOT NULL,
  `packageId` int(11) UNSIGNED NOT NULL,
  `sort` int(11) UNSIGNED NOT NULL,
  `extension` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`packageImageId`),
  KEY `package_images_packageId_FK` (`packageId`),
  CONSTRAINT `package_images_packageId_FK` FOREIGN KEY (`packageId`) REFERENCES `packages` (`packageId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




CREATE TABLE `packages` (
  `packageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `package` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `tabs` text COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `pdf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `destinationId` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`packageId`),
  KEY `packages_destinationId_FK` (`destinationId`),
  CONSTRAINT `packages_destinationId_FK` FOREIGN KEY (`destinationId`) REFERENCES `destinations` (`destinationId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = <<<HEREDOC
            SET FOREIGN_KEY_CHECKS = 0;
            
            DROP TABLE `categories`;
            DROP TABLE `category_images`;
            DROP TABLE `destination_images`;
            DROP TABLE `destinations`;
            DROP TABLE `package_images`;
            DROP TABLE `packages`;
            
            SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        $this->query($sql);
    }
}