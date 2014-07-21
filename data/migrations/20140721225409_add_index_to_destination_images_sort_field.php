<?php

use Phinx\Migration\AbstractMigration;

class AddIndexToDestinationImagesSortField extends AbstractMigration
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
ALTER TABLE `destination_images`
  ADD  INDEX `destination_images_sort_INDEX` (`sort`);
HEREDOC;
        $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query("ALTER TABLE `destination_images` DROP INDEX `destination_images_sort_INDEX`");
    }
}