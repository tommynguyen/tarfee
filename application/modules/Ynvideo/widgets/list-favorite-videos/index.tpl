<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<ul class="generic_list_widget ynvideo_widget <?php echo ($this->viewType == 'small')?'':'videos_browse ynvideo_frame ynvideo_list'?>">
    <?php foreach ($this->videos as $video): ?>
        <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
            <?php
                if ($this->viewType == 'small') {
                    echo $this->partial('_video_widget.tpl', 'ynvideo', 
                        array('video' => $video, 'infoCol' => 'favorite')); 
                } else {
                    echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                        'video' => $video,
                        'recentCol' => 'creation_date',
                        'infoCol' => 'favorite'
                    ));
                }
            ?>
        </li>
    <?php endforeach; ?>
</ul>