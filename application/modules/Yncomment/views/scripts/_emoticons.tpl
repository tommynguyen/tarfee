<?php $emoticons = Engine_Api::_() -> yncomment() -> getEmoticons();?>
<div id="emoticons-comment-icons" style="display:none;">
    <span id="emoticons-comment-button" class="adv_post_smile" onclick="setCommentEmoticonsBoard(this);" style="display:block;" title="<?php echo $this->translate('Insert Emoticons') ?>">
        <i class="fa fa-smile-o"></i>
        <span id="emoticons-comment-board"  class="yncomment_comment_embox yncomment_comment_embox_closed" >
            <span class="yncomment_comment_embox_title">
                <span class="fleft" id="emotion_comment_label"></span>
                <span class="fright"id="emotion_comment_symbol" ></span>
            </span>
            <?php foreach ($emoticons as $emoticon):
                $title = $this->translate(ucwords($emoticon -> title));?>         
                <span class="yncomment_comment_embox_icon" onmouseover='setCommentEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($title)?>","<?php echo $this->string()->escapeJavascript($emoticon-> text)?>", this)' onclick='addCommentEmotionIcon("<?php echo $this->string()->escapeJavascript($emoticon->text)?>", this)'  title="<?php echo $title."&nbsp;".$emoticon->text; ?>">
                    <?php echo "<img alt = '{$title}' src='{$this->layout()->staticBaseUrl}application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>"; ?>
                </span>
            <?php endforeach;?>
        </span>                 
    </span>
</div>