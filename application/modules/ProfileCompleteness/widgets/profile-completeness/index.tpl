<ul class="profile_project_content">

    <li>
        <div class="profile_process" style="background-color: <?php echo $this->color; ?>">
            <div class="profile_completed" style="width: <?php echo $this->percent_completed; ?>%;">
            </div> 
        </div>
   </li>

   <li class="profile_project_content_info">
        <?php
        
        echo $this->translate(" profile completeness: ");
        
        echo $this->translate(round($this->percent_completed));
        echo "%";
        ?>
    </li>

    <li  class="profile_project_content_info">
        <?php 
    		if($this->percent_completed != 100){
    			// echo $this->translate("Next: ");
    		}
		?>
        <?php foreach ($this->emptyField as $key => $emptyf): ?> 
            <?php
            if($emptyf != 0)
            {
                if ($key == 'photo') 
                {
                    $action = 'photo';
                    $key = 'photo_pc';
					echo $this->htmlLink(array(
	                    'route' => 'default',
	                    'module' => 'user',
	                    'controller' => 'edit',
	                    'action' => $action), '+ ' . Zend_Registry::get('Zend_Translate')->_($key) . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
                } 
				else if ($key == 'username') 
                {
                    $action = 'general';
                    $key = 'profile url';
					echo $this->htmlLink(array(
	                    'route' => 'default',
	                    'module' => 'user',
	                    'controller' => 'settings',
	                    'action' => $action), '+ ' . Zend_Registry::get('Zend_Translate')->_($key) . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
                } 
				/*else if($key == 'sportlike')
				{
					$action = 'photo';
					echo $this->htmlLink(array(
	                    'route' => 'default',
	                    'module' => 'user',
	                    'controller' => 'edit',
	                    'action' => $action), '+ ' . Zend_Registry::get('Zend_Translate')->_('like sport') . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
				}*/
				else if($key == 'clubfollow')
				{
					 echo $this->htmlLink(array(
	                    'route' => 'group_general',
	                    'action' => 'browse'), '+ ' . Zend_Registry::get('Zend_Translate')->_('follow clubs') . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
				}
				else if($key == 'videoupload')
				{
					echo $this->htmlLink(array(
	                    'route' => 'video_general',
	                    'action' => 'create'), '+ ' . Zend_Registry::get('Zend_Translate')->_('upload video') . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
				}
                else 
                {
                    $action = 'profile';
					echo $this->htmlLink(array(
	                    'route' => 'default',
	                    'module' => 'user',
	                    'controller' => 'edit',
	                    'action' => $action), '+ ' . Zend_Registry::get('Zend_Translate')->_($key) . ' (+' . round(($emptyf*100)/$this->sum) . '%)', array('target' => '_blank')
	                );
                }
                break;
            }
            ?>
        <?php endforeach; ?>
    </li>

        <?php
            if ($this->percent_completed != 100) {
                //echo $this->link_UpdateProfile;
            };
        ?>
</ul>

