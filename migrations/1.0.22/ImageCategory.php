<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

class ImagecategoryMigration_1022 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'ImageCategory',
            array(
            'columns' => array(
                new Column(
                    'imageCategoryId',
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
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'imageCategoryId'
                    )
                ),
                new Column(
                    'createdAt',
                    array(
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'filename'
                    )
                ),
                new Column(
                    'categoryId',
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
                        'after' => 'categoryId'
                    )
                ),
                new Column(
                    'extension',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 11,
                        'after' => 'sort'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('imageCategoryId')),
                new Index('IDX_AD1C86C39C370B71', array('categoryId'))
            ),
            'references' => array(
                new Reference('FK_AD1C86C39C370B71', array(
                    'referencedSchema' => 'robinson_development',
                    'referencedTable' => 'Category',
                    'columns' => array('categoryId'),
                    'referencedColumns' => array('categoryId')
                ))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '10',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_unicode_ci'
            )
        )
        );
    }
}
