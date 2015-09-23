<?php
class Ynevent_Widget_FeatureEventsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$Name = $table -> info('name');
		$limit = 3;
		
		$select = $table -> select() -> from($Name, "$Name.*");
		$select -> where("featured = 1");
		$select -> order("rand()");
		$select -> limit($limit);
		//echo $select;
		$this -> view -> items = $items = $table -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);
		
		$this -> view -> typed = '1';
		if (!$totalItems)
		{
			$this -> setNoRender();
		}
		$this -> view -> html_mobile_slideshow = $this -> view -> partial('_m_slideshow.tpl', 'ynevent', array('events' => $items));
	}

}
