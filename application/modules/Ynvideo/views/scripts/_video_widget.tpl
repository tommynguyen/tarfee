<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div class="photo video_thumb_wrapper">
    <?php $href = $this->video->getHref();
	$request = Zend_Controller_Front::getInstance()->getRequest();
	if($request -> getParam('format', '') == 'smoothbox')
	{
		$href = $this->video->getPopupHref().'/format/smoothbox';
	}
    echo $this->htmlLink($href, $this->itemPhoto($this->video, 'thumb.large'), array('class' => 'thumb')) ?>
    <span class="video_button_add_to_area">
        <button class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $this->video->getIdentity()?>" video-id="<?php echo $this->video->getIdentity()?>">
            <div class="ynvideo_plus" />
        </button>
    </span>
</div>
<div class="info">
    <div class="ynvideo_title">
        <?php
            echo $this->htmlLink($href, htmlspecialchars ($this->string()->truncate($this->video->getTitle(), 25)), array('title' => $this->video->getTitle()));
        ?>
    </div>
    <div class="ynvideo_date">
        <?php echo $this->translate('Created on %1$s', $this->timestamp($this->video->creation_date)) ?>
    </div>
    <div class="owner">
        <?php
        $owner = $this->video->getOwner();
        echo $this->translate('By %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle(), array('target' => '_parent')));
        ?>
    </div>
    <div class="ynvideo_view_count">
        <?php if (!isset($this->infoCol)) : ?>
            <?php echo $this->translate(array('%1$s view', '%1$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
        <?php else : ?>
            <?php if ($this->infoCol == 'like') : ?>
                <?php
                    $likeCount = $this->video->likes()->getLikeCount();
                    echo $this->translate(array('%1$s like', '%1$s likes', $likeCount), $this->locale()->toNumber($likeCount));
                ?>
            <?php elseif ($this->infoCol == 'comment') : ?>
                <?php
                    $commentCount = $this->video->comments()->getCommentCount();
                    echo $this->translate(array('%1$s comment', '%1$s comments', $commentCount), $this->locale()->toNumber($commentCount));
                ?>
            <?php elseif ($this->infoCol == 'favorite') : ?>
                <?php
                    echo $this->translate(array('%1$s favorite', '%1$s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count));
                ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video)) ?>
</div>