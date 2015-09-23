<?php

class Ynevent_Form_Follow extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    $this->addElement('Radio', 'follow', array(
      'multiOptions' => array(
        1 => 'Follow',
        0 => 'Unfollow',
        //3 => 'Awaiting Reply',
      ),
    ));
  }
}