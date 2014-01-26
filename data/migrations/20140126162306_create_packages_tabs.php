<?php
// @codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;

class CreatePackagesTabs extends AbstractMigration
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
            CREATE TABLE `package_tabs`(  
  `packageTabId` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `type` TINYINT UNSIGNED NOT NULL,
  `packageId` INT UNSIGNED NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `updatedAt` DATETIME NOT NULL,
  PRIMARY KEY (`packageTabId`),
  CONSTRAINT `package_tabs_packageId_FK` FOREIGN KEY (`packageId`) REFERENCES `packages`(`packageId`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
            DROP TABLE `packages_tabs`;
            SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        $this->query($sql);
    }
}