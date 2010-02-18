<?php

/**
  * meeting
  * 
  * This class has been auto-generated by the Doctrine ORM Framework.
  * It is the model class where all processing and computing functions are
  * stored.
  * 
  * @author     Romain Deveaud <romain.deveaud@univ-avignon.fr>
  * @project    RdvZ v2.0
  */
class meeting extends Basemeeting
{

  /**
    * save() function overriding. Meeting hash codes are generated here.
    *
    * @return The Meeting object inserted in the database.
    */
  public function save(Doctrine_Connection $conn = null)
  {
    if($this->isNew())
    {    
      // Retrieving meeting creator's id.
      $this->setUid(sfContext::getInstance()->getUser()->getProfileVar(sfConfig::get('app_user_id'))) ;

      $min = 0;
      $max = base_convert ('zzz', 36, 10); // hash is 3 chars max.
      $id = rand($min, $max) ;
      $hash = base_convert($id, 10, 36) ;

      while(Doctrine::getTable('meeting')->hashExists($hash) || strlen($hash) != 3)
        $hash = base_convert($id, 10, 36) ;

      $this->setHash($hash) ;

      $dt = date_create() ;

      $dt->modify("+3 month") ;
      $this->setDateEnd($dt->format('Y-m-d')) ;

      $dt->modify("+1 month") ;
      $this->setDateDel($dt->format('Y-m-d')) ;
    }

    return parent::save($conn) ; 
  }

  /**
    * Prepares all the variables used in the showSuccess view.
    *
    * @return array Set of variables.
    */
  public function processShow()
  {
    $meeting_dates = Doctrine::getTable('meeting_date')->retrieveByMid($this->getId()) ;
    
    // This application is currently french-only.
    setlocale(LC_TIME,'fr_FR.utf8','fra') ;

    $dates    = array() ;
    $months   = array() ;
    $comments = array() ;
    $votes    = array() ;
    
    foreach($meeting_dates as $d)
    {
      // Generating verbose dates.
      $f = strtotime($d->getDate()) ;
      $months[] = strftime("%B %Y",$f) ;
      $dates[strftime("%B %Y", $f)][$d->getId()] = strftime("%a %d", $f) ;
      $comments[] = $d->getComment() ;

      $v = Doctrine::getTable('meeting_poll')->retrieveByDateId($d->getId()) ;
      
      if(!count($v))
      {
        // Little trick : if there is no vote associated to this date, it means that
        // the owner of the meeting probably added it afterwards.
        // We have to handle it and create 'fake' votes wich are blanks and penalized.
        
        // To do this, we retrieve all the users who already voted for this meeting
        // and add them this 'fake' vote.
        $u = Doctrine::getTable('meeting_poll')->retrieveUidByMeetingId($this->getId()) ;
        $n = Doctrine::getTable('meeting_poll')->retrieveNameByMeetingId($this->getId()) ;

        foreach($u as $uid)
        {
          // If this is a database user.
          $p = new meeting_poll() ;
          $p->setPoll(-1000) ;
          $p->setDateId($d->getId()) ;
          $p->setUid($uid) ;
          $p->save() ;
          $votes[$uid][$d->getId()] = $p ;
        }

        foreach($n as $name)
        {
          // If this is an extern user.
          $p = new meeting_poll() ;
          $p->setPoll(-1000) ;
          $p->setDateId($d->getId()) ;
          $p->setParticipantName($name) ;
          $p->save() ;
          $votes[$name][$d->getId()] = $p ;
        }
      }
      else
      {
        // Retrieving the votes for this date.
        foreach($v as $poll)
        {
          if(is_null($poll->getUid()))
            $votes[$poll->getParticipantName()][$d->getId()] = $poll ;
          else
            $votes[$poll->getUid()][$d->getId()] = $poll ;
        }
      }
    }

    // For the view. 
    $months = array_unique($months) ;

    // Finding the best dates : we have to sum all the votes for each date
    // and then compare them. We keep the dates with the max vote counts.
    $t = Doctrine::getTable('meeting_poll')->getVotesByMeeting($this->getId()) ;
    $max = 0 ;

    foreach($t as $res)
      if ($res->getCnt() > $max) $max = $res->getCnt() ;

    $bests = array() ;
    $md = $meeting_dates ;
    
    foreach($t as $res)
      if($res->getCnt() == $max) $bests[] = $res->getDateId() ;

    return array('dates' => $dates, 'comments' => $comments, 'bests' => $bests, 'md' => $md, 'months' => $months, 'votes' => $votes) ;
  }

  /**
    * Dates saving when creating a new meeting.
    */
  public function saveDates($dates,$comments)
  {
    foreach($dates as $num => $date)
    {
      $d = new meeting_date();
      $d->setMid($this->getId()) ;
      $d->setComment($comments[$num]) ;
      $d->setDate(date_format(date_create($date),'Y-m-d')) ;
      $d->save() ;
    }
  }

  /**
    * Function called when a meeting owner edits his meeting.
    * Due to the dynamic content generation, we have to compare
    * which dates and comments where here before and after the edit,
    * and the save or delete the records accordingly.
    *
    */
  public function processDatesAndCommentsForUpdate($dates_to_add,$dates_to_remove,$comments_to_add)
  {
    foreach($dates_to_add as $did => $val)
    {
      // For each new date, we have to know if it already exists in
      // the database.
      $d = Doctrine::getTable('meeting_date')->findWithMid($did,$this->getId()) ;
      if(is_null($d))
      {
        // If not, let's create it !
        $d = new meeting_date() ;
        $d->setDate(date_format(date_create($val),'Y-m-d')) ;
        $d->setComment($comments_to_add[$did]) ;
        $d->setMid($this->getId()) ;
      }
      else
      {
        // It it does, we just change the date and the comment
        // if we need to.
        $d->setDate(date_format(date_create($val),'Y-m-d')) ;
        if(array_key_exists($did,$comments_to_add)) $d->setComment($comments_to_add[$did]) ;
      }

      $d->save() ;

      // Cancel the future operations !
      unset($dates_to_remove[$did]) ;
      unset($comments_to_add[$did]) ;
    }

    foreach($dates_to_remove as $did => $val)
    {
      // Removing the votes associated to the date before deleting
      // the date itself, otherwise a SQL error occurs.
      $polls = Doctrine::getTable('meeting_poll')->retrieveByDateId($did) ;
      foreach($polls as $p) 
        $p->delete() ;

      $d = Doctrine::getTable('meeting_date')->findWithMid($did, $this->getId()) ;
      $d->delete() ;
    }

    foreach($comments_to_add as $did => $comm)
    {
      // Update of a comment.
      $d = Doctrine::getTable('meeting_date')->find($did) ;
      $d->setComment($comments_to_add[$did]) ;
      $d->save() ;
    }
  }

  /**
    * This function is called when a user wants to export the meeting
    * results in CSV format.
    */
  public function createCsv()
  {
    $counts        = Doctrine::getTable('meeting_poll')->getVotesByMeeting($this->getId()) ;
    $meeting_dates = Doctrine::getTable('meeting_date')->retrieveByMid($this->getId()) ;

    $res = array() ;
    foreach($meeting_dates as $md)
      foreach($counts as $c)
        if($c->getDateId() == $md->getId())
          $res[$md->getId()] = array('date' => $md->getDate(), 'count' => $c->getCnt(), 'comment' => $md->getComment()) ;

    return $res ; 
  }
}
