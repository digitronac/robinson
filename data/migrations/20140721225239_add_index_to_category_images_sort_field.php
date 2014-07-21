<?php

use Phinx\Migration\AbstractMigration;

class AddIndexToCategoryImagesSortField extends AbstractMigration
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
ALTER TABLE `category_images`
  ADD  INDEX `category_images_sort_INDEX` (`sort`);
HEREDOC;
        $this->query($sql);

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query("ALTER TABLE `category_images` DROP INDEX `category_images_sort_INDEX`");
    }
}