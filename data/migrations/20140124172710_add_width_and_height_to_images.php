<?php
// @codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;

class AddWidthAndHeightToImages extends AbstractMigration
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
            
            ALTER TABLE `category_images`   
  ADD COLUMN `width` SMALLINT UNSIGNED NOT NULL AFTER `extension`,
  ADD COLUMN `height` SMALLINT UNSIGNED NOT NULL AFTER `width`;
            
            ALTER TABLE `destination_images`   
  ADD COLUMN `width` SMALLINT UNSIGNED NOT NULL AFTER `extension`,
  ADD COLUMN `height` SMALLINT UNSIGNED NOT NULL AFTER `width`;
            
            ALTER TABLE `package_images`   
  ADD COLUMN `width` SMALLINT UNSIGNED NOT NULL AFTER `extension`,
  ADD COLUMN `height` SMALLINT UNSIGNED NOT NULL AFTER `width`;
            
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
            
            ALTER TABLE `category_images`   
  DROP COLUMN `width`, 
  DROP COLUMN `height`;
            
            ALTER TABLE `destination_images`   
  DROP COLUMN `width`, 
  DROP COLUMN `height`;
            
            ALTER TABLE `package_images`   
  DROP COLUMN `width`, 
  DROP COLUMN `height`;
            
            SET FOREIGN_KEY_CHECKS = 1;
HEREDOC;
        $this->query($sql);
    }
}