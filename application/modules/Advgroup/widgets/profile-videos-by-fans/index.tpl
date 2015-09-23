<ul class="tfvideo-by-fans" id="group-videos-by-fans">
    <?php foreach ($this->paginator as $item): ?>
    <li>
        <?php
        echo $this->partial('_video_listing.tpl', 'advgroup', array(
            'video' => $item,
        ));
        ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php if($this->paginator->getTotalItemCount() > $this->itemCountPerPage):?>
  <?php echo $this->htmlLink($this -> url(array(), 'default', true).'search?type%5B%5D=video&video_type=fan&parent_type=group&parent_id='.$this->subject()->getIdentity(), $this -> translate('View all'), array('class' => 'icon_event_viewall')) ?>
<?php endif;?>