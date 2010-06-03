<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addmeeting extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('meeting', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 8,
              'autoincrement' => true,
              'primary' => true,
             ),
             'hash' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 8,
             ),
             'title' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 255,
             ),
             'description' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 4000,
             ),
             'uid' => 
             array(
              'type' => 'integer',
              'length' => 8,
             ),
             'closed' => 
             array(
              'type' => 'boolean',
              'default' => 0,
              'length' => 25,
             ),
             'date_del' => 
             array(
              'type' => 'timestamp',
              'length' => 25,
             ),
             'date_end' => 
             array(
              'type' => 'timestamp',
              'length' => 25,
             ),
             'notif' => 
             array(
              'type' => 'boolean',
              'default' => 0,
              'length' => 25,
             ),
             'created_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
             ),
             'updated_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
             ),
             ), array(
             'indexes' => 
             array(
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->dropTable('meeting');
    }
}