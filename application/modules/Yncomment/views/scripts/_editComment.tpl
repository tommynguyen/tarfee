<form method="post" action="" class="activity-comment-form" enctype="application/x-www-form-urlencoded" id="activity-comment-edit-form-<?php echo $comment->comment_id;?>">
    <textarea rows = "1" id="activity-comment-edit-body-<?php echo $comment->comment_id;?>" name="body"></textarea>
    <button type="submit" id="activity-comment-edit-submit-<?php echo $comment->comment_id;?>" class="mtop5" name="submit" <?php if($this->commentShowBottomPost):?> style="display: none;" <?php endif;?> ><?php echo $this->translate("Edit");?></button>
    <input type="hidden" id="activity-comment-edit-id-<?php echo $comment->comment_id;?>" value="<?php echo $comment->comment_id;?>" name="comment_id">
    <input type="hidden" value='<?php echo $action->action_id ?>' name='action_id'>
</form>
<script type="text/javascript">
    // cancel edit
    function closeEdit(comment_id) 
    {
        if($('activity-comment-edit-form-' + comment_id))
            $('activity-comment-edit-form-' + comment_id).style.display = 'none';
        if($('comments_body_' + comment_id))
            $('comments_body_' + comment_id).style.display = 'initial';
        if($('yncomment_comments_attachment_' + comment_id))
            $('yncomment_comments_attachment_' + comment_id).style.display = 'block';
        
        $('comment_edit_' + comment_id).style.display = 'none';
        $('close_edit_box-' + comment_id).style.display = 'none';
    }
</script>