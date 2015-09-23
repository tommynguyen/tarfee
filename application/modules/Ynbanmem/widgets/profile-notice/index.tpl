

<?php if( count($this->paginator) ): ?>
 <?php $flag = false;?>
  <div class="messages_list">
    <ul>
      <?php foreach( $this->paginator as $conversation ):
	  $conversation = Engine_Api::_()->getItemTable('messages_conversation')->find($conversation['conversation_id'])->current();
      $user = Engine_Api::_()->getItem('user', $conversation->user_id);
        $message = $conversation->getOutboxMessage($user);
        $recipient = $conversation->getRecipientInfo($user);
		$tb = Engine_Api::_()->getDbTable('extramessage','ynbanmem');
               // echo $recipient->outbox_message_id;
		$extra = $tb->getExtraMessage($recipient->outbox_message_id);
		if(count($extra) == 0)
			continue;
		
        $resource = "";
        $sender   = "";
        if( $conversation->hasResource() &&
                  ($resource = $conversation->getResource()) ) {
          $sender = $resource;
        } else if( $conversation->recipients > 1 ) {
          $sender = $user;
        } else {
          foreach( $conversation->getRecipients() as $tmpUser ) {
            if( $tmpUser->getIdentity() != $user->getIdentity() ) {
              $sender = $tmpUser;
              break;
            }
          }
        }
        if( (!isset($sender) || !$sender) ){
          if( $user->getIdentity() !== $conversation->user_id ){
            $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
          } else {
            $sender = $user;
          }
        }
        if( !isset($sender) || !$sender ) {
          //continue;
          $sender = new User_Model_User(array());
        }
        ?>
        <li<?php if( !$recipient->inbox_read ): ?> class='messages_list_new'<?php endif; ?> id="message_conversation_<?php echo $conversation->getIdentity() ?>">
		<?php $flag = true;	?>
          <div class="messages_list_photo">
            <?php echo $this->htmlLink($sender->getHref(), $this->itemPhoto($sender, 'thumb.icon')) ?>
          </div>
          <div class="messages_list_from">
            <p class="messages_list_from_name">
              <?php if( !empty($resource) ): ?>
                <?php echo $resource->toString() ?>
              <?php elseif( $conversation->recipients == 1 ): ?>
                <?php echo $this->htmlLink($sender->getHref(), $sender->getTitle()) ?>
              <?php else: ?>
                <?php echo $this->translate(array('%s person', '%s people', $conversation->recipients),
                    $this->locale()->toNumber($conversation->recipients)) ?>
              <?php endif; ?>
            </p>
            <p class="messages_list_from_date">
              <?php echo $this->timestamp($message->date) ?>
            </p>
          </div>
          <div class="messages_list_from">
            <p class="messages_list_from_name">
              <?php 
                // ... scary
                ( (isset($message) && '' != ($title = trim($message->getTitle()))) ||
                  (isset($conversation) && '' != ($title = trim($conversation->getTitle()))) ||
                  $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
              ?>
			   <?php switch($extra[0]['type'])
					{
						case 1:
							echo $this->translate('Notice by ');
						break;
						case 2:
							echo $this->translate('Warning by ');
						break;
						case 3:
							echo $this->translate('Infraction by');
						break;
					}
				?>
				<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
            </p>
			<p> </p>
             <p class="messages_list_from_date" title="reason">
              <?php if($extra[0]['type'] == 2 )
							echo $extra[0]['reason']; ?>
            </p>
          </div>
		  
		  <div class="messages_list_info">
            <p class="messages_list_info_title">
              <?php
                // ... scary
                ( (isset($message) && '' != ($title = trim($message->getTitle()))) ||
                  (isset($conversation) && '' != ($title = trim($conversation->getTitle()))) ||
                  $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
              ?>
              <?php echo $this->htmlLink($conversation->getHref(), $title) ?>
            </p>
            <p class="messages_list_info_body">
              <?php echo html_entity_decode($message->body) ?>
            </p>
          </div>
		  
		  
        </li>
		
      <?php endforeach; ?>
    </ul>
  </div >
  <?php if($flag):?>
	<div style="float: right; margin-right: 121px;"><a   href='<?php 
	 echo $this->url(array('action' => 'notice'), 'ynbanmem_general');?>'>
	  <?php echo $this->translate('View more') ?>
	</a>
	</div>
	<?php else:?>
	 <p><?php echo $this->translate(array('You have 0 sent message total', 'You have 0 sent messages total', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?></p>
  <br />
	<?php endif;?>
  <br />
  <!--button id="delete"><?php echo $this->translate('Delete Selected');?></button-->
  <script type="text/javascript">
  <!--
  $('delete').addEvent('click', function(){
    var selected_ids = new Array();
    $$('div.messages_list input[type=checkbox]').each(function(cBox) {
      if (cBox.checked)
        selected_ids[ selected_ids.length ] = cBox.value;
    });
    var sb_url = '<?php echo $this->url(array('action'=>'delete'), 'messages_general', true) ?>?place=outbox&message_ids='+selected_ids.join(',');
    if (selected_ids.length > 0)
      Smoothbox.open(sb_url);
  });
  //-->
  </script>
  <br />
  <br />

<?php else: ?>
  <p><?php echo $this->translate(array('You have %s sent message total', 'You have %s sent messages total', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?></p>
  <br />
<?php endif; ?>


