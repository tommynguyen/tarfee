<form method="post" action="" class="activity-comment-form" enctype="application/x-www-form-urlencoded" id="activity-reply-edit-form-<?php echo $reply->comment_id;?>">
    <textarea rows = "1" id="activity-reply-edit-body-<?php echo $reply->comment_id;?>" name="body"></textarea>
    <button type="submit" id="activity-reply-edit-submit-<?php echo $reply->comment_id;?>" name="submit" <?php if($this->commentShowBottomPost):?> style="display: none;" <?php endif;?>><?php echo $this->translate("Edit");?></button>
    <input type="hidden" id="activity-reply-edit-id-<?php echo $reply->comment_id;?>" value="<?php echo $reply->comment_id;?>" name="comment_id">
    <input type="hidden" value='<?php echo $action->action_id ?>' name='action_id'>
</form>
<script type="text/javascript">
    // cancel edit
    function closeReplyEdit(reply_id) 
    {
        if($('activity-reply-edit-form-' + reply_id))
            $('activity-reply-edit-form-' + reply_id).style.display = 'none';
        if($('reply_body_' + reply_id))
            $('reply_body_' + reply_id).style.display = 'initial';
        if($('yncomment_comments_attachment_' + reply_id))
            $('yncomment_comments_attachment_' + reply_id).style.display = 'block';
        
        $('reply_edit_' + reply_id).style.display = 'none';
        $('close_edit_box-' + reply_id).style.display = 'none';
    }
</script>