<h2><?php echo $this->translate('Watch Later') ?></h2>
<?php
    $totalVideo = $this->paginator->getTotalItemCount();
?>
<?php if ($totalVideo > 0) : ?>
    <ul class="ynvideo_frame ynvideo_videos_manage videos_manage">
        <h3>
            <?php
            echo $this->translate(array('%1$s video', '%1$s videos', $totalVideo), $this->locale()->toNumber($totalVideo));
            ?>
        </h3>
        <?php foreach ($this->paginator as $video) : ?>    
            <li>
                <?php
                echo $this->partial('_video.tpl', array('video' => $video, 'watched' => $video->watched))
                ?>
            </li>
        <?php endforeach; ?>

        <?php if ($this->paginator->getCurrentItemCount() < $totalVideo) : ?>
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->params));?>
            </li>
        <?php endif; ?>   
    </ul>
<?php else: ?>
    <div class="tip">
        <span>
            <?php
                if (array_key_exists('search', $this->params)) {
                    echo $this->translate('There are no videos.');
                } else {
                    echo $this->translate('You do not have any videos in your watch-later list.');
                }
            ?>
        </span>
    </div>
<?php endif; ?>