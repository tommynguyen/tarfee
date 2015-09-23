<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<ul class="ynvideo_frame">
    <?php if (!isset($this->category)) : ?>
        <?php $totalCategory = $this->categoryPaginator->getTotalItemCount();?>
        <?php if ($totalCategory > 0) : ?>
            <?php foreach ($this->categoryPaginator as $category) : ?>
                <li class="ynvideo_videos_list">
                    <div class="ynvideo_category_title">
                        <a href="<?php echo $category->getHref() ?>">
                            <?php echo $this->translate($category->category_name) ?>
                        </a>
                    </div>
                    <ul class="videos_browse">
                        <?php $videos = $this->videosByCategory[$category->getIdentity()]; ?>
                        <?php foreach ($videos as $video) : ?>
                            <li>
                                <?php echo $this->partial('_video_listing.tpl', 'ynvideo', array('video' => $video)) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->categoryPaginator, null, null, array('query' => $this->params));?>
            </li>
        <?php else : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('There are no videos.'); ?>
                </span>
            </div>
        <?php endif; ?>
    <?php else: ?>        
        <li class="ynvideo_videos_list">
            <div class="ynvideo_category_title">
                <a href="<?php echo $this->category->getHref() ?>"><?php echo $this->category->category_name ?></a>
            </div>
            <?php $videoItemCount = $this->videoPaginator->getTotalItemCount();?>
            <?php if ($videoItemCount > 0 ) : ?>
                <h4>
                    <?php 
                        echo $this->translate(array('%1$s video', '%1$s videos', $this->videoPaginator->getTotalItemCount()), 
                            $this->videoPaginator->getTotalItemCount()) ;
                    ?>
                </h4>
                <ul class="videos_browse">
                    <?php foreach ($this->videoPaginator as $video) : ?>
                        <li>
                            <?php echo $this->partial('_video_listing.tpl', 'ynvideo', array('video' => $video))?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate('There are no videos.'); ?>
                    </span>
                </div>
            <?php endif; ?>
        </li>
        <?php if ($videoItemCount > 0) : ?>
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->videoPaginator, null, null, array('query' => $this->params));?>
            </li>
        <?php endif; ?>
    <?php endif; ?>
</ul>