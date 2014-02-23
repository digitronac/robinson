<?php

use Phinx\Migration\AbstractMigration;

class AlterTabsIdToInteger extends AbstractMigration
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
ALTER TABLE `package_tabs`
  CHANGE `packageTabId` `packageTabId` INT UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `package_tags`
  CHANGE `packageTagId` `packageTagId` INT UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `destination_tabs`
  CHANGE `destinationTabId` `destinationTabId` INT UNSIGNED NOT NULL AUTO_INCREMENT;
  SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
    $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // no need to ever migrate this down
        $sql = <<<HEREDOC
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `package_tabs`
  CHANGE `packageTabId` `packageTabId` INT UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `package_tags`
  CHANGE `packageTagId` `packageTagId` INT UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `destination_tabs`
  CHANGE `destinationTabId` `destinationTabId` INT UNSIGNED NOT NULL AUTO_INCREMENT;
  SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        $this->query($sql);

    }
}