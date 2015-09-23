<?php $paginater_vari = 0; if( !empty($this->user_obj)) {  $paginater_vari = $this->user_obj->getCurrentPageNumber(); }  ?>

<script type="text/javascript">
 var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
 var call_status = '<?php echo $this->call_status; ?>';
 var resource_id = '<?php echo $this->resource_id; ?>';
 var resource_type = '<?php echo $this->resource_type; ?>';
 var url = en4.core.baseUrl + 'yncomment/like/dislikelist';

 en4.core.runonce.add(function() {
    document.getElementById('like_members_search_input').addEvent('keyup', function(e) {
		$('likes_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Yncomment/externals/images/loading.gif" alt="" style="margin-top:10px;" /></center>';
            var request = new Request.HTML({
           'url' : url,
                'data' : {
                    'format' : 'html',
    				'resource_type' : resource_type,
    				'resource_id' : resource_id,
    				'call_status' : call_status,
                    'search' : this.value,
    				'is_ajax':1,
                    'showLikeWithoutIconInReplies': '<?php echo $this->showLikeWithoutIconInReplies ;?>',
                    'other': '<?php echo $this->other;?>',
                    'notIncludedId': '<?php echo $this->notIncludedId;?>'
                },
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			document.getElementById('likes_popup_content').innerHTML = responseHTML;
			en4.core.runonce.trigger();
			}
				});			
        request.send();
    });
  });

 var paginateLikeMembers = function(page, call_status) {
		var search_value = $('like_members_search_input').value;
		if (search_value == '') {
			search_value = '';
		}
		var request = new Request.HTML({
		'url' : url,
			'data' : {
				'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'search' : search_value,
				'call_status' : call_status,
				'page' : page,
				'is_ajax':1,
                'showLikeWithoutIconInReplies': '<?php echo $this->showLikeWithoutIconInReplies ;?>',
                'other': '<?php echo $this->other;?>',
                'notIncludedId': '<?php echo $this->notIncludedId;?>'
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('likes_popup_content').innerHTML = responseHTML;
				en4.core.runonce.trigger();
			}
		});					
		request.send();
  }

 //Showing 'friend' which liked this content.
 var likedStatus = function(call_status) {
  var request = new Request.HTML({
   'url' : url,
      'data' : {
        'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'call_status' : call_status,
                'showLikeWithoutIconInReplies': '<?php echo $this->showLikeWithoutIconInReplies ;?>',
                'other': '<?php echo $this->other;?>',
                'notIncludedId': '<?php echo $this->notIncludedId;?>'
            },
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			document.getElementById('like_members_profile').getParent().innerHTML = responseHTML;
			en4.core.runonce.trigger();
			}
		});		
		request.send();
  }
</script>
</div>

<?php  if(empty($this->is_ajax)) { ?>
<a id="like_members_profile" style="posituin:absolute;"></a>
<div class="yncomment_members_popup">
	<div class="top">
		<?php
			if($this->call_status == 'public')	
			{
                if($this->showLikeWithoutIconInReplies == 3) 
    				$title = $this->translate('People who have voted down for this');
                else 
                    $title = $this->translate('People who dislike this');
			}	
			else	
			{
                if($this->showLikeWithoutIconInReplies == 3) 
				    $title = $this->translate('Friends who have voted down for this');
                else 
                    $title = $this->translate('Friends who dislike this');
			}
		?>
		<div class="heading"><?php echo $title; ?></div>
		<div class="yncomment_members_search_box">
        <div class="link" style="display: none;">
	    	<a href="javascript:void(0);" class="<?php if($this->call_status == 'public') { echo 'selected'; } ?>" id="show_all" onclick="likedStatus('public');"><?php echo $this->translate('All '); ?>(<?php echo number_format($this->public_count); ?>)</a>
				<a href="javascript:void(0);" class="<?php if($this->call_status == 'friend') { echo 'selected'; } ?>" onclick="likedStatus('friend');"><?php echo $this->translate('Friends '); ?>(<?php echo number_format($this->friend_count); ?>)</a>
			</div>
			<div class="yncomment_members_search fright" style="display: none;">
                <input id="like_members_search_input" type="text" value="" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';" placeholder="<?php echo $this->translate('Search Members')?>"/>
			</div>
		</div>
	</div>
	<div class="yncomment_members_popup_content" id="likes_popup_content">
		<?php } ?>
    <?php if( !empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
				<?php if( $this->user_obj->getCurrentPageNumber() > 1 ): ?>
					<div class="yncomment_members_popup_paging">
						<div id="user_like_members_previous" class="paginator_previous">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
								'onclick' => 'paginateLikeMembers(likeMemberPage - 1, call_status)'
							)); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php  endif; ?>
		<?php $count_user = count($this->user_obj);
				if(!empty($count_user)) {
					foreach( $this->user_obj as $user_info ) { ?>
				<div class="item_member">
					<div class="item_member_thumb">
						<?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo seao_common_add_tooltip_link', 'target' => '_parent', 'title' => $user_info->getTitle(), 'rel'=> 'user'.' '.$user_info->getIdentity()));?>
					</div>
					<div class="item_member_details">
						<div class="item_member_name">
							<?php  $title1 = $user_info->getTitle(); ?>
							<?php  $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1?>
							<?php echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$user_info->getIdentity())); ?>
						</div>
					</div>	
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
				if(!empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
					<?php if( $this->user_obj->getCurrentPageNumber() < $this->user_obj->count() ): ?>
						<div class="yncomment_members_popup_paging">
							<div id="user_like_members_next" class="paginator_next" style="border-top-width:1px;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
									'onclick' => 'paginateLikeMembers(likeMemberPage + 1, call_status)'
								)); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
<?php if(empty($this->is_ajax)) { ?>
	</div>
</div>
<div class="yncomment_members_popup_bottom">
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>
<?php } ?>