<?php
class Ynfeedback_Plugin_Menus { 
	public function canViewFeedback() {
		return true;
	}
	
	public function canCreateFeedback() {
		return true;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if( !Engine_Api::_()->authorization()->isAllowed('ynfeedback_idea', $viewer, 'create') ) {
	      return false;
	    }
		return true;
	}
	
    public function canManageFeedback() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        return ($viewer->getIdentity()) ? true : false;
    }
    
	public function onMenuInitialize_YnfeedbackProfileShare() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		if( $subject->getType() !== 'ynfeedback_idea' )
		{
			throw new Exception('This idea does not exist.');
		}

		if( !$viewer->getIdentity() )
		{
			return false;
		}
			
		return array(
	      'label' => 'Share',
	      'class' => 'smoothbox',
	      'route' => 'default',
	      'params' => array(
		        'module' => 'activity',
		        'controller' => 'index',
		        'action' => 'share',
		        'type' => $subject->getType(),
		        'id' => $subject->getIdentity(),
		        'format' => 'smoothbox',
			),
		);
	}
    
    public function onMenuInitialize_YnfeedbackEditFeedback() {
        $subject = Engine_Api::_() -> core() -> getSubject();
        
        if ($subject -> getType() !== 'ynfeedback_idea') {
            throw new Ynfeedback_Model_Exception('Whoops, not a feedback!');
        }
        if( !$subject->isEditable()) {
            return false;
        }
                
        return array(
            'label' => 'Edit',
            'route' => 'ynfeedback_specific',
            'params' => array(
                'action' => 'edit',
                'idea_id' => $subject -> getIdentity(),
            ),
            'icon-font' => 'fa fa-pencil-square-o',
            'class' => 'feedback-option-link',
        );
    }
    
    public function onMenuInitialize_YnfeedbackManageScreenshots() {
        $subject = Engine_Api::_() -> core() -> getSubject();
        
        if ($subject -> getType() !== 'ynfeedback_idea') {
            throw new Ynfeedback_Model_Exception('Whoops, not a feedback!');
        }
        if( !$subject->isEditable()) {
            return false;
        }
                
        return array(
            'label' => 'Manage Screenshots',
            'route' => 'ynfeedback_specific',
            'params' => array(
                'action' => 'manage-screenshots',
                'idea_id' => $subject -> getIdentity(),
            ),
            'icon-font' => 'fa fa-file-image-o',
            'class' => 'feedback-option-link',
        );
    }
    
    public function onMenuInitialize_YnfeedbackManageFiles() {
        $subject = Engine_Api::_() -> core() -> getSubject();
        
        if ($subject -> getType() !== 'ynfeedback_idea') {
            throw new Ynfeedback_Model_Exception('Whoops, not a feedback!');
        }
        if( !$subject->isEditable()) {
            return false;
        }
                
        return array(
            'label' => 'Manage Files',
            'route' => 'ynfeedback_specific',
            'params' => array(
                'action' => 'manage-files',
                'idea_id' => $subject -> getIdentity(),
            ),
            'icon-font' => 'fa fa-file-text',
            'class' => 'feedback-option-link',
        );
    }
    
    public function onMenuInitialize_YnfeedbackDeleteFeedback() {
        $subject = Engine_Api::_() -> core() -> getSubject();
        
        if ($subject -> getType() !== 'ynfeedback_idea') {
            throw new Ynfeedback_Model_Exception('Whoops, not a feedback!');
        }
        if( !$subject->isEditable()) {
            return false;
        }
                
        return array(
            'label' => 'Delete',
            'route' => 'ynfeedback_specific',
            'params' => array(
                'action' => 'delete',
                'idea_id' => $subject -> getIdentity(),
            ),
            'icon-font' => 'fa fa-trash',
            'class' => 'feedback-option-link smoothbox',
        );
    }
}
