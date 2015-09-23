<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<h2><?php echo $this->translate('My Favorite Videos') ?></h2>
<?php
$totalVideo = $this->paginator->getTotalItemCount();
if ($totalVideo > 0):
    ?>
    <ul class="ynvideo_frame ynvideo_videos_manage videos_manage">
        <h3>
            <?php
            $totalVideo = $this->paginator->getTotalItemCount();
            echo $this->translate(array('%1$s video', '%1$s video', $totalVideo), $this->locale()->toNumber($totalVideo));
            ?>
        </h3>
        <?php foreach ($this->paginator as $item) : ?>
            <li>
                <?php
                echo $this->partial('_video.tpl', array('video' => $item))
                ?>
            </li>
        <?php endforeach; ?>
        <?php if ($this->paginator->getCurrentItemCount() < $totalVideo) : ?>
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->paginator); ?>
            </li>
        <?php endif; ?>   
    </ul>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any videos in your favorite list.'); ?>                
        </span>
    </div>
<?php endif; ?>    