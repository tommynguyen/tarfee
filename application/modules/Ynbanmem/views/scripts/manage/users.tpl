<script type="text/javascript">
	var currentOrder = '<?php echo $this->order ?>';
	var currentOrderDirection = '<?php echo $this->order_direction ?>';
	var changeOrder = function(order, default_direction){
	// Just change direction
		if( order == currentOrder ) {
			$('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
		} else {
			$('order').value = order;
		    $('order_direction').value = default_direction;
		}
		$('filter_form').submit();
	}
	function multiDelete(url)
	{
		var Checkboxs = document.forms[2].elements; 
		
		var values = "";
		for(var i = 0; i < Checkboxs.length; i++) 
		{	
			 var type = Checkboxs[i].type;
		     if (type=="checkbox" && Checkboxs[i].checked)
			 {				 
		       	values += "," + Checkboxs[i].value;				
		     }
		}
        if(values == "")
        {
            alert("You don\'t choose any user");  
            return false;
        }
		else if(values != "")
		{
			values = "(" + values + ")";
		}
		url += "?userIds=" + values;
		Smoothbox.open(url);
		
	}
	function selectAll()
	{
	  var i;
	  var multimodify_form = $('multimodify_form');
	  var inputs = multimodify_form.elements;
	  for (i = 1; i < inputs.length - 1; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = inputs[0].checked;
	    }
	  }
	}
	function multiModify()
	{
	  var multimodify_form = $('multimodify_form');
	  if (multimodify_form.submit_button.value == 'delete')
	  {
	    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
	  }
	}
</script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Member Management');?>
    </h2>
    <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
        <?php
		
        // Render the menu
        echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
        ?>
    </div>
    <?php endif;?>
</div>
<div class="ynbanmem_manage_users">
  <?php echo $this->form->render($this);?>
</div>

<br />

<div class='ynbanmem_manage_users_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s member found", "%s members found", $count),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->$form,
      //'params' => $this->formValues,
    )); ?>
  </div>
</div>

<br />
<?php if(count($this->paginator)>0):?>
<div class="ynbanmem_table_form">
<form id='multimodify_form' method="post" action="">
  <table class='ynbanmem_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Creation IP") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Last login IP") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('', 'ASC');"><?php echo $this->translate("Notice") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('', 'ASC');"><?php echo $this->translate("Warning") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('', 'ASC');"><?php echo $this->translate("Infraction") ?></a></th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ):
			$creation_ip = Engine_IP::normalizeAddress($item->creation_ip);
			$lastlogin_ip = Engine_IP::normalizeAddress($item->lastlogin_ip);
        
          ?>
          <tr>
            <td><input <?php if ($item->level_id == 1) echo 'disabled';?> name='modify_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'></td>
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($item->getHref(),
                  $this->string()->truncate($item->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
            <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td class='admin_table_email'>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td><?php 
             	$ipObj = new Engine_IP($item->creation_ip);
            	echo $ipObj->toString();            
           		?>
           	</td>
            <td><?php 
            	$ipObj = new Engine_IP($item->lastlogin_ip);
            	echo $ipObj->toString();
            	?>
            </td>
			
			<?php 
			$viewer = Engine_Api::_()->user()->getViewer();
			
			$notices = 0;
			$warnings = 0;
			$infractions = 0;
			$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
			$usersTable = Engine_Api::_()->getDbtable('users', 'user');
			$selectLevel = $permissionsTable->select()
		                ->from($permissionsTable,'level_id')
		                ->where('type = ?', 'ynbanmem')
		                ->where('name = ?', 'action')
		                ->query()
		                ->fetchAll();
				
		         $users = $usersTable->select()
		                ->from($usersTable,'user_id')
		                ->where('level_id IN (?)', $selectLevel)
		                ->query()
		                ->fetchAll();
			$bannedEmailTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
			$bannedEmails = $bannedEmailTable->getAllBannedEmails();
			$bannedIpTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
			$bannedIps = $bannedIpTable->getAddresses();
			$bannedUsernameTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
			$bannedUsernames = $bannedUsernameTable->getAllBannedUsers();
			
			//get all
			$rDb = Engine_Api::_()->getDbtable('recipients', 'messages');
			$rName = $rDb->info('name');
			$cDb = Engine_Api::_()->getDbtable('conversations', 'messages');
			$cName = $cDb->info('name');
			$select = $rDb->select()
			  ->from($rName)
			  //>joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
			  ->where("`{$rName}`.`user_id` = ?", $item->user_id)
			  ->where("`{$rName}`.`inbox_deleted` = ?", 0)
			  //->order(new Zend_Db_Expr('outbox_updated DESC'))
			   ->query()
			   ->fetchAll();
			// print_r($select);die;
			foreach( $select as $rec )
			{
			//echo $recipient['outbox_message_id'];die;
			  //$user = Engine_Api::_()->getItem('user', $rec['user_id']);
			   // $message = $rec->getOutboxMessage($user);
				//$recipient = $rec->getRecipientInfo($user);
					$exist = $rDb->select()
					  ->from($rName)
					  //>joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
					  ->where("`{$rName}`.`user_id` IN(?)", $users)
					  ->where("`{$rName}`.`outbox_deleted` = ?", 0)
					  //->order(new Zend_Db_Expr('outbox_updated DESC'))
					   ->query()
					   ->fetchAll();
				if(count($exist) != 0)
				{
					$tb = Engine_Api::_()->getDbTable('extramessage','ynbanmem');
					   // echo $recipient->outbox_message_id;
					$extra = $tb->getExtraMessage($rec['inbox_message_id']);
				
					if(count($extra) == 0)
					{
						continue;
					}
					else
					{
						switch($extra[0]['type'])
						{
							case 1:
								$notices++;
								break;
							case 2:
								$warnings++;
								break;
							case 3:
								$infractions++;
								break;
						}
					}
				}
			}
			
			?>
            <td>
            	<?php echo $notices;?>            	
            </td>
            <td>
            	<?php echo $warnings;?>            	
            </td>
            <td>
            	<?php echo $infractions;?>            	
            </td>
            
            <td class='admin_table_options'>
              <a class='smoothbox' href='<?php echo $this->url(array('controller' => 'index','action' => 'compose', 'to' => $item->user_id, 'format' => 'smoothbox'), 'ynbanmem_general');?>'>
                <?php  if (Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'action') && $item->user_id != $viewer->user_id ) echo $this->translate("Send message") ?>
              </a>
              </br>	
              <a target = '_blank' href='<?php echo $this->url(array('controller' => 'manage','action' => 'ips', 'id' => $item->user_id),'ynbanmem_general');?>'>
                <?php echo $this->translate("View Ips") ?>
              </a>
              
              
              <?php if($item->level_id != 1): ?>
	              </br>	
				 
				 <?php
					$bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
					$bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');

					$isBannedUsername = $bannedUsernamesTable->isUsernameBanned($item->username);
					$isBannedEmail = $bannedEmailsTable->isEmailBanned($item->email);
					
			//        // Build Ban/Unban URL
					$typeURL;
					$banText;
					$bannedEmail;
					$bannedUsername;
					$banned_id;
				 if (Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'ban') && $item->user_id != $viewer->user_id): // @todo change this to look up actual superadmin level ?>
					<a  <?php if($isBannedUsername || $isBannedEmail): ?> class='smoothbox'  <?php else: ?> target = '_blank' <?php endif;?> href='<?php 
					
					if (!$isBannedUsername && !$isBannedEmail) { 
						$banText = 2; // Ban 
						$bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($item->username);
						//$typeURL = 1; // Ban username
						echo $this->url(array('controller' => 'index', 'action' => 'add', 'id' => $item->user_id), 'ynbanmem_general');
					} else {
						if ($isBannedUsername && $isBannedEmail) {

							$banText = 1; // Uban 
							$bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($item->username);
							$bannedUser_id = $bannedUsername['banned_id'];
							$bannedEmail = $bannedEmailsTable->getBannedEmailByEmail($item->email);
							$bannedEmail_id = $bannedEmail['banned_id'];
							//$typeURL = 2; // Unban Username
							echo $this->url(array('controller' => 'index', 'action' => 'unban', 'user' => $bannedUser_id,'email'=>$bannedEmail_id , 'type'=>0), 'ynbanmem_general');
						} else {
							if ($isBannedUsername) {

								$banText = 1; // Uban 
								$bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($item->username);
								$bannedid = $banned_id = $bannedUsername['banned_id'];
								//$typeURL = 3; // Unban Username
								echo $this->url(array('controller' => 'index', 'action' => 'unban', 'id' => $bannedid,'type'=>1), 'ynbanmem_general');
							} else {
								if ($isBannedEmail) {
								
									$banText = 1; //Unabn 
									$bannedEmail = $bannedEmailsTable->getBannedEmailByEmail($item->email);
									
									$bannedid = $banned_id = $bannedEmail['banned_id'];
									//$typeURL = 4; // Unban Email
									echo $this->url(array('controller' => 'index', 'action' => 'unban', 'id' => $bannedid,'type'=>2), 'ynbanmem_general');
								}
							}
						}
					}
						
						?>'> <?php switch($banText)
						{
						case 1:
							echo $this->translate('Unban');
							break;
						case 2:
								if($item->level_id != 1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'ban') && $item->user_id != $viewer->user_id)
									echo $this->translate('Ban');
							break;
						}?>
				</a>
				
				 <?php endif;?>
							 
	              </br>
	              <?php if(!$item->approved):?>
	              <a class='smoothbox' href='<?php echo $this->url(array('controller' => 'manage','action' => 'approve', 'id' => $item->user_id),'ynbanmem_general');?>'>
	                <?php echo $this->translate("Approve") ?>
	              </a>
	              </br>
	              <?php endif;?>	
	              <?php if(!$item->verified):?>              
	              <a class='smoothbox' href='<?php echo $this->url(array('controller' => 'manage','action' => 're-send', 'email' => $item->email),'ynbanmem_general');?>'>
	                <?php echo $this->translate("Resend verification mail") ?>
	              </a>
	              </br>
	              <?php endif;?>	 	
	              <a class='smoothbox' href='<?php echo $this->url(array('controller' => 'manage','action' => 'delete', 'id' => $item->user_id), 'ynbanmem_general');?>'>
	                <?php if (($this->superAdminCount>1 && $item->level_id==1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'delete')) || ( $item->level_id !=1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'delete')) ) 
								echo $this->translate("Delete") ?>
	              </a>
             <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='ynbanmem_buttons'>    
    <button type='button'  name="submit_button" value="delete" style="float: right;" onclick="multiDelete('<?php echo $this->url(array('action' => 'multi-delete'));?>')"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
</div>
<?php else:?>
	<div class="tip">
      <span>
           <?php echo $this->translate('There are no user yet.');?>
      </span>
           <div style="clear: both;"></div>
    </div>	
<?php endif; ?>



