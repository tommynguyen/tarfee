<?php 
$this->headTranslate(array(
		"Do you want to delete this message in queue?"
));
?>

<script type="text/javascript">
function queue_delete(id) {
	var opt = confirm(en4.core.language.translate("Do you want to delete this message in queue?"));
	if (opt == false)
	{
		return;
	}
	
	var elm_id = 'queue_'+id;
	var htmlTemp = $(elm_id).get("html");
	
	$(elm_id).innerHTML= "<img style='margin-top:4px;' src='application/modules/Contactimporter/externals/images/loading1.gif'></img>";
    new Request.JSON({
      'url' : en4.core.baseUrl + 'contactimporter/queuedelete',
      'data' : {
        'format' : 'json',
        'task': 'queuedelete',
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

function show(obj_id) {
    $(obj_id).style.display = '';
}
function hide(obj_id) {
   $(obj_id).style.display = 'none';
}
</script>

<?php if ($this->paginator->getTotalItemCount() == 0): ?>
  <div class="tip">
    <span><?php echo $this->translate("You do not have any queue messages at this time."); ?></span>
  </div>
<?php else: ?>
	<table class="contactimporter_list_pending_invitations">
		<tr style="background-color: #5ba1cd;">
            <td style="padding-left: 10px;">
            	<strong>
					<div><?php echo $this->translate("Contacts"); ?></div>
				</strong>
			</td>
			<td style="text-align: center;">
        		<strong><?php echo $this->translate("Type"); ?></strong>
        	</td>
        	<td class="pending_invitation_options">
        		<strong><?php echo $this->translate("Options"); ?></strong>
        	</td>
		</tr>
		
  		<?php foreach($this->paginator as $contact): ?>
  			<tr id="queue_<?php echo $contact['queue_id'] . "_" . $contact['uid'] ?>">
  				<td style="padding-left: 10px;">
  					<div title="<?php echo $contact['uname']?>">
                    	<?php echo $contact['uname']; ?>
                    </div>
  				</td>
  				<td style="text-align: center;">
  					<i class="contactimporter_queue_item contactimporter_queue_<?php echo $contact['service'] ?>"></i>
  				</td>
  				<td class="pending_invitation_options">
  					<a href="javascript:queue_delete('<?php echo $contact['queue_id'] . "_" . $contact['uid'] ?>')" class="pending_invite_delete" title="<?php echo $this->translate('Delete') ?>"><?php echo $this->translate('Delete') ?></a>
  				</td>
  			</tr>
  		<?php endforeach; ?>
	</table>
	
	<div style="padding-bottom:0px; padding-top:10px; padding-right: 0px;">
	        <?php echo $this->paginationControl($this->paginator); ?>
	</div>
	
<?php endif; ?>