<?php
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$tableCategory = Engine_Api::_() -> getItemTable('ynfeedback_category');
	$categories = $tableCategory -> getCategories();
    $siteTitle = $this->layout()->siteinfo['title'];
	unset($categories[0]);
	$isAllow = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynfeedback_idea', null, 'create')->checkRequire();
?>


<div class='ynfeedback-preview-overlay'></div>
<div class='ynfeedback-preview-main ynfeedback-preview-simple'>
	<span class='btn-ynfeedback-preview-popup-close'><?php echo $this->translate('<i class=\'fa fa-times\'></i>'); ?></span>
	<div class='ynfeedback-preview-main-index'>	
		<div id='popup-title'>
	       	<span id='simple_popup_title'><?php echo $this -> translate('Most Voted Feedback')?></span>       
	    </div>
	    <div id='tab-content'>
	        <div id='add-new-idea-content' class='tab-content active'>
	        </div>
	    </div>
    </div>
</div>