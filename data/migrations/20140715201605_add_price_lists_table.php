<?php

use Phinx\Migration\AbstractMigration;

class AddPriceListsTable extends AbstractMigration
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
CREATE TABLE `pricelists`(
  `pricelistId` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` VARCHAR(128) NOT NULL,
  `createdAt` DATETIME NOT NULL,
   PRIMARY KEY(`pricelistId`)
) ENGINE=INNODB CHARSET=utf8 COLLATE=utf8_unicode_ci;
HEREDOC;
        $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = "DROP TABLE `pricelists`";
        $this->query($sql);
    }
}