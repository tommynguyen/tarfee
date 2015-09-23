<?php
class Ynvideo_Form_Admin_Widget_PlayersOfWeek extends Core_Form_Admin_Widget_Standard {
  	public function init() {
    	parent::init();
    
    	// Set form attributes
    	$this->setTitle('Players of the Week/Day');
		
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'description' => 'Maximum 64 characters',
      		'validators' => array(
        		array('StringLength', false, array(0, 64)),
      		),
      		'filters' => array(
        			'StripTags',
        		new Engine_Filter_Censor(),
      		),
		));
		$this->title->getDecorator("Description")->setOption("placement", "append");
		
		$this->addElement('Integer', 'numberOfVideos', array(
			'label' => 'Number of videos will show',
			'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
            'value' => 5
		));
		
		$this->addElement('Select', 'type', array(
			'label' => 'Type of the widget',
			'multiOptions' => array(
				'week' => 'Week',
				'day' => 'Day'
			),
			'value' => 'week'
		));
		
		$this->addElement('Select', 'weekDay', array(
			'label' => 'Day for resetting widget',
			'multiOptions' => array(
				'sunday' => 'Sunday',
				'monday' => 'Monday',
				'tuesday' => 'Tuesday',
				'wednesday' => 'Wednesday',
				'thursday' => 'Thursday',
				'friday' => 'Friday',
				'saturday' => 'Saturday'
			),
			'value' => 'sunday'
		));
		
		$hour = array();
		for ($i = 0; $i < 24; $i++) {
			$hour[$i] = $i.':00';
		};
		
		$this->addElement('Select', 'dayHour', array(
			'label' => 'Time for resetting widget',
			'multiOptions' => $hour,
			'value' => 0
		));
		
		$this->addElement('Integer', 'share_internal', array(
			'label' => 'Point for each internal share',
            'value' => 2
		));
		
		$this->addElement('Integer', 'like', array(
			'label' => 'Point for each like',
            'value' => 3
		));
		
		$this->addElement('Integer', 'comment', array(
			'label' => 'Point for each comment',
            'value' => 2
		));
		
		$this->addElement('Integer', 'view', array(
			'label' => 'Point for each view',
            'value' => 1
		));
		
		$this->addElement('Integer', 'dislike', array(
			'label' => 'Point for each dislike',
            'value' => -1
		));
		
		// $this->addElement('Integer', 'unsure', array(
			// 'label' => 'Point for each unsure',
            // 'value' => 0
		// ));
				
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() -> appendFile($view -> baseUrl() . '/application/modules/Ynvideo/externals/scripts/players_of_week.js');		
	}
}