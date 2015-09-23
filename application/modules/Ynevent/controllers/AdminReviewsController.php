<?php

class Ynevent_AdminReviewsController extends Core_Controller_Action_Admin {

    public function indexAction() {
    	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_reviews');
		
		
		$review_tbl = Engine_Api::_()->getItemTable('ynevent_review');
		$RName = $review_tbl->info('name');

		$report_tbl = Engine_Api::_()->getItemTable('event');
		$Name = $report_tbl->info('name');
		
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
			if(isset($values['dismiss']))
			{
				foreach ($values as $key => $value) {
	                if ($key == 'delete_' . $value) {
	                	
						$table = Engine_Api::_()->getItemTable('ynevent_reviewreport');
						$select = $table->select() -> where('review_id = ?', $value);
						$rows = $table->fetchAll($select);
						foreach($rows as $row)
						{
							$row->delete();
						}
				
						$review = Engine_Api::_()->getItem('ynevent_review', $value);
						$review->report_count = 0;
						$review->save();						
	                    
	                }
            	}
			}
			else{
				foreach ($values as $key => $value) {
	                if ($key == 'delete_' . $value) {
	                    $review = Engine_Api::_()->getItem('ynevent_review', $value);
	                    $review->delete();
	                }
	            }
			}
			
            
        }
		
		$values = $this->_getAllParams();
		if (!isset($values['order'])) {
            $values['order'] = $RName.".creation_date";
        }

        if (!isset($values['direction'])) {
            $values['direction'] = "ASC";
        }
		$select = $review_tbl->select()->from($RName)-> setIntegrityCheck(false);
		$select	->joinLeft($Name, "$Name.event_id = $RName.event_id ","$Name.title");
		$select -> order($values['order'] . " " . $values['direction']);
		
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        $this->view->formValues = $values;
        
    }
	public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $review = Engine_Api::_()->getItem('ynevent_review', $id);
                $review->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-reviews/delete.tpl');
    }
	public function dismissReportAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost()) {

            try {
            	$table = Engine_Api::_()->getItemTable('ynevent_reviewreport');
				$select = $table->select() -> where('review_id = ?', $id);
				$rows = $table->fetchAll($select);
				foreach($rows as $row)
				{
					$row->delete();
				}
				
				$review = Engine_Api::_()->getItem('ynevent_review', $id);
				$review->report_count = 0;
				$review->save();
				
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-reviews/dismiss-report.tpl');
    }
	public function viewAction()
	{
		$id = $this->_getParam('id');
		$event_id = $this->_getParam('event_id');
		if($id && $event_id)
		{
			$this->view->event = Engine_Api::_()->getItem('event', $event_id);
			
			$table = Engine_Api::_()->getItemTable('ynevent_reviewreport');
			$select = $table->select() -> where('review_id = ?', $id)->order('review_id DESC');
			$this->view->reports = $reports = $table->fetchAll($select);
		}
		else{
			return $this->_forward('requireauth', 'error', 'core');
		}
		
		if ($this->getRequest()->isPost()) {
			$values = $this->getRequest()->getPost();
			if(isset($values['dismiss']))
			{
				foreach($reports as $report)
				{
					$report->delete();
				}
				
				$review = Engine_Api::_()->getItem('ynevent_review', $id);
				$review->report_count = 0;
				$review->save();
			}
			else{
				$review = Engine_Api::_()->getItem('ynevent_review', $id);
				$review->delete();
			}
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
		}
		
	}

}