<?php

use Phinx\Migration\AbstractMigration;

class AddSlugFieldToPackage extends AbstractMigration
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
ALTER TABLE `packages`
  ADD COLUMN `slug` VARCHAR(256) NOT NULL AFTER `status`;
HEREDOC;
        $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query('ALTER TABLE `packages` DROP COLUMN `slug`');
    }
}