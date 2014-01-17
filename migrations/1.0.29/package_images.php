<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

class PackageImagesMigration_1029 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'package_images',
            array(
            'columns' => array(
                new Column(
                    'packageImageId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    )
                ),
                new Column(
                    'filename',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'packageImageId'
                    )
                ),
                new Column(
                    'title',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'filename'
                    )
                ),
                new Column(
                    'createdAt',
                    array(
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'title'
                    )
                ),
                new Column(
                    'packageId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'createdAt'
                    )
                ),
                new Column(
                    'sort',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'packageId'
                    )
                ),
                new Column(
                    'extension',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 16,
                        'after' => 'sort'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('packageImageId')),
                new Index('package_images_packageId_FK', array('packageId'))
            ),
            'references' => array(
                new Reference('package_images_packageId_FK', array(
                    'referencedSchema' => 'robinson_development',
                    'referencedTable' => 'packages',
                    'columns' => array('packageId'),
                    'referencedColumns' => array('packageId')
                ))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '1',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_unicode_ci'
            )
        )
        );
    }
}
