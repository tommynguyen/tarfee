<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<div class="video_thumb_wrapper">  
    <?php $poster = $this->video->getOwner();?>
    <div class="ynvideo_thumb_img_wrap" style="width:<?php echo $this->videoWidth?>px">
        <?php 
            echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.large', '', 
                array('width' => $this->videoWidth . 'px', 'height' => $this->videoHeight . 'px'))) 
        ?>    
        <span class="video_button_add_to_area">
            <button class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $this->video->getIdentity()?>" video-id="<?php echo $this->video->getIdentity()?>">
                <div class="ynvideo_plus" />
            </button>
        </span>
    </div>
    <div class="ynvideo_featured_frame" style="width:<?php echo $this->videoWidth?>px">
        <div class="ynvideo_feature_user_thumb">
            <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon')) ?>    
        </div>
        <div class="ynvideo_featured_info">
            <div class="ynvideo_featured_title">
                <?php echo $this->htmlLink($this->video->getHref(), $this->string()->truncate($this->video->title, 15), array('title' => $this->string()->stripTags($this->video->title))) ?>
            </div>
            <div class="ynvideo_feature_info">
                <?php echo $this->translate('Posted by ')?>
                <?php echo $this->htmlLink($poster->getHref(), htmlspecialchars ($this->string()->truncate($poster->getTitle(), 15)), array('title' => $poster->getTitle()))?>
                <?php echo $this->translate(' on ')?>
                <?php echo $this->locale()->toDateTime(strtotime($this->video->creation_date), array('type' => 'date')) ?>
            </div>
            <div class="ynvideo_feature_description" title="<?php echo $this->string()->stripTags($this->video->description)?>">
                <?php echo $this->string()->truncate($this->string()->stripTags($this->video->description), 20)?>
            </div>
            <div class="ynvideo_feature_info">
                <?php 
                    echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video));
                ?>
                <div class="ynvideo_views">
                    <?php 
                        echo $this->translate(array('(%1$s view)', '(%1$s views)', $this->video->view_count), 
                            $this->locale()->toNumber($this->video->view_count));
                    ?>
                </div>
                <div class="ynvideo_clear"></div>
            </div>
        </div>
        <div class="ynvideo_clear"></div>
    </div>
</div>    
