<?php

/**
 * Basemeeting
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $hash
 * @property string $title
 * @property string $description
 * @property integer $uid
 * @property integer $closed
 * @property timestamp $date_del
 * @property timestamp $date_end
 * @property integer $aifna
 * @property integer $notif
 * @property Doctrine_Collection $meeting_dates
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6365 2009-09-15 18:22:38Z jwage $
 */
abstract class Basemeeting extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('meeting');
        $this->hasColumn('hash', 'string', 6, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '6',
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', 4000, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '4000',
             ));
        $this->hasColumn('uid', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('closed', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('date_del', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('date_end', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('aifna', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('notif', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
    $this->hasMany('meeting_date as meeting_dates', array(
             'local' => 'id',
             'foreign' => 'mid'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}