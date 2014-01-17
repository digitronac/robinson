<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

class DestinationsMigration_1029 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'Destinations',
            array(
            'columns' => array(
                new Column(
                    'destinationId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 10,
                        'first' => true
                    )
                ),
                new Column(
                    'destination',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'destinationId'
                    )
                ),
                new Column(
                    'description',
                    array(
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'destination'
                    )
                ),
                new Column(
                    'status',
                    array(
                        'type' => Column::TYPE_BOOLEAN,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'description'
                    )
                ),
                new Column(
                    'createdAt',
                    array(
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'status'
                    )
                ),
                new Column(
                    'updatedAt',
                    array(
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'createdAt'
                    )
                ),
                new Column(
                    'categoryId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'updatedAt'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('destinationId')),
                new Index('destinationId', array('destinationId')),
                new Index('DestinationsStatus_INDEX', array('status')),
                new Index('DestinationsCategoryId_FK', array('categoryId'))
            ),
            'references' => array(
                new Reference('DestinationsCategoryId_FK', array(
                    'referencedSchema' => 'robinson_development',
                    'referencedTable' => 'Category',
                    'columns' => array('categoryId'),
                    'referencedColumns' => array('categoryId')
                ))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '3',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_unicode_ci'
            )
        )
        );
    }
}
