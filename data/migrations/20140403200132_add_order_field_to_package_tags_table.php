<?php

use Phinx\Migration\AbstractMigration;

class AddOrderFieldToPackageTagsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = <<<HEREDOC
  ALTER TABLE `package_tags`
  ADD COLUMN `order` TINYINT UNSIGNED DEFAULT 1  NOT NULL AFTER `type`
HEREDOC;
        $this->query($sql);

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = <<<HEREDOC
        ALTER TABLE `package_tags` DROP COLUMN `order`;
HEREDOC;
        $this->query($sql);
    }
}