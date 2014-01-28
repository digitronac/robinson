<?php
//@codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;

class CreatePackageTagsTable extends AbstractMigration
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
CREATE TABLE `package_tags`(  
  `packageTagId` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag` VARCHAR(255) NOT NULL,
  `type` TINYINT UNSIGNED NOT NULL,
  `packageId` INT UNSIGNED NOT NULL,
  `createdAt` DATETIME NOT NULL,
  PRIMARY KEY (`packageTagId`)
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
            DROP TABLE `package_tags`;
            SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        
        $this->query($sql);
    }
}