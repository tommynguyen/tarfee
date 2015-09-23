<?php

?>
<div class="headline">
  <div class="tabs">
    	<ul class="navigation">
		    <li>
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
		    <li class="active">
		        <a class="menu_user_settings user_settings_network" href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'deactivate'),'user_extended', true) ?>"><?php echo $this -> translate('Deactivate Account')?></a>
		    </li>
		</ul>
  </div>
</div>

<?php if( $this->isLastSuperAdmin ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You may not deactive the last super admin account.'); ?>
    </span>
  </div>
<?php return; endif; ?>

<?php echo $this->form->setAttrib('id', 'user_form_settings_deactivate')->render($this) ?>
