<script type="text/javascript">
    
function invitation_delete(invite_id) {
	$('invite_'+invite_id).innerHTML= "<img style='margin-top:4px;' src='application/modules/Contactimporter/externals/images/loading1.gif'></img>";
    new Request.HTML({
      'url' : en4.core.baseUrl + 'contactimporter/invitationdelete',
      'data' : {
        'format' : 'json',
        'task': 'invitationdelete',
        'id': invite_id
      },
      onSuccess : function() {
           location.reload();
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
    <span><?php echo $this->translate("You do not have any queue emails at this time."); ?></span>
  </div>
<?php else: ?>
	<table class="contactimporter_list_pending_invitations">
		<tr style="background-color: #5ba1cd;">
            <td style="padding-left: 10px;">
            	<strong>
					<div><?php echo $this->translate("Email"); ?></div>
				</strong>
			</td>
			<td style="text-align: center;">
        		<strong><?php echo $this->translate("Sent date"); ?></strong>
        	</td>
        	<td class="pending_invitation_options">
        		<strong><?php echo $this->translate("Options"); ?></strong>
        	</td>
		</tr>
		
  		<?php foreach($this->paginator as $invitation): ?>
  			<tr id="invite_<?php echo $invitation->getIdentity(); ?>">
  				<td style="padding-left: 10px;">
  					<div title="<?php echo $invitation->email; ?>" class="contactimporter_queue_email" style="padding-left: 25px;">
                    <?php echo $invitation->email;?>
                    </div>
  				</td>
  				<td style="text-align: center;">
  					<?php echo $this->timestamp($invitation->creation_date) ?>
  				</td>
  				<td class="pending_invitation_options">
  					<a href="javascript:invitation_delete('<?php echo $invitation->getIdentity() ?>')" class="pending_invite_delete" title="<?php echo $this->translate('Delete') ?>"><?php echo $this->translate('Delete') ?></a>
  				</td>
  			</tr>
  		<?php endforeach; ?>
	</table>
	
	<div style="padding-bottom:0px; padding-top:10px; padding-right: 0px;">
	        <?php echo $this->paginationControl($this->paginator); ?>
	</div>
	
<?php endif; ?>