<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

class DestinationimagesMigration_1021 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'DestinationImages',
            array(
            'columns' => array(
                new Column(
                    'destinationImageId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 10,
                        'first' => true
                    )
                ),
                new Column(
                    'filename',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'destinationImageId'
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
                    'destinationId',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 10,
                        'after' => 'createdAt'
                    )
                ),
                new Column(
                    'sort',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 10,
                        'after' => 'destinationId'
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
                new Index('destinationImageId', array('destinationImageId')),
                new Index('DestinationImagesCategoryId_FK', array('destinationId'))
            ),
            'references' => array(
                new Reference('DestinationImagesCategoryId_FK', array(
                    'referencedSchema' => 'robinson_development',
                    'referencedTable' => 'Destinations',
                    'columns' => array('destinationId'),
                    'referencedColumns' => array('destinationId')
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
