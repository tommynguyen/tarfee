<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<ul class="generic_list_widget ynvideo_widget">
    <?php foreach ($this->videos as $item): ?>
        <li>
            <?php echo $this->partial('_video_widget.tpl', 'ynvideo', array('video' => $item)) ?>
        </li>
    <?php endforeach; ?>
</ul>

