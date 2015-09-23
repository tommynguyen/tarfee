<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<ul class="generic_list_widget generic_list_widget_large_photo">
    <?php foreach ($this->paginator as $item): ?>
        <li>
            <?php
            echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                'video' => $item,
            ));
            ?>
        </li>
    <?php endforeach; ?>
</ul>