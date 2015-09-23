<?php $edit_comment_id = $this->nested_comment_id . '_' . $comment->comment_id;?>
<form method="post" action="" action-id="<?php echo $edit_comment_id;?>" enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $edit_comment_id;?>'>
	<textarea id="<?php echo $edit_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
	<?php if( $this->viewer() && $this->subject()): ?>
		<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
	<?php endif; ?>
	<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
	<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">
    <input type="hidden" name="comment_id" value="<?php echo $comment->comment_id;?>" id="comment_id">
    <div id="compose-containe-menu-items_<?php echo $edit_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
        <?php if($this->nestedCommentPressEnter):?>
          <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post") ?></button>
         <?php else:?>
            <button id="submit" type="submit" style="display: inline-block;"><?php echo $this->translate("Post") ?></button>
            <div id="composer_container_icons_<?php echo $edit_comment_id; ?>"></div>
         <?php endif;?>
    </div>
</form>
<script type="text/javascript">
    // cancel edit
    function closeEdit(type, id, comment_id, parent_comment_id) {
       $('close_edit_box-'+ comment_id).style.display = 'none';
       if($('yncomment_edit_comment_' + comment_id))
        $('yncomment_edit_comment_' + comment_id).style.display = 'none';
       if($('yncomment_comment_data-' + comment_id))
        $('yncomment_comment_data-' + comment_id).style.display = 'block';
    
       $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
       if($('comments_'+ type +'_'+ id +'_' + comment_id))
            $('comments_'+ type +'_'+ id +'_' + comment_id).style.display = 'block';
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
<?php if(!$this->showSmilies):?>
    <style type="text/css">
    .yncomment_replies_info .compose-menu.inside-compose-icons{
        right:0px;
    }
    </style>
<?php endif; ?>