<script type="text/javascript">
    
function invite_delete(invite_id) {
	$('invite_'+invite_id).innerHTML= "<img style='margin-top:4px;' src='application/modules/Contactimporter/externals/images/loading1.gif'></img>";
    new Request.HTML({
      'url' : en4.core.baseUrl + 'contactimporter/invitedelete',
      'data' : {
        'format' : 'json',
        'task': 'invitedelete',
        'id': invite_id
      },
      onSuccess : function() {
           location.reload();
      }  
    }).send();
}
function invite_resend(invite_id) {
    hide(invite_id + "_link");
    show(invite_id + "_progress");
    new Request.HTML({
      'url' : en4.core.baseUrl + 'contactimporter/inviteresend',
      'data' : {
        'format' : 'json',
        'task': 'inviteresend',
        'id': invite_id
        
      },
      onSuccess : function(response) 
      {
            hide(invite_id + "_progress");
            show(invite_id + "_link");
            //$(invite_id + "_link").innerHTML = '<?php echo $this->translate("Re-sent")?>';
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

<?php if( count($this->navigation) ): ?>
<div class="headline">
  <h2>
	<?php echo $this->translate('Invite Your Friends');?>
  </h2>
  <div class="tabs">
	<?php
	  // Render the menu
	  echo $this->navigation()
		->menu()
		->setContainer($this->navigation)
		->render();
	?>
  </div>
</div>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() == 0): ?>

  <div class="tip">
    <span><?php echo $this->translate("You do not have any pending invitations at this time."); ?></span>
  </div>

<?php else: ?>
  
<div style="text-align: center;">
<div style=" padding-bottom:16px; padding-top:0px; padding-right: 0px; padding-left:25px;">
        <?php echo $this->paginationControl($this->paginator); ?>
</div>
<table align="center" class="pending_invite_table"><tr><td>
  <?php foreach($this->paginator as $invite) { ?>
	<div id="invite_<?php echo $invite['id'] ?>" class="clearfix" style="background-position: top left; background-repeat:no-repeat;background-image:url('application/modules/Contactimporter/externals/images/mail.jpg'); max-width:550px; border-bottom: 1px solid #DEDEDE;margin-bottom:5px; padding: 0px 0px 5px 30px;">

	
	<div class='profile_action_date'>
        <div style="float: left;">
            <table>
            <tr>
                <td>
                   <div style="width: 200px; overflow: hidden;" title="<?php echo $invite['recipient']?>">
                    <?php
                    $invite_email = $invite['recipient'];
                    $cut_email = (strlen($invite_email) < 25 ? $invite_email : (substr($invite_email, 0, 25) . '...') );
                    echo $cut_email;
                    ?>
                    </div>
                </td>
                <td>
                    &nbsp;&nbsp; <?php echo $this->timestamp($invite['timestamp']) ?> &nbsp;&nbsp;
                </td>
            </tr>
            </table>
        </div>
      <div style="float: right;">
	  <a href="javascript:invite_delete('<?php echo $invite['id'] ?>')" class="pending_invite_delete" title="<?php echo $this->translate('Delete') ?>"><?php echo $this->translate('Delete') ?></a>
	  <span style="padding-left: 2px;padding-right: 2px;"> | </span>
	  <span id="<?php echo $invite['id'] ?>_link">
	  <a href="javascript:invite_resend('<?php echo $invite['id'] ?>')" class="pending_invite_resend" title="<?php echo $this->translate('Resend invitation') ?>"><?php echo $this->translate('Resend invitation') ?></a>
	  </span>
	  <span id="<?php echo $invite['id'] ?>_progress" style="display:none; height: 18px; background: url() left no-repeat; padding-left: 20px">
        <img src="application/modules/Contactimporter/externals/images/loading_small.gif" alt="" height="20px">
		<?php echo $this->translate('Sending...') ?>
	  </span>
      </div>
	</div>
	</div>
  <?php } ?>
</td></tr>
</table>

<div style="padding-bottom:0px; padding-top:16px; padding-right: 0px; padding-left:25px;">
        <?php echo $this->paginationControl($this->paginator); ?>
</div>

</div>
<?php endif; ?>