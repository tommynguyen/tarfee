<?php

class Ynevent_AgentController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynevent_main');
	}

	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$agentTable = Engine_Api::_()->getDbTable('agents', 'ynevent');
		$agents = $agentTable->getUserAgents($viewer);
		$this->view->countAgent = count($agents);
		$maxAgent = Ynevent_Plugin_Utilities::getNumberAgentAllow($viewer);
		$this->view->maxAgent = ($maxAgent == null ? 5 : $maxAgent);
		$page = $this->_getParam('page', 1);
		$this->view->paginator = $paginator = Zend_Paginator::factory($agents);
		$this->view->paginator->setItemCountPerPage(10);
		$this->view->paginator->setCurrentPageNumber($page);
		
		// Render
		$this->_helper->content->setEnabled();
	}

	public function createAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$agentTable = Engine_Api::_()->getDbTable('agents', 'ynevent');
		$agents = $agentTable->getUserAgents($viewer);

		$maxAgent = Ynevent_Plugin_Utilities::getNumberAgentAllow($viewer);
		$maxAgent = ($maxAgent == null ? 5 : $maxAgent);
		if (count($agents) >= $maxAgent)
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'agent',
					'action' => 'index'
			), 'event_extended', true);
		}
		$this->view->form = $form = new Ynevent_Form_Agent_Create();
		if (!$this->getRequest()->isPost())
		{
			return;
		}
		$values = $this->getRequest()->getPost();

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			if (!empty($values['country'])) {
				$this->view->countryVal = $values['country'];
			} 
			else {
				$this->view->countryVal = '';
			}
			if(!empty($values['state']))
			{
				$this->view->stateVal = $values['state'];
			}
			else {
				$this->view->stateVal = '';
			}
			
			if(!empty($values['city']))
			{
				$this->view->cityVal = $values['city'];
			}
			else {
				$this->view->cityVal = '';
			}
			
			return;
		}
		$values = $form->getValues();
		if (isset($values['starttime']) && !empty($values['starttime']))
		{
			$startDay = $values['starttime'];
			$values['starttime'] = Engine_Api::_()->ynevent()->changeDateFormat($startDay, 'mdy');
		}
		if (isset($values['endtime']) && !empty($values['endtime']))
		{
			$endDay = $values['endtime'];
			$values['endtime'] = Engine_Api::_()->ynevent()->changeDateFormat($endDay, 'mdy');
		}

		// Convert times
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer->timezone);
		if (!empty($values['starttime'])) {
			$start = strtotime($values['starttime']);
			$values['starttime'] = date('Y-m-d H:i:s', $start);
		}
		
		if (!empty($values['endtime']))
		{
			$end = strtotime($values['endtime']);
			$values['endtime'] = date('Y-m-d H:i:s', $end);
		}

		if (count($agents))
		{
			foreach ($agents as $agent)
			{
				if ($agent->title == $values['title'])
				{
					$form->addError("The agent name existed");
					return;
				}
			}
		}

		$agentTable = Engine_Api::_()->getDbTable('agents', 'ynevent');
		$agent = $agentTable->createRow();

		date_default_timezone_set($oldTz);

		$db = $agentTable->getAdapter();
		$db->beginTransaction();

		try
		{
			$agent->setFromArray($values);
			$agent->user_id = $viewer->getIdentity();
			$agent->creation_date = date('Y-m-d H:i:s');
			$agent->save();
			$db->commit();
		}
		catch (Exeption $e)
		{
			throw $e;
			$db->rollBack();
		}

		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'agent',
			'action' => 'index'
		), 'event_extended', true);
	}

	public function editAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$agent_id = $this->_getParam('id');
		if ($agent_id)
		{
			$agent = Engine_Api::_()->getItem('event_agent', $agent_id);
		}
		if (!is_object($agent))
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'agent',
					'action' => 'index'
			), 'event_extended', true);
		}
		if (!$agent->isOwner($viewer))
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'agent',
					'action' => 'index'
			), 'event_extended', true);
		}
		$this->view->form = $form = new Ynevent_Form_Agent_Edit();
		if (!empty($agent->starttime) && $agent->starttime != 0)
		{
			$agent->starttime = date('m-d-Y', strtotime($agent->starttime));
		}
		else
		{
			$agent->starttime = null;
		}
		
		if (!empty($agent->endtime) && $agent->endtime != 0)
		{
			$agent->endtime = date('m-d-Y', strtotime($agent->endtime));
		}
		else
		{
			$agent->endtime = null;
		}
		
		$values = $agent->toArray(); 
		
		if (!$this->getRequest()->isPost())
		{
			$form->populate($values);
			
			if (!empty($values['country_code']))
			{
				$this->view->countryVal = $values['country_code'];
			}
			else
			{
				$this->view->countryVal = '';
			}
			
			if (!empty($values['state']))
			{
				$this->view->stateVal = $values['state'];
			}
			else
			{
				$this->view->stateVal = '';
			}
			
			if (!empty($values['city']))
			{
				$this->view->cityVal = $values['city'];
			}
			else
			{
				$this->view->cityVal = '';
			}

			return;
		}
		//print_r($this->getRequest()->getPost());
		//echo "<br />";
		if (!$form->isValid($this->getRequest()->getPost()))
		{
			
			return;
		}

		$values = $form->getValues();
		
		
		if (isset($values['starttime']) && !empty($values['starttime']))
		{
			$startDay = $values['starttime'];
			$values['starttime'] = Engine_Api::_()->ynevent()->changeDateFormat($startDay, 'mdy');
		}
		if (isset($values['endtime']) && !empty($values['endtime']))
		{
			$endDay = $values['endtime'];
			$values['endtime'] = Engine_Api::_()->ynevent()->changeDateFormat($endDay, 'mdy');
		}
		// Convert times
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer->timezone);
		if (!empty($values['starttime']))
		{
			$start = strtotime($values['starttime']);
		}
		if (!empty($value['endtime']))
		{
			$end = strtotime($values['endtime']);
		}
		date_default_timezone_set($oldTz);
		if (!empty($start))
		{
			$values['starttime'] = date('Y-m-d H:i:s', $start);
		}
		if (!empty($end))
		{
			$values['endtime'] = date('Y-m-d H:i:s', $end);
		}

		$db = Engine_Api::_()->getDbTable('agents', 'ynevent')->getAdapter();
		$db->beginTransaction();
		try
		{
			$agent->setFromArray($values);
			$agent->save();
			$db->commit();
		}
		catch (Exeption $e)
		{
			$db->rollBack();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute(array(
				'controller' => 'agent',
				'action' => 'index'
		), 'event_extended', true);
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$agent_id = $this->_getParam('id');
		if ($agent_id)
		{
			$agent = Engine_Api::_()->getItem('event_agent', $agent_id);
		}
		if (!is_object($agent))
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'agent',
					'action' => 'index'
			), 'event_extended', true);
		}
		if (!$agent->isOwner($viewer))
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'agent',
					'action' => 'index'
			), 'event_extended', true);
		}
		$this->view->form = $form = new Ynevent_Form_Agent_Delete();
		if (!$this->getRequest()->isPost())
		{
			return;
		}
		$db = Engine_Api::_()->getDbTable('agents', 'ynevent')->getAdapter();
		$db->beginTransaction();
		try
		{
			$agent->delete();
			$db->commit();
		}
		catch (Exeption $e)
		{
			throw $e;
			$db->rollBack();
		}

		//redirect
		$this->view->status = true;
		$this->view->message = Zend_Registry::get('Zend_Translate')->_('The agent has been deleted');
		return $this->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						"controller" => "agent",
						"action" => "index"
				), 'event_extended', true),
				'messages' => Array($this->view->message),
				'layout' => 'default-simple',
		));
	}

}
