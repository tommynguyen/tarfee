<?php
class Ynmember_Widget_PeopleMayKnowController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$params = $this->_getAllParams();
		if(!empty($params['itemCountPerPage']))
		{
			$limit = $params['itemCountPerPage'];
		}
		else
		{
			$limit = 3;
		}		
		// Don't render this if friendships are disabled
	    if( !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible ) {
	      return $this->setNoRender();
	    }
		if(!$viewer -> getIdentity())
		{
			return $this->setNoRender();
		}
		$tableUser = Engine_Api::_() -> getItemTable('user');
		$select = $tableUser -> select() -> where('user_id <> ?', $viewer->getIdentity()) -> where('enabled = 1') -> where('verified = 1') -> where('approved = 1') -> order(new Zend_Db_Expr(('rand()')));
		$list_users = $tableUser -> fetchAll($select);
	    $list_show_users = array();
		foreach($list_users as $subject)
		{
			if(count($list_show_users) >= $limit)
			{
				break;
			}
		    // Diff friends
		    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
		    $friendsName = $friendsTable->info('name');
		
		    // Mututal friends/following mode
		    $col1 = 'resource_id';
		    $col2 = 'user_id';
		
		    $select = new Zend_Db_Select($friendsTable->getAdapter());
		    $select
		      ->from($friendsName, $col1)
		      ->join($friendsName, "`{$friendsName}`.`{$col1}`=`{$friendsName}_2`.{$col1}", null)
		      ->where("`{$friendsName}`.{$col2} = ?", $viewer->getIdentity())
		      ->where("`{$friendsName}_2`.{$col2} = ?", $subject->getIdentity())
		      ->where("`{$friendsName}`.active = ?", 1)
		      ->where("`{$friendsName}_2`.active = ?", 1)
		      ;
		    // Now get all common friends
		    $uids = array();
		    foreach( $select->query()->fetchAll() as $data ) {
		      $uids[] = $data[$col1];
		    }
		
		    // Do not render if nothing to show
		    if( count($uids) > 0 ) {
		      if(!$subject -> membership() -> isMember($viewer))
			  {
			      $list_show_users[] = array(
			      	'user_id' => $subject->getIdentity(),
			      	'number_mutual' => count($uids),
				  );
			  }
		    }
	    }
		if(count($list_show_users) <= 0)
		{
			foreach($list_users as $subject)
			{
				if(count($list_show_users) >= $limit)
				{
					break;
				}
				if(!$subject -> membership() -> isMember($viewer))
			  	{
			  		$list_show_users[] = array(
			      	'user_id' => $subject->getIdentity());
				}
			}
		}
		$this -> view -> list_show_users = $list_show_users;		
	}
}