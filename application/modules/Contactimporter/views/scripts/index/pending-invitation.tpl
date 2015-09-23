<?php 
$this->headTranslate(array(
		"Do you want to delete this invitation?",
		"Do you want to delete invitations?",
		"Please choose any invitations to delete?",
		"Please choose any invitations to send?",
		"Sent invitations successfully!",
));
?>

<script type="text/javascript">
function set_check()
{
	current_status = $("pending_invitaions_select_all").checked;
	$$("input[name='pending_invitaions[]']").each(function(el) { el.checked = current_status; })
}

function reset_cb_all(el)
{
	if (el.checked == false)
	$("pending_invitaions_select_all").checked = false;
}

function invitations_send()
{
	checkedItems = $$("input[name='pending_invitaions[]']:checked");
	if (checkedItems.length > 0)
	{
		var ids = [];
		checkedItems.each(function(i, e) {
			if (i.get("invitation_type") == "email")
			{
				ids.push(i.get("value"));	
			}
		});

		if (ids.length == 0)
		{
			alert(en4.core.language.translate("Please choose any invitations to send?"));
			return;	
		}		
	}
	else
	{
		alert(en4.core.language.translate("Please choose any invitations to send?"));
		return;
	}
	
	new Request.JSON({
      'url' : en4.core.baseUrl + 'contactimporter/invitationsend',
      'data' : {
        'format' : 'json',
        'task': 'invitationsend',
        'id': ids.join()
        
      },
      onSuccess : function(response) 
      {
          alert(en4.core.language.translate("Sent invitations successfully!"))
      }
    }).send();
	
}

function invitations_delete()
{
	checkedItems = $$("input[type=checkbox]:checked");
	if (checkedItems.length == 0)
	{
		alert(en4.core.language.translate("Please choose any invitations to delete?"));
		return;
	} 
	
	var opt = confirm(en4.core.language.translate("Do you want to delete invitations?"));
	if (opt == false)
	{
		return;
	}
	var ids = [];
	$$("input[name='pending_invitaions[]']:checked").each(function(i, e) {
	    ids.push(i.get("value"));
	});
	
	if (ids.length > 0)
	{
		new Request.JSON({
	      'url' : en4.core.baseUrl + 'contactimporter/invitationdelete',
	      'data' : {
	        'format' : 'json',
	        'task': 'invitationdelete',
	        'id': ids.join()
	      },
	      onSuccess : function(responseJSON, responseText) {
	          location.reload();
	      }  
	    }).send();
	}
}

function invitation_delete(id) {
	var opt = confirm(en4.core.language.translate("Do you want to delete this invitation?"));
	if (opt == false)
	{
		return;
	}
	
	var elm_id = 'invitation_'+id;
	var htmlTemp = $(elm_id).get("html");
	
	$(elm_id).innerHTML= "<img style='margin-top:4px;' src='application/modules/Contactimporter/externals/images/loading1.gif'></img>";
    new Request.JSON({
      'url' : en4.core.baseUrl + 'contactimporter/invitationdelete',
      'data' : {
        'format' : 'json',
        'task': 'invitationdelete',
        'id': id
      },
      onSuccess : function(responseJSON, responseText) {
          if(responseJSON.error > 0)
          {
        	  $(elm_id).innerHTML = htmlTemp;
          }
          else
          {
        	  $(elm_id).remove();
        	  location.reload();
          }
      }  
    }).send();
}
function invitation_resend(invite_id) {
    hide(invite_id + "_link");
    show(invite_id + "_progress");
    new Request.HTML({
      'url' : en4.core.baseUrl + 'contactimporter/invitationsend',
      'data' : {
        'format' : 'json',
        'task': 'invitationsend',
        'id': invite_id
        
      },
      onSuccess : function(response) 
      {
            hide(invite_id + "_progress");
            show(invite_id + "_link");
      }
    }).send();
}
function show(obj_id) {
    $(obj_id).style.display = '';
}
function hide(obj_id) {
   $(obj_id).style.display = 'none';
}
</script>

<?php if ($this->paginator->getTotalItemCount() == 0): ?>
  <div class="tip">
    <span><?php echo $this->translate("You do not have any pending invitations at this time."); ?></span>
  </div>
<?php else: ?>
	<table class="contactimporter_list_pending_invitations" style="table-layout: fixed">
		<tr style="background-color: #5ba1cd;">
        	<td class="cb_select">
        		<input type="checkbox" name="pending_invitaions_select_all" id="pending_invitaions_select_all" value="0" onclick="javascript:set_check();">
        	</td>
            <td>
            	<strong>
					<div style="width: 200px; overflow: hidden; text-overflow: ellipsis;"><?php echo $this->translate("Contacts/Emails"); ?></div>
				</strong>
			</td>
			<td style="text-align: center;">
        		<strong><?php echo $this->translate("Type"); ?></strong>
        	</td>
        	<td class="pending_invitation_options">
        		<strong><?php echo $this->translate("Options"); ?></strong>
        	</td>
		</tr>
		<?php $hasEmailInvitation = false;?>
  		<?php foreach($this->paginator as $invitation): ?>
  			<tr id="invitation_<?php echo $invitation->invitation_id ?>">
  				<td class="cb_select">
  					<input type="checkbox" invitation_type="<?php echo $invitation->type; ?>" name="pending_invitaions[]" onclick="javascript:reset_cb_all(this);" id="cb_pending_invitation_<?php echo $invitation->invitation_id ?>" value="<?php echo $invitation->invitation_id ?>" />
  				</td>
  				
  				<td>
  					<?php if($invitation->type == 'email'): ?>
	                		<?php $hasEmailInvitation = true;?>
	                		<div style="overflow: hidden; text-overflow: ellipsis;" title="<?php echo $invitation->email?>">
			                    <?php
			                    $email = $invitation->email;
			                    echo $email;
			                    ?>
		                    </div>
		                <?php else: ?>
		                	<?php $ustring = ($invitation->uname) ? ($invitation->uname) : ($invitation->service . "/" . $invitation->uid)?>
		                    <div style="overflow: hidden;" title="<?php echo $ustring ?>">
			                    <?php
			                    $uname = $ustring;
			                    echo $uname;
			                    ?>
		                    </div>
	                	<?php endif; ?>
  				</td>
  				<td style="text-align: center;">
  					<i class="contactimporter_queue_item contactimporter_queue_<?php echo $invitation->service; ?>"></i>
  				</td>
  				<td class="pending_invitation_options">
  					<?php if($invitation->type == 'email') : ?>
				      	<span id="<?php echo $invitation->invitation_id; ?>_link">
							<a href="javascript:invitation_resend('<?php echo $invitation->invitation_id; ?>')" class="pending_invite_resend" title="<?php echo $this->translate('Resend') ?>"><?php echo $this->translate('Resend') ?></a>
						</span>
						<span id="<?php echo $invitation->invitation_id; ?>_progress" style="display:none; height: 18px; background: url() left no-repeat; padding-left: 20px">
				        	<img src="application/modules/Contactimporter/externals/images/loading_small.gif" alt="" height="20px">
					  	</span> 
						&nbsp;|&nbsp;
      				<?php endif; ?>
      	
					<a href="javascript:invitation_delete('<?php echo $invitation->invitation_id ?>')" class="pending_invite_delete" title="<?php echo $this->translate('Delete') ?>"><?php echo $this->translate('Delete') ?></a>
  				</td>
  			</tr>
  		<?php endforeach; ?>
	</table>
	
	<div style="margin-top: 10px;">
		<?php if($hasEmailInvitation): ?>
			<button onclick="invitations_send()"><?php echo $this->translate("Resend"); ?></button>
		<?php endif; ?>
		<button onclick="invitations_delete()"><?php echo $this->translate("Delete"); ?></button>
	</div>
	
	<div style="padding-bottom:0px; padding-top:10px; padding-right: 0px;">
	        <?php echo $this->paginationControl($this->paginator); ?>
	</div>
	
	
<?php endif; ?>
