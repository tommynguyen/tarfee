<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
    <?php if ($this->video->duration): ?>
        <?php echo $this->partial('_video_duration.tpl', 'ynvideo', array('video' => $this->video))?>
    <?php endif; ?>
    <?php
    if ($this->video->photo_id) {
        echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.normal'));
    } else {
        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/video.png">';
    }
    ?>
    <span class="video_button_add_to_area">
        <button class="ynvideo_uix_button ynvideo_add_button" video-id="<?php echo $this->video->getIdentity()?>">
            <div class="ynvideo_plus" />
        </button>
    </span>
</div> 
<div class="video_options">
    <?php
        if (!isset($this->canRemove) || $this->canRemove == true) {
            echo $this->htmlLink(
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'action' => 'remove', 
                    'video_id' => $this->video->getIdentity()), null), 
                $this->translate('Remove'), array('class' => 'buttonlink ynvideo_playlist_delete smoothbox')
            );
        }
    ?>
</div>
<div class="video_info video_info_in_list">
    <div class="ynvideo_title">
        <?php echo $this->htmlLink($this->video->getHref(), $this->video->title) ?>
        <?php if (isset($this->video->watched) && $this->video->watched) : ?>
            <span class="ynvideo_watched"><?php echo $this->translate('Watched')?></span>
        <?php endif; ?>
    </div>
    
    <div class="video_stats">
        <?php echo $this->partial('_video_views_stat.tpl', 'ynvideo', array('video' => $this->video))?>
        <div class="ynvideo_block">
            <?php echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video))?>
        </div>
    </div>
    
    <div class="video_desc">
        <?php echo $this->string()->truncate($this->string()->stripTags($this->video->description), 300) ?>
    </div>
</div>