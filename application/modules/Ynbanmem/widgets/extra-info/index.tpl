

  <h3>
    <?php echo $this->translate('Extra Info');?>
  </h3>
  
  <script type="text/javascript">

function loginAsUser(id) {
  if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
    return;
  }
  var url = '<?php echo $this->url(array('action' => 'login'), 'ynbanmem_general') ?>';
  var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
  (new Request.JSON({
    url : url,
    data : {
      format : 'json',
      id : id
    },
    onSuccess : function() {
      window.location.replace( baseUrl );
    }
  })).send();
}


<?php if( $this->openUser ): ?>
window.addEvent('load', function() {
  $$('#multimodify_form .admin_table_options a').each(function(el) {
    if( -1 < el.get('href').indexOf('/edit/') ) {
      el.click();
      //el.fireEvent('click');
    }
  });
});
<?php endif ?>
</script>
<ul>

    <li>
        <?php
        echo $this->translate('User ID: '); echo $this->user->user_id;?>
        <?php // @todo implement link ?>

    </li>

    <li>
        <?php echo $this->translate('User Resgister IP: ')?>
        <?php // @todo implement link ?>
        <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php
        $ipObj = new Engine_IP($this->user->creation_ip);
        echo $ipObj->toString()
        ?>
        <?php else: ?>
        <?php echo $this->translate('(hidden)') ?>
        <?php endif ?>
    </li>

    <li>
        <?php echo $this->translate('Last accessed IP: ').' ' ?>
        <?php // @todo implement link ?>
        <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php
        $ipObj = new Engine_IP($this->user->lastlogin_ip);
        echo $ipObj->toString()
        ?>
        <?php else: ?>
        <?php echo $this->translate('(hidden)') ?>
        <?php endif ?>
    </li>

    <li>
        <?php echo $this->translate('Last logged in:') ?>
        <?php echo $this->locale()->toDateTime($this->user->lastlogin_date) ?>

    </li>
    <li>
        <?php echo $this->translate('Email: ') ?>
        <?php echo $this->user->email ?>

    </li>
    <li>

        </a>
    </li>
    <li>
	
        <?php  if (Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'ban')): // @todo change this to look up actual superadmin level ?>
        <a  <?php if($this->typeURL !=1):?> class='smoothbox' <?php else: ?> target = '_blank' <?php endif;?> href='<?php 

            switch($this->typeURL)
            {
            case 1:
                echo $this->url(array('action' => 'add', 'id' => $this->user->user_id), 'ynbanmem_general');
            break;
            case 2:
                echo $this->url(array('action' => 'unban', 'user' => $this->bannedUser_id,'email'=>$this->bannedEmail_id , 'type'=>0), 'ynbanmem_general');
             break;
          
            case 3:
                echo $this->url(array('action' => 'unban', 'id' => $this->bannedid,'type'=>1), 'ynbanmem_general');
                break;
            
            case 4:
                echo $this->url(array('action' => 'unban', 'id' => $this->bannedid,'type'=>2), 'ynbanmem_general');
                break;
            }

            ?>'> <?php switch($this->banText)
            {
            case 1:
            echo $this->translate('Unban');
            break;
            case 2:
				if(($this->superAdminCount>1 && $this->user->level_id == 1) || $this->user->level_id != 1)
					echo $this->translate('Ban');
            break;
            }?>
    </a>
	<?php if(($this->superAdminCount>1 && $this->user->level_id == 1) || $this->user->level_id != 1): ?>
    |
	<?php endif;?>
     <?php endif;?>
	 <?php if ($this->viewer->isAdmin() || Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'note') ): // @todo change this to look up actual superadmin level ?>
    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'note', 'id' => $this->user->user_id), 'ynbanmem_general');?>'>
       <?php 
							 if ($this->user->note != NULL)
								echo $this->translate("edit note");
							else echo $this->translate("add note");?>
</a>
<?php endif;?>
 <?php if (($this->superAdminCount>1 && $this->user->level_id==1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'delete')) || ( $this->user->level_id !=1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'delete')) )                                                                                                 : // @todo change this to look up actual superadmin level ?>
                    |
<a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $this->user->user_id), 'ynbanmem_general');?>'>
   <?php echo $this->translate("delete") ?>
</a>
<?php endif;?>

</li>


<li>

    <?php if( $this->user->level_id != 1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'login')): // @todo change this to look up actual superadmin level ?>
    <a  href='<?php echo $this->url(array('action' => 'login', 'id' => $this->user->user_id),'ynbanmem_general');?>' onclick="loginAsUser(<?php echo $this->user->user_id ?>); return false;">
       <?php echo $this->translate("Log as this user") ?>
</a>
<?php endif; ?>

</li>
</ul>