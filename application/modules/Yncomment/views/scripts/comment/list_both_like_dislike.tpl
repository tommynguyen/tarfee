<?php
    $replyLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('yncomment.reply.link', 1);
    if(!empty($this->photoLightboxComment) ) {
        $replyLink = 0;
    }
?>
<?php $this->headTranslate(array('This comment has been hidden.', 'Unhide', 'This reply has been hidden.')); ?>
<?php if (empty($this->parent_div)): ?>
  <div id="parent_div" class="yncomment_replies_wrapper">
<?php endif; ?>
<?php if (!$this->page): ?>
    <div class='yncomment_replies <?php if ($this->parent_comment_id): ?>yncomment_replies_child<?php endif; ?> <?php if (!$this->nestedCommentPressEnter): ?>yncomment_press_enter<?php endif; ?>' id="comments_<?php echo $this->nested_comment_id ?>">
<?php endif; ?>
<?php if (empty($this->parent_comment_id)): ?>
    <div class='yncomment_replies_options yncomment_txt_light'>
	<?php if ($this->viewer()->getIdentity()): ?>
    	<?php if ($this->canComment): ?>
	        <div class="yncomment_float_left">
			<!--WITHOUT ICON-->
			<?php if($this->showLikeWithoutIcon):?>
				<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($this->subject,$this->viewer())):?>
					<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?> onclick="en4.yncomment.yncomments.like('<?php echo
					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>>
	                <b class="yn_like_dislike"><?php echo  $this->translate('Like') ?></b>
					</a>
				<?php else:?>
					<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block; color: #2A6496" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?> onclick="en4.yncomment.yncomments.undolike('<?php echo
					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>>
	                <b class="yn_like_dislike"><?php echo  $this->translate('Like') ?></b>
				<?php endif;?>
			<?php else:?>
				<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($this->subject,$this->viewer())):?>
					<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.like('<?php echo
					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>" >
						<i class="fa fa-thumbs-up ynfontawesome"></i> <?php //echo $this->translate('Like') ?>
					</a>
				<?php else:?>
						<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block; color: #fff !important;background:#f63; " href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.undolike('<?php echo
						$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>" >
							<i class="fa fa-thumbs-up ynfontawesome"></i> <?php //echo $this->translate('Like') ?>
						</a>
				<?php endif;?>
			<?php endif;?>
			<!-- unsure 
			&nbsp;&middot;&nbsp; 
	    		<!--WITHOUT ICON
	    		<?php if($this->showLikeWithoutIcon):?>
	    			<?php if(Engine_Api::_()->getDbtable('unsures', 'yncomment')->getUnsure($this->subject, $this->viewer())):?>
	    				<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;color: #2A6496" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.undounsure('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><?php echo $this->translate('Unsure') ?>
	    					</a>
	    			<?php else :?>
	    					<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unsure('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><?php echo $this->translate('Unsure') ?>
	    					</a>
	    			<?php endif;?>
	    		<?php else:?>
	    			<?php if(Engine_Api::_()->getDbtable('unsures', 'yncomment')->getUnsure($this->subject, $this->viewer())):?>
	    				<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;color: #2A6496" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.undounsure('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><i class="fa fa-meh-o ynfontawesome"></i> <?php echo $this->translate('Unsure') ?>
	    					</a>
	    			<?php else :?>
	    					<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unsure('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><i class="fa fa-meh-o ynfontawesome"></i> <?php echo $this->translate('Unsure') ?>
	    					</a>
	    			<?php endif;?>
	    		<?php endif; ?>
	    		-->
	           
	    		<!--WITHOUT ICON-->
	    		<?php if($this->showLikeWithoutIcon):?>
	    			<?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($this->subject, $this->viewer())):?>
	    				<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block; color: #2A6496" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.undounlike('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><?php echo $this->translate('Dislike') ?>
	    					</a>
	    			<?php else :?>
	    					<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><?php echo $this->translate('Dislike') ?>
	    					</a>
	    			<?php endif;?>
	    		<?php else:?>
	    			<?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($this->subject, $this->viewer())):?>
	    				<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block; color: #fff !important;background:#f63;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.undounlike('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><i class="fa fa-thumbs-down ynfontawesome"></i> <?php //echo $this->translate('Dislike') ?>
	    					</a>
	    			<?php else :?>
	    					<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike('<?php echo
	    					$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><i class="fa fa-thumbs-down ynfontawesome"></i> <?php //echo $this->translate('Dislike') ?>
	    					</a>
	    			<?php endif;?>
	    		<?php endif; ?>
	            </div><!--floatleft-->
	        <?php endif; ?>
	    <?php else:?>
	    	<div class="yncomment_float_left">
			<!--WITHOUT ICON-->
			<?php if($this->showLikeWithoutIcon):?>
				<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
                <b class="yn_like_dislike"><?php echo  $this->translate('Like') ?></b>
			<?php else:?>
				<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
					<i class="fa fa-thumbs-up ynfontawesome"></i> <?php echo $this->translate('Like') ?>
				</a>
			<?php endif;?>
			<!-- 
			&nbsp;&middot;&nbsp; 
	    		<?php if($this->showLikeWithoutIcon):?>
					<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
						<?php echo $this->translate('Unsure') ?>
					</a>
	    		<?php else:?>
					<a id="unsure_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
						<i class="fa fa-meh-o ynfontawesome"></i> <?php echo $this->translate('Unsure') ?>
					</a>
	    		<?php endif; ?> -->
	           &nbsp;&middot;&nbsp; 
	    		<!--WITHOUT ICON-->
	    		<?php if($this->showLikeWithoutIcon):?>
					<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>>
						<?php echo $this->translate('Dislike') ?>
					</a>
	    		<?php else:?>
					<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
						<i class="fa fa-thumbs-down ynfontawesome"></i> <?php //echo $this->translate('Dislike') ?>
					</a>
	    		<?php endif; ?>
	    		&nbsp;&middot;&nbsp; 
	    		<!--WITHOUT ICON-->
	    		<?php if($this->showLikeWithoutIcon):?>
					<a id="writecomments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>>
						<?php echo $this->translate('Comment') ?>
					</a>
	    		<?php else:?>
					<a id="writecomments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="<?php echo $this -> url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login');?>">
						<i class="fa fa-comments ynfontawesome"></i> <?php echo $this->translate('Comment') ?>
					</a>
	    		<?php endif; ?>
	            </div><!--floatleft-->
        <?php endif; ?>
        <div class="yncomment_float_right">
        <?php if($this->likes->getTotalItemCount() > 0): ?>
            <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
             <?php if (Engine_Api::_()->getDbTable('likes', 'core')->getLike($this->subject, $this->viewer()) && $this->likes->getTotalItemCount() == 1) :?>
                <?php echo $this->translate(array('%s like this.', '%s likes this.', $this->likes->getTotalItemCount()), $this->fluentLikeList(Engine_Api::_()->getDbtable('likes', 'yncomment')->likes($this->subject)->getAllLikesUsers())) ?>
            <?php else:?>
                <?php echo $this->translate(array('%s likes this.', '%s like this.', $this->likes->getTotalItemCount()), $this->fluentLikeList(Engine_Api::_()->getDbtable('likes', 'yncomment')->likes($this->subject)->getAllLikesUsers())) ?>
            <?php endif;?>
        <?php endif;?>
        <?php if($this->dislikes->getTotalItemCount() > 0): ?>
        <?php if($this->likes->getTotalItemCount() > 0) :?>
           &nbsp;&middot;&nbsp;
        <?php else:?>
        <?php endif;?>
          
        <?php if($this->showDislikeUsers) :?>
            <?php $this->dislikes->setItemCountPerPage($this->dislikes->getTotalItemCount()) ?> 
            <?php if (Engine_Api::_()->getDbTable('dislikes', 'yncomment')->getDislike($this->subject, $this->viewer()) && $this->dislikes->getTotalItemCount() == 1) :?>
              <?php echo $this->translate(array('%s dislike this.', '%s dislikes this.', $this->dislikes->getTotalItemCount()), $this->fluentDisLikeList(Engine_Api::_()->getDbTable('dislikes', 'yncomment')->getAllDislikesUsers($this->subject))) ?>
           <?php else:?>
              <?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', $this->dislikes->getTotalItemCount()), $this->fluentDisLikeList(Engine_Api::_()->getDbTable('dislikes', 'yncomment')->getAllDislikesUsers($this->subject))) ?>
           <?php endif;?>
        
         <?php else:?>  
            <b><?php echo $this->translate(array('%s person dislikes this.', '%s people dislike this.', $this->dislikes->getTotalItemCount()), $this->locale()->toNumber($this->dislikes->getTotalItemCount()));?></b>
        <?php endif;?>
       <?php endif;?>
       </div><!--floatright-->
    </div>
    <?php else: ?>
<?php endif; ?>


<?php if (isset($this->formComment)):
    if ($this->parent_comment_id): ?>
	   <form method="post" action=""  enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $this->nested_comment_id;?>' action-id="<?php echo $this->nested_comment_id;?>" style="display:none;" class="comments_form_yncomments_comments">
    		<textarea id="<?php echo $this->nested_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
    		<?php if( $this->viewer() && $this->subject()): ?>
    			<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
    		<?php endif; ?>
    		<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
    		<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">
            <div id="compose-containe-menu-items_<?php echo $this->nested_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
              <?php if($this->nestedCommentPressEnter):?>
                 <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post") ?></button>
              <?php else:?>
                 <button id="submit" type="submit"><?php echo $this->translate("Post") ?></button>
                 <?php if(!empty($this->showComposerOptions)):?>
                    <div id="composer_container_icons_<?php echo $this->nested_comment_id; ?>"></div>
                 <?php endif;?>
              <?php endif;?>   
            </div>
		</form>
        <?php else:?>		
        <form method="post" action=""  enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $this->nested_comment_id;?>' action-id="<?php echo $this->nested_comment_id;?>">
			<textarea id="<?php echo $this->nested_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
			<?php if( $this->viewer() && $this->subject()): ?>
				<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			<?php endif; ?>
			<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
			<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">
            <div id="compose-containe-menu-items_<?php echo $this->nested_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
                <?php if($this->nestedCommentPressEnter):?>
                  <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post") ?></button>
                 <?php else:?>
                    <button id="submit" type="submit" style="display: inline-block;"><?php echo $this->translate("Post") ?></button>
                    <?php if(!empty($this->showComposerOptions)):?>
                        <div id="composer_container_icons_<?php echo $this->nested_comment_id; ?>"></div>
                    <?php endif;?>
                 <?php endif;?>
            </div>
		</form>
      <?php endif; ?>
    <?php endif; ?>
    <ul>
      <?php if (empty($this->parent_comment_id)):?>
       <li id="yncomment_replies_li">
          <span><?php
            echo $this->translate(array('yn_total_comment', '%s Comments & Replies',
            $this->commentsCount), $this->locale()->toNumber($this->commentsCount))?>
          </span>
          <div class="yncomment_replies_filter fright" id="yncomment_replies_filter">
            <div class="mright5" id="filter_<?php echo $this->nested_comment_id; ?>" style="display:none;"></div>
            <div class="yncomment_replies_pulldown yncomment_pulldown">
              <div class="yncomment_dropdown_menu_wrapper" id="filter_dropdown_menu" style="display:none;">
              	<div class="yncomment_dropdown_menu">
                  <ul>
                    <li  class="<?php if ($this->filter == 'public'): ?> active <?php endif;?>" ><a href="javascript:void(0);" onclick="filterComments('public', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Public"); ?></a></li>
                    <li class="<?php if ($this->filter == 'professional'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="filterComments('professional', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Professional"); ?></a></li>
                  </ul>
                </div>
              </div>
              <?php if ($this->filter == 'public'): ?>
              	<a href="javascript:void(0);" onclick="showFilterComments();"><?php echo $this->translate("Filter by Public"); ?>&nbsp;
                <i class="fa fa-caret-down ynfontawesome"></i></a>
              <?php elseif($this->filter == 'professional'):?>
              	<a href="javascript:void(0);" onclick="showFilterComments();"><?php echo $this->translate("Filter by Professional"); ?>&nbsp;
                <i class="fa fa-caret-down ynfontawesome"></i></a>
              <?php endif;?>
            </div>
          </div> 
         <?php if($this->dislikes->getTotalItemCount() > 0
            ||  $this->likes->getTotalItemCount() > 0 || $this->comments->getTotalItemCount()  > 0):?>
          <?php if ($this->comments->getTotalItemCount() > 1): // REPLIES ------- ?>
              <div class="yncomment_replies_sorting fright" id="yncomment_replies_sorting">
                <div class="mright5" id="sort_<?php echo $this->nested_comment_id; ?>" style="display:none;"></div>
                <div class="yncomment_replies_pulldown yncomment_pulldown">
                  <div class="yncomment_dropdown_menu_wrapper" id="sorting_dropdown_menu" style="display:none;">
                  	<div class="yncomment_dropdown_menu">
                      <ul>
                        <li  class="<?php if ($this->order == 'DESC'): ?> active <?php endif;?>" ><a href="javascript:void(0);" onclick="sortComments('DESC', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Newest"); ?></a></li>
                        <li class="<?php if ($this->order == 'ASC'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('ASC', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Oldest"); ?></a></li>
                        <?php if($this->showLikeWithoutIconInReplies == 3):?>
                        <li class="<?php if ($this->order == 'like_count'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('like_count', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Most Voted"); ?></a></li>
                        <?php else:?>
                        <li class="<?php if ($this->order == 'like_count'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('like_count', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Most Liked"); ?></a></li>
                        <?php endif;?>
                      </ul>
                    </div>
                  </div>
                  <?php if ($this->order == 'DESC'): ?>
                  	<a href="javascript:void(0);" onclick="showSortComments();">
                        <?php echo $this->translate("Sort by Newest"); ?>&nbsp;
                        <i class="fa fa-caret-down ynfontawesome"></i>
                    </a>

                  <?php elseif($this->order == 'ASC'):?>

                  	<a href="javascript:void(0);" onclick="showSortComments();">
                        <?php echo $this->translate("Sort by Oldest"); ?>&nbsp;
                        <i class="fa fa-caret-down ynfontawesome"></i>
                    </a>
                  <?php elseif($this->order == 'like_count'):?>

                    <?php if($this->showLikeWithoutIconInReplies == 3):?>
                  	<a href="javascript:void(0);" onclick="showSortComments();">
                        <?php echo $this->translate("Sort by Most Voted"); ?>&nbsp;
                        <i class="fa fa-caret-down ynfontawesome"></i>
                    </a>
                    <?php else:?>
                    <a href="javascript:void(0);" onclick="showSortComments();">
                        <?php echo $this->translate("Sort by Most Liked"); ?>&nbsp;
                        <i class="fa fa-caret-down ynfontawesome"></i>
                    </a>
                    <?php endif;?>
                  <?php endif;?>
                </div>
              </div>
          <?php endif; ?>
        <?php endif; ?>
        </li> 
      <?php endif; ?>
      <?php if ($this->comments->getTotalItemCount() > 0): // REPLIES ------- ?>
        <?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
          <li class="yncomment_prev_count">
            <div> </div>
            <div class="yncomment_replies_viewall">
              <?php
              $viewPrevTitle = $this->translate('View previous comments');
              if($this->parent_comment_id)
              {
                  $viewPrevTitle = $this->translate('View previous replies');
              }
              echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-long-arrow-left"></i> '.$viewPrevTitle, array(
                  'onclick' => 'en4.yncomment.yncomments.loadComments("' . $this->subject->getType() . '", "' . $this->subject->getIdentity() . '", "' . ($this->page - 1) . '", "' . $this->order . '", "' . $this->parent_comment_id . '", "' . $this->taggingContent . '", "' . $this->showComposerOptions . '", 2, "' . $this->showAsNested . '", "' . $this->showAsLike . '", "' . $this->showDislikeUsers . '", "' . $this->showLikeWithoutIcon . '", "' . $this->showLikeWithoutIconInReplies . '")', 'class' => 'mright5 buttonlink'
              ))
              ?>
              <div id="view_previous_comments_<?php echo $this->parent_comment_id; ?>" style="display:none;"></div>
            </div>
          </li>
        <?php endif; ?>
        <?php
        // Iterate over the replies backwards (or forwards!)
        $replies = $this->comments->getIterator();
        $i = 0;
        $l = count($replies) - 1;
        $d = 1;
        $e = $l + 1;
        for (; $i != $e; $i += $d):
          $comment = $replies[$i];
          $total_child = count(Engine_Api::_() -> getDbtable('comments', 'yncomment')-> getAllReplies($this->subject, $comment->getIdentity()));
          $poster = $this->item($comment->poster_type, $comment->poster_id);
          $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
          $canEdit = $poster->isSelf($this->viewer());
          $isOpenHide = ($this -> openHide == $comment->getIdentity());
          $isHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment) && !$isOpenHide);
          $openHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment) && $isOpenHide);
          ?>
          <li id="comment-<?php echo $comment->comment_id ?>" class="yncomment_replies_list <?php if($openHide) echo 'yncomment_hidden_open'?>">
            <!-- Check hide-->
            <?php if($isHide):?>
                <!-- Comment has been hidden -->
                <a class="yncomment_hidden"  href="javascript:;" onclick="en4.yncomment.yncomments.openHideComment('<?php echo
                        $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $comment->getIdentity()?>', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>')">
                    <span title="<?php echo $this -> translate('1 hidden')?>">
                        <i class="fa fa-circle"></i><i class="fa fa-circle"></i><i class="fa fa-circle"></i>
                    </span>
                </a>
            <?php else: ?>
            <div class="yncomment_replies_content <?php if($total_child) echo "yncomment_content_hasChild";?>">
              <div class="yncomment_replies_author_photo">
                <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle()))?>
              </div>
              <div class="yncomment_replies_info">
                <span class="yncomment_replies_info_op">
                <?php if ($this->showAsNested) : ?>
                    <span class="yncomment_replies_showhide">
                      <span class="minus" onclick="showReplyData(1, '<?php echo $comment->comment_id ?>', '<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $openHide ?>');" id="hide_<?php echo $comment->comment_id ?>" title="<?php echo $this->translate("Collapse"); ?>">
                        <i class="fa fa-minus"></i>
                      </span> 
                      <span class="plus" onclick="showReplyData(0, '<?php echo $comment->comment_id ?>', '<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $openHide ?>');" id="show_<?php echo $comment->comment_id ?>" style="<?php if($openHide): ?>display:block; <?php else:?>display:none; <?php endif;?>" title="<?php echo $this->translate("Expand"); ?>">
                        <i class="fa fa-plus"></i>
                      </span> 
                    </span>	
                <?php endif; ?>
                <?php if ($this->viewer_id): ?>
                    <span class="yncomment_replies_pulldown">
                      <div class="yncomment_dropdown_menu_wrapper">
                        <div class="yncomment_dropdown_menu">
                    <?php 
                       $attachMentArray  = array();
                       if (!empty($comment->attachment_type) 
                       && Engine_Api::_() -> hasItemType($comment->attachment_type)
                       && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                        <?php if($comment->attachment_type == 'album_photo' || $comment->attachment_type == 'advalbum_photo'):?>
                          <?php $status = true; ?>
                          <?php $photo_id = $attachment->photo_id; ?>
                          <?php $album_id = $attachment->album_id; ?>
                          <?php $src = $attachment->getPhotoUrl(); ?>
                          <?php $attachMentArray = array('status' => $status, 'photo_id' => $photo_id , 'album_id' => $attachment->album_id, 'src' => $src);?>
                       <?php elseif($comment->attachment_type == 'core_link') :?>
                            <?php $status = true; ?>
                            <?php $uri = $attachment->uri;?>
                            <?php $attachMentArray = array('status' => $status, 'url' => $uri);?>
                       <?php endif;?>
                    <?php endif;?>
                    <?php  $commentBody = $comment -> body;
                        if (isset($comment->params)) {
                            $commentBody = $this -> stringTagToElement($commentBody, (array) Zend_Json::decode($comment->params));
                        }?>
                    <script type="text/javascript">  
                        en4.core.runonce.add(function() {
                          en4.yncomment.editCommentInfo['<?php echo $comment->getIdentity() ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($commentBody );?>', 'attachment_type':'<?php echo $comment->attachment_type ?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
                        });
                    </script>
                          <ul>     
                            <?php if ($canEdit): ?>
                            <li>
                                <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="showEditForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>','<?php echo $comment->parent_comment_id ?>');"><?php echo $this->translate('Edit') ?></a>
                            </li>
                            <?php endif;?>
                            <?php if ($canDelete): ?>                 
                                <li>
                                    <a href="javascript:void(0);" title="<?php echo $this->translate("Delete"); ?>" onclick="yncomemntDeleteComment('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '<?php echo $comment->comment_id ?>','<?php echo $comment->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>')"><?php echo $this->translate('Delete'); ?></a>
                                </li>
                           <?php endif; ?>
                           <?php if(!$canEdit && !$canDelete):?>
                                 <!-- Hide -->
                                 <?php if(Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment)):?>
                                    <li>
                                        <a href="javascript:void(0);" title="<?php echo $this->translate("Unhide"); ?>" onclick="en4.yncomment.yncomments.unHideComment('<?php echo
                                            $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $comment->getIdentity()?>', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>')"><?php echo $this->translate('Unhide'); ?></a>
                                    </li>
                                 <?php else:?>
                                     <li>
                                        <a href="javascript:void(0);" title="<?php echo $this->translate("Hide"); ?>" onclick="hideComment('<?php echo $comment->comment_id ?>','<?php echo $comment->getType() ?>')"><?php echo $this->translate('Hide'); ?></a>
                                    </li>
                                 <?php endif;?>
                                 <!-- Report -->
                                <li>
                                    <?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $comment->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report"), 'class' => "smoothbox")) ?>
                                </li>
                            <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                      <span class="yncomment_comment_dropbox"><i class="fa fa-caret-down"></i></span>
                    </span>
                <?php endif; ?>
                </span>
                <div class='yncomment_replies_author yncomment_txt_light'>
                <?php if($comment->parent_comment_id):?>
                    <?php $item = Engine_Api::_()->getItem($comment->getType(), $comment->parent_comment_id);?>
                    <?php $posterParent = Engine_Api::_()->getItem($item->poster_type, $item->poster_id);?>
                    <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
                    <div class="yncomment_comment_tips_wrap">
                      <span class="yncomment_comment_tips">
                        <a href="<?php echo $posterParent->getHref()?>">&nbsp;<i class="fa fa-long-arrow-right ynfontawesome"></i>&nbsp;<?php echo $posterParent->getTitle() ?></a>
                      </span>
                      <div class='yncomment_replies_author_photo_tooltip info_tip_content_wrapper'>
                        <div class="tip_main_photo fleft mright5">
                          <?php echo $this->itemPhoto($posterParent, 'thumb.icon', $posterParent->getTitle())?> 
                        </div>
                    <div class="tip_main_body">
                        <b><?php echo $posterParent->getTitle(); ?></b>
                        <div>
                            <?php $content = $item->body ?>
                            <?php
                            if (isset($comment->params)) 
                            {
                              $actionParams = (array) Zend_Json::decode($item->params);
                              if (isset($actionParams['tags'])) 
                              {
                                  // Convert tags
                                  $content = $this -> stringTagToObject($content, $actionParams);
                                  // Convert emoticons
                                  echo $this->smileyToEmoticons($content);
                              } else {
                                   // Convert emoticons
                                  echo $this->smileyToEmoticons($this->viewMore($item->body, null, 3 * 1027));
                              }
                            }
                            ?>
                            <?php if (!empty($item->attachment_type) 
                            && Engine_Api::_() -> hasItemType($item->attachment_type)
                            && null !== ($attachment = $this->item($item->attachment_type, $item->attachment_id))): ?>
                              <div class="yncomment_comments_attachment" id="yncomment_comments_attachment">
                                <div class="yncomment_comments_attachment_photo">
                                  <?php if (null !== $attachment->getPhotoUrl()): ?>
                                        <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if(!in_array($item->attachment_type, array('advalbum_photo', 'album_photo'))):?>
                                <div class="yncomment_comments_attachment_info">
                                  <div class="yncomment_comments_attachment_title">
                                        <?php echo $this->htmlLink($attachment->getHref(array('comment' => $item->comment_id)), $attachment->getTitle()) ?>
                                  </div>
                                  <div class="yncomment_comments_attachment_des">
                                        <?php echo $attachment->getDescription() ?>
                                  </div>
                                </div>
                                <?php endif;?>
                              </div>
                            <?php endif; ?>	
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php else:?>
                      <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle());?>
                    <?php endif;?>&nbsp;&#183;&nbsp;&nbsp;<?php echo $this->translate("%s", $this->timestamp($comment->creation_date));?>
                </div>
                <div id="yncomment_data-<?php echo $comment->comment_id ?>">
                  <div class="yncomment_replies_comment <?php $ownerLevel = $comment -> getOwner() -> level_id; if($ownerLevel == 6 || $ownerLevel == 7) echo 'yncomment_replies_comment_professional';?>" id="yncomment_comment_data-<?php echo $comment->comment_id ?>">
                    <?php $content = $comment->body ?>
                    <?php
                    if (isset($comment->params)) {
                      $actionParams = (array) Zend_Json::decode($comment->params);
                      if (isset($actionParams['tags'])) {
                         // Convert tags
                        $content = $this -> stringTagToObject($content, $actionParams);
                        // Convert emoticons
                        echo $this->smileyToEmoticons($content);
                      } else {
                           // Convert emoticons
                           echo $this->smileyToEmoticons($this->viewMore($comment->body, null, 3 * 1027));
                      }
                    }
                    ?>
                    <?php if (!empty($comment->attachment_type) 
                    && Engine_Api::_() -> hasItemType($comment->attachment_type)
                    && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                      <div class="yncomment_comments_attachment" id="yncomment_comments_attachment">
                        <div class="yncomment_comments_attachment_photo">
                          <?php if (null !== $attachment->getPhotoUrl()): ?>
                             <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                          <?php endif; ?>
                        </div>
                        <?php if(!in_array($comment->attachment_type, array('advalbum_photo', 'album_photo'))):?>
                        <div class="yncomment_comments_attachment_info">
                          <div class="yncomment_comments_attachment_title">
                            <?php echo $this->htmlLink($attachment->getHref(array('comment' => $comment->comment_id)), $attachment->getTitle()) ?>
                          </div>
                          <div class="yncomment_comments_attachment_des">
                             <?php echo $attachment->getDescription() ?>
                          </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <?php endif; ?>	
                  </div>
                  <div id="yncomment_edit_comment_<?php echo $comment->comment_id;?>" style="display: none;" class="yncomment_replies yncomment_replies_child comment_edit">
                    <?php include APPLICATION_PATH . '/application/modules/Yncomment/views/scripts/comment/edit.tpl'; ?>
                  </div>
                  <?php if($this->viewer()->getIdentity()):?>
                        <div id="close_edit_box-<?php echo $comment->comment_id;?>" class="yncomment_txt_light f_small comment_close" style="display:none;">
                         <a href='javascript:void(0);' onclick="closeEdit('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>','<?php echo $comment->parent_comment_id ?>');"><i class="fa fa-times ynfontawesome"></i><?php echo $this->translate(' Cancel edit');?></a>
                        </div>
                  <?php endif;?>
                  <div class="yncomment_replies_date yncomment_txt_light">
                        <?php if ($this->canComment): ?>
                          <?php if ($this->showAsNested): ?>
                            <?php if (isset($this->formComment)):?>
                                <?php if($replyLink):?>
                                        <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');">
                                            <?php if($this->showLikeWithoutIconInReplies != 1):?><i class="fa fa-reply-all ynfontawesome"></i>&nbsp;<?php endif;?>
                                            <?php if($this->showLikeWithoutIconInReplies != 2):?><?php echo $this->translate('Reply');?><?php endif;?></a><?php if($this->showLikeWithoutIconInReplies != 1):?>&nbsp;&nbsp;<?php else:?>&nbsp;&#183;&nbsp;<?php endif;?>
                                <?php else:?>
                                    <?php if($comment->parent_comment_id == 0) :?>
                                        <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');">
                                        <?php if($this->showLikeWithoutIconInReplies != 1):?><i class="fa fa-reply-all ynfontawesome"></i>&nbsp;<?php endif;?>
                                        <?php if($this->showLikeWithoutIconInReplies != 2):?><?php echo $this->translate('Reply');?><?php endif;?></a><?php if($this->showLikeWithoutIconInReplies != 1):?>&nbsp;&nbsp;<?php else:?>&nbsp;&#183;&nbsp;<?php endif;?>
                                    <?php endif; ?>
                                <?php endif;?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        
					  <?php if($this->showLikeWithoutIconInReplies == 1 && $this->viewer()->getIdentity()):?>
							<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
                                <a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?> onclick="en4.yncomment.yncomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?> >
                                    <b  class="yn_like_dislike"><?php echo $this->translate('like') ?></b>
                                </a>
							<?php else: ?>
								<?php echo $this->translate('like') ?>
							<?php endif;?>
					   <?php elseif($this->showLikeWithoutIconInReplies == 0 && $this->viewer()->getIdentity()):?>
					       <?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
							<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
								 <b class="yn_like_dislike"><i class="fa fa-thumbs-up ynfontawesome"></i> <?php echo $this->translate('like') ?></b></a>
							<?php else: ?>
								<i class="fa fa-thumbs-up ynfontawesome"></i> <?php echo $this->translate('like') ?>
							<?php endif;?>
					   <?php elseif($this->showLikeWithoutIconInReplies == 2 && $this->viewer()->getIdentity()):?>
					       <?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
							<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
								<b class="yn_like_dislike"><i class="fa fa-thumbs-up ynfontawesome"></i></b></a>
							<?php else: ?>
								<i class="fa fa-thumbs-up ynfontawesome"></i>
							<?php endif;?>
                        <?php elseif($this->showLikeWithoutIconInReplies == 3 && $this->viewer()->getIdentity()):?>
					       <?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
							<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
								<b class="yn_like_dislike"><i class="fa fa-arrow-up ynfontawesome"></i>&nbsp; <?php echo $this->translate ("Vote up") ?> </b>
                            </a>
							<?php else: ?>
								<i class="fa fa-arrow-up ynfontawesome"></i>&nbsp; <?php echo $this->translate ("Vote up") ?>
							<?php endif;?>    
					    <?php endif;?>
					    
					    <?php if ($comment->likes()->getLikeCount() > 0 && $this->showLikeWithoutIconInReplies == 3): ?>
                        <?php if($this->viewer()->getIdentity()):?>
                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                            <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount()?></a>
                        <?php else:?>
                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                            <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s vote', '%s votes', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()))?></a>
                        <?php endif;?>
                      <?php endif ?>
					    
					<?php if($this->showLikeWithoutIconInReplies == 1 && $this->viewer()->getIdentity()):?>
						<?php if(!Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($comment, $this->viewer())):?>
								&nbsp;&middot;&nbsp;
								<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
                                <b class="yn_like_dislike"><?php echo $this->translate('dislike') ?></b></a>
						<?php else: ?>
								&nbsp;&middot;&nbsp;<?php echo $this->translate('dislike') ?>
						<?php endif;?>
						<?php elseif($this->showLikeWithoutIconInReplies ==0 && $this->viewer()->getIdentity()):?>
						<?php if(!Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($comment, $this->viewer())):?>
								&nbsp;&nbsp;<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>  
                        <b class="yn_like_dislike"> <i class="fa fa-thumbs-down ynfontawesome"></i> <?php echo $this->translate('dislike') ?></b></a>
						<?php else: ?>
								&nbsp;&nbsp;<i class="fa fa-thumbs-down ynfontawesome"></i> <?php echo $this->translate('dislike') ?>
						<?php endif;?>
						<?php elseif($this->showLikeWithoutIconInReplies == 2 && $this->viewer()->getIdentity()):?>
						<?php if(!Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($comment, $this->viewer())):?>
								&nbsp;&nbsp;<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
                        <b class="yn_like_dislike"><i class="fa fa-thumbs-down ynfontawesome"></i></b></a>
						<?php else: ?>
								&nbsp;&nbsp;<i class="fa fa-thumbs-down ynfontawesome"></i>
						<?php endif;?>
                        <?php elseif($this->showLikeWithoutIconInReplies == 3 && $this->viewer()->getIdentity()):?>
                            &nbsp;&nbsp;
					   <?php if(!Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($comment, $this->viewer())):?>
						 	<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.yncomment.yncomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showComposerOptions ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
                        <b class="yn_like_dislike"><i class="fa fa-arrow-down ynfontawesome"></i>&nbsp; <?php echo $this->translate ("Vote down") ?></b>
                        </a>
						<?php else: ?>
							 	<i class="fa fa-arrow-down ynfontawesome"></i>&nbsp; <?php echo $this->translate ("Vote down") ?>
						<?php endif;?>
				  <?php endif; ?>
				  
				    <?php if (Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) > 0 && $this->showLikeWithoutIconInReplies == 3): ?>
                        <?php if($this->viewer()->getIdentity()):?> 
                            <?php if($this->showDislikeUsers) :?>
                                 <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                                 <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )?></a>
                            <?php else:?>
                              <b class="count-disable-vote-down"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment );?></b>
                            <?php endif;?>
                        <?php else:?>
                            <?php if($this->showDislikeUsers) :?>
                                <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                                <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s vote down', '%s vote downs', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )))?></a>
                            <?php else:?>
                                <b><?php echo $this->translate(array('%s vote down', '%s vote downs', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )));?></b>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endif ?>
				  
                  <?php if ($comment->likes()->getLikeCount() > 0 && $this->showLikeWithoutIconInReplies != 3): ?>
                       <?php if($this->canComment):?>
							<span>&nbsp;|&nbsp;</span>
                        <?php endif;?>
                        <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                        <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s likes this.', '%s like this.', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()));?></a>
                  <?php endif ?>
                  <?php if (Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) > 0 && $this->showLikeWithoutIconInReplies != 3): ?>
                        <?php if($this->canComment || $comment->likes()->getLikeCount() > 0):?>
                        <span>&nbsp;|&nbsp;</span>
                        <?php endif;?>
						<?php if($this->showDislikeUsers) :?>
                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                            <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )))?></a>
						<?php else:?>
							<b><?php echo $this->translate(array('%s dislikes this.', '%s dislikes this.', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )));?></b>
						<?php endif;?>
                  <?php endif ?>
                  </div>  
                </div>
              </div>
            </div>
            <?php if ($this->showAsNested): ?>
              <?php if ($this->format): ?>
                <?php echo $this->action("list", "comment", "yncomment", array("type" => $this->subject->getType(), "id" =>
                    $this->subject->getIdentity(), 'format' => 'html', 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1, 'taggingContent' => $this->taggingContent, 'showComposerOptions' => $this->showComposerOptions, 'showAsNested' => $this->showAsNested, 'showAsLike' => $this->showAsLike, 'showDislikeUsers' => $this->showDislikeUsers, 'showLikeWithoutIcon' => $this->showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies, 'showSmilies' => $this->showSmilies, 'showComposerOptions' => $this->showComposerOptions, 'photoLightboxComment' => $this->photoLightboxComment, 'commentsorder' => $this->commentsorder)); ?>
              <?php else: ?>
                <?php echo $this->action("list", "comment", "yncomment", array("type" => $this->subject->getType(), "id" =>
                    $this->subject->getIdentity(), 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1, 'taggingContent' => $this->taggingContent, 'showComposerOptions' => $this->showComposerOptions, 'showAsNested' => $this->showAsNested, 'showAsLike' => $this->showAsLike, 'showDislikeUsers' => $this->showDislikeUsers, 'showLikeWithoutIcon' => $this->showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies, 'showSmilies' => $this->showSmilies, 'showComposerOptions' => $this->showComposerOptions, 'photoLightboxComment' => $this->photoLightboxComment, 'commentsorder' => $this->commentsorder)); ?>
              <?php endif; ?>  
           <?php endif; ?>  
          <?php endif; ?>  
          </li>
        <?php endfor; ?>
        <?php if ($this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
            <div> </div>
            <div class="yncomment_replies_viewall">
              <?php
              $viewLaterTitle = $this->translate('View later comments');
              if($this->parent_comment_id)
              {
                  $viewLaterTitle = $this->translate('View later replies');
              }
              echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-long-arrow-right"></i> '.$viewLaterTitle, array(
                  'onclick' => 'en4.yncomment.yncomments.loadComments("' 
                  . $this->subject->getType() . '", "' 
                  . $this->subject->getIdentity() . '", "' 
                  . ($this->page + 1) . '", "' 
                  . $this->order . '", "' 
                  . $this->parent_comment_id . '", "' 
                  . $this->taggingContent .'", "'
                  . $this->showComposerOptions
                  . '", 1, "' 
                  . $this->showAsNested . '", "' 
                  . $this->showAsLike . '", "' 
                  . $this->showDislikeUsers . '", "' 
                  . $this->showLikeWithoutIcon . '", "' 
                  . $this->showLikeWithoutIconInReplies 
                  . '")', 
                  'class' => 'mright5 buttonlink'
              ))
              ?>
              <div id="view_later_comments_<?php echo $this->parent_comment_id; ?>" style="display:none;"></div>
            </div>
      </li>
      <?php endif; ?>
      </ul>
<?php endif; ?>
<?php if (!$this->page): ?>
    </div>
<?php endif; ?>
<?php if (empty($this->parent_div)): ?>
  </div>
<?php endif; ?>
<script type="text/javascript">
  var unhideReqActive = false;
  var hideReqActive = false;
  en4.core.runonce.add(function() 
  {
        // Scroll to reply
        if( window.location.hash != '' ) {
          var hel = $(window.location.hash);
          if( hel ) {
            window.scrollTo(hel);
          }
        }
  });
  en4.core.runonce.add(function()
  {
        $($('comments-form_<?php echo $this->nested_comment_id ?>').body).autogrow();
  });
  var nestedCommentPressEnter = '<?php echo $this->nestedCommentPressEnter;?>';
  en4.core.runonce.add(function()
  {
    if(($('comments-form_<?php echo $this->subject->getGuid() ?>_0').getElementById('compose-container')) == null) 
    {
        makeComposer($($('comments-form_<?php echo $this->subject->getGuid() ?>_0').body).id, '<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', 0);
        tagContentComment();
    }
  });
  function showSortComments() 
  {
    $('sorting_dropdown_menu').toggle();
  }
  function showFilterComments() 
  {
		 $('filter_dropdown_menu').toggle();
  }
  function yncomemntDeleteComment(type, id, comment_id, order, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) 
  {     
        $$('.yncomment_replies_pulldown_open').each(function(item, index)
        {
            item.removeClass('yncomment_replies_pulldown_open');
        }); 
        var msg = "<div class='yncomment_show_popup'><h3>" + "<?php echo $this->string()->escapeJavascript($this->translate('Delete Comment?')) ?>" + "</h3><p>" + "<?php echo $this->string()->escapeJavascript($this->translate('Are you sure that you want to delete this comment? This action cannot be undone.')) ?>" + "</p>" + "<button type='submit' onclick=\"en4.yncomment.yncomments.deleteComment(\'" + type + "\',\'" +  id + "\',\'" +  comment_id + "\',\'" +  order + "\',\'" +  parent_comment_id + "\',\'" +  taggingContent + "\',\'" +  showComposerOptions + "\',\'" +  showAsNested + "\',\'" +  showAsLike + "\',\'" +  showDislikeUsers + "\',\'" +  showLikeWithoutIcon + "\',\'" +  showLikeWithoutIconInReplies + "\'); return false;\">" + "<?php echo $this->string()->escapeJavascript($this->translate('Delete')) ?>" + "</button>" + " <?php echo $this->string()->escapeJavascript($this->translate('or')) ?> " + "<a href='javascript:void(0);'onclick='YncommentSmoothboxClose();'>" + "<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>" + "</a></div>"
        Smoothbox.open(msg);
  }
  function YncommentSmoothboxClose() 
  {
        if (typeof parent.Smoothbox == 'undefined') {
            Smoothbox.close();
        } else {
            parent.Smoothbox.close();
        }
   }
</script>
<?php
$arr_showComposerOptions = explode(',', $this -> showComposerOptions);
if($this->showSmilies && !$this->nestedCommentPressEnter): ?>
    <style type="text/css">
        #yncomment-comment-compose-link-activator, #yncomment-compose-link-menu span {
            margin-right: 28px;
        }
    </style>
<?php endif; ?>
<?php if($this->showSmilies && !in_array('addLink', $arr_showComposerOptions) && !$this->nestedCommentPressEnter): ?>
    <style type="text/css">
        #yncomment-comment-compose-photo-activator, #yncomment-compose-link-menu span {
            margin-right: 28px;
        }
    </style>
<?php endif; ?>
<?php if(!$this->nestedCommentPressEnter): ?>
<style type="text/css">
	.yncomment_replies .compose-menu {
		width: 100%;
		display: inline-table;
	}
</style>
<?php endif; ?>
