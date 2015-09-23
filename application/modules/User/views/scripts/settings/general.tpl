<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9874 2013-02-13 00:48:05Z shaun $
 * @author     Steve
 */
?>
<div class="headline">
  <div class="tabs">
    	<ul class="navigation">
		    <li class="active">
		        <a class="menu_user_settings user_settings_general" href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'general'),'user_extended', true) ?>"><?php echo $this -> translate('Settings')?></a>
		    </li>
		    <li>
		        <a class="menu_user_settings user_settings_privacy" href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'password'),'user_extended', true) ?>"><?php echo $this -> translate('Change Password')?></a>
		    </li>
		    <?php 
		    $canDetele = false;
		    if (Engine_Api::_() -> core() -> hasSubject('user')) 
		    {
				$subject = Engine_Api::_() -> core() -> getSubject('user');
				// Check viewer
				$viewer = Engine_Api::_() -> user() -> getViewer();
				if ($viewer -> getIdentity()) 
				{
					$canDetele = (bool)$subject -> authorization() -> isAllowed($viewer, 'delete');
				}
			}
			if($canDetele):?>
		    <li>
		        <a class="menu_user_settings user_settings_network" href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'delete'),'user_extended', true) ?>"><?php echo $this -> translate('Delete Account')?></a>
		    </li>
		    <?php endif;?>
		    <li>
		        <a class="menu_user_settings user_settings_network" href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'deactivate'),'user_extended', true) ?>"><?php echo $this -> translate('Deactivate Account')?></a>
		    </li>
		</ul>
  </div>
</div>
<div class="global_form">
  <?php if ($this->form->saveSuccessful): ?>
    <h3><?php echo $this->translate('Settings were successfully saved.');?></h3>
  <?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div>

<?php if( Zend_Controller_Front::getInstance()->getRequest()->getParam('format') == 'html' ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      var req = new Form.Request($$('.global_form')[0], $('global_content'), {
        requestOptions : {
          url : '<?php echo $this->url(array()) ?>'
        },
        extraData : {
          format : 'html'
        }
      });
    });
  </script>
<?php endif; ?>