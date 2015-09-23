<?php $emoticons = Engine_Api::_() -> yncomment() -> getEmoticons();?>
<div id="emoticons-yncomment-comment-icons" style="display:none;">
    <span id="emoticons-yncomment-comment-button" class="adv_post_smile" onclick="setYnCommentEmoticonsBoard(this);" style="display:block;" title="<?php echo $this->translate('Insert Emoticons') ?>">
        <i class="fa fa-smile-o"></i>
        <span id="emoticons-yncomment-comment-board"  class="yncomment_comment_embox yncomment_comment_embox_closed" >
            <span class="yncomment_comment_embox_title">
                <span class="fleft" id="emotion_yncomment_comment_label"></span>
                <span class="fright"id="emotion_yncomment_comment_symbol" ></span>
            </span>
            <?php foreach ($emoticons as $emoticon):
                $title = $this->translate(ucwords($emoticon -> title));
                ?>         
                <span class="yncomment_comment_embox_icon" onmouseover='setYnCommentEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($title)?>","<?php echo $this->string()->escapeJavascript($emoticon -> text)?>", this)' onclick='addYnCommentEmotionIcon("<?php echo $this->string()->escapeJavascript($emoticon -> text)?>", this)'  title="<?php echo $title."&nbsp;".$emoticon -> text; ?>">
                    <?php echo "<img alt = '{$title}' src= '{$this->layout()->staticBaseUrl}application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>"; ?>
                </span>
            <?php endforeach;?>
            <i class="fa fa-smile-o"></i>
        </span>                 
    </span>
</div>