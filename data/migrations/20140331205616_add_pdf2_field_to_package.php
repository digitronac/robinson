<?php

use Phinx\Migration\AbstractMigration;

class AddPdf2FieldToPackage extends AbstractMigration
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
  ADD COLUMN `pdf2` VARCHAR(255) DEFAULT ''  NOT NULL AFTER `pdf`;
HEREDOC;
        $this->query($sql);

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = <<<HEREDOC
ALTER TABLE `packages`
  DROP COLUMN `pdf2` ;
HEREDOC;
        $this->query($sql);
    }
}