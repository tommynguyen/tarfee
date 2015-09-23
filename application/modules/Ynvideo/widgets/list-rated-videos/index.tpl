<ul class="generic_list_widget generic_list_widget_large_photo">
    <?php foreach ($this->paginator as $item): ?>
        <li>
            <?php
            echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                'video' => $item,
                'recentCol' => $this->recentCol
            ));
            ?>
        </li>
    <?php endforeach; ?>
</ul>