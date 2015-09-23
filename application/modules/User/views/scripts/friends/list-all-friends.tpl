<?php $paginater_vari = 0; if( !empty($this->friends)) {  $paginater_vari = $this->paginator->getCurrentPageNumber(); }  ?>

<script type="text/javascript">
 var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
 var url = en4.core.baseUrl + 'user/friends/list-all-friends';

 var paginateFriends = function(page) {
		var request = new Request.HTML({
		'url' : url,
			'data' : {
				'format' : 'html',
				'page' : page,
				'is_ajax':1,
				'user_id': <?php echo $this -> subject() -> getIdentity()?>
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('friends_popup_content').innerHTML = responseHTML;
				en4.core.runonce.trigger();
			}
		});					
		request.send();
  }
</script>
<div style="width: 700px">
<?php  if(empty($this->is_ajax)) { ?>
<div class="friends_members_popup">
	<div class="top">
		<?php
            $title = $this->translate('Friends who friend with you');
			if(!$this -> viewer() -> isSelf($this -> subject()))
			{
				$title = $this->translate('Friends who friend with you %s', $this -> subject() -> getTitle());
			}
		?>
		<div class="heading"><?php echo $title; ?></div>
	</div>
	<div class="friends_members_popup_content" id="friends_popup_content">
		<?php } ?>
    <?php if( !empty($this->friends) && count($this->friends) > 0 ): ?>
				<?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
					<div class="friends_members_popup_paging">
						<div id="user_friends_previous" class="paginator_previous">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
								'onclick' => 'paginatefriends(likeMemberPage - 1)'
							)); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php  endif; ?>
		<?php $count_user = count($this->friends);
				if(!empty($count_user)) {
					foreach( $this->friends as $user_info ) { ?>
						<div class="item_member">
							<div class="item_member_thumb">
								<?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo', 'target' => '_parent', 'title' => $user_info->getTitle(), 'rel'=> 'user'.' '.$user_info->getIdentity()));?>
							</div>
							<div class="item_member_details">
								<div class="item_member_name">
									<?php echo $this->htmlLink($user_info->getHref(), $this -> string() -> truncate($user_info->getTitle(), 20), array('title' => $user_info->getTitle(), 'target' => '_parent', 'class' => '', 'rel'=> 'user'.' '.$user_info->getIdentity())); ?>
								</div>
							</div>	
							<?php if($this -> viewer() -> isSelf($this -> subject())):?>
							<div class="item_member_options">
								<?php echo $this->htmlLink(array(
						            'route' => 'user_extended',
						            'controller' => 'friends',
						            'action' => 'remove',
						            'user_id' => $user_info->getIdentity(),
						        ), $this->translate('Unfriend'), array(
						            'class' => 'buttonlink smoothbox'
						        ));
				        		?>
				        		<?php echo $this->htmlLink($user_info->getHref(), $this -> translate('Visit Profile'), array('title' => $this -> translate('Visit Profile'), 'target' => '_parent', 'class' => '')); ?>
								
								<?php echo $this->htmlLink(array(
						            'route' => 'messages_general',
						            'action' => 'compose',
						            'to' => $user_info->getIdentity()
						        ), $this->translate('Send Message'), array(
						            'class' => 'buttonlink smoothbox'
						        ));
				        		?>
				        		
								<?php echo $this->htmlLink(array(
						            'route' => 'user_extended',
						            'controller' => 'block',
						            'action' => 'add',
						            'user_id' => $user_info->getIdentity()
						        ), $this->translate('Block'), array(
						            'class' => 'buttonlink smoothbox'
						        ));
				        		?>
				        		
				        		<?php echo $this->htmlLink(array(
						            'route' => 'default',
						            'module' => 'core',
						            'controller' => 'report',
						            'action' => 'create',
						            'subject' => $user_info->getGuid()
						        ), $this->translate('Report Abuse'), array(
						            'class' => 'buttonlink smoothbox'
						        ));
				        		?>
							</div>
							<?php endif;?>
					</div>
				<?php	}
			 } else { ?>
			<div class='tip' style="margin:10px 0 0 140px;"><span>
			 		<?php
			 			echo $this->no_result_msg;
			 		?>
			 </span></div>
			<?php } ?>
			<?php 
				if(!empty($this->friends) && $this->paginator->count() > 1 ): ?>
					<?php if( $this->paginator->getCurrentPageNumber() < $this->friends->count() ): ?>
						<div class="ynfeed_members_popup_paging">
							<div id="user_like_members_next" class="paginator_next" style="border-top-width:1px;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
									'onclick' => 'paginateFriends(likeMemberPage + 1)'
								)); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
<?php if(empty($this->is_ajax)) { ?>
	</div>
</div>
<div class="friends_members_popup_bottom">
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>
<?php } ?>
</div>