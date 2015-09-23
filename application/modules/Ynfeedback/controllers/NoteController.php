<?php

class Ynfeedback_NoteController extends Core_Controller_Action_Standard 
{
	public function indexAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> content -> setNoRender();
		}
		$feedbackId = $this -> _getParam('feedback_id', null);
		if (is_null($feedbackId))
		{
			return $this -> _helper -> content -> setNoRender();
		}
		$noteTbl = Engine_Api::_()->getDbTable('notes', 'ynfeedback');
		$this -> view -> notes = $notes = $noteTbl -> getNoteByFeedbackId($feedbackId);
		$this -> view -> form = $form = new Ynfeedback_Form_Note_Create();
		
		if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
        $values = $form -> getValues();
        $noteTbl = Engine_Api::_() -> getDbTable('notes', 'ynfeedback');
        if ($values['note_id'] == 0)
        {
        	$note = $noteTbl -> createRow();
	        $note -> setFromArray(array(
	        	'creation_date' => date('Y-m-d H:i:s'),
	        	'idea_id' => $feedbackId,
	        	'user_id' => $viewer -> getIdentity(),
	        	'content' => $values['content'],
	        ));
        }
        else 
        {
        	$note = $noteTbl -> getNote($values['note_id']);
        	$note -> content = $values['content'];
        }
        $note -> save();
        return $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'format' => 'smoothbox',
        	'parentRefresh' => true,
			'messages' => array($this->view->translate("Saved Note Successfully."))
		));
		
	}
	
	public function deleteAction()
	{
		$this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$noteId = $this->_getParam('note_id', null);
		if (is_null($noteId))
		{
			return;
		}
		$noteTbl = Engine_Api::_()->getDbTable('notes', 'ynfeedback');
		$note = $noteTbl -> getNote($noteId);
		if (is_null($note)){
			return;
		}
		$note -> delete();
		echo Zend_Json::encode(array(
			'result' => Zend_Registry::get("Zend_Translate")-> _("Deleted note successfully.")
		));
		exit;
	}
}