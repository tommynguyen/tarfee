<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<?php 
    $totalPlaylists = $this->paginator->getTotalItemCount();
?>
<div class="ynvideo_playlist_headline">
    <h2><?php echo $this->translate('My Playlists') ?></h2>
    
    <?php if ($totalPlaylists > 0): ?>
        <div class="ynvideo_playlist_headline_control">
            <span>
                <a href="<?php echo $this->url(array('action' => 'create'), 'video_playlist')?>" class="buttonlink ynvideo_playlist_add">
                    <?php echo $this->translate('Create New Playlist')?>
                </a>
            </span>
            <span>
                <?php echo $this->translate('Sort by')?>
                <select name="ynvideo_sort_by" onchange="listPlaylist(this.value)">
                    <option 
                        value="<?php echo $this->url(array('order_by' => 'desc'), 'video_playlist')?>"
                        <?php if ($this->order_by == 'DESC') : ?>
                            selected="selected"
                        <?php endif; ?>
                    >
                        <?php echo $this->translate('Newest created')?>
                    </option>
                    <option 
                        value="<?php echo $this->url(array('order_by' => 'asc'), 'video_playlist')?>"
                        <?php if ($this->order_by == 'ASC') : ?>
                            selected="selected"
                        <?php endif; ?>
                    >
                        <?php echo $this->translate('Oldest created')?>
                    </option>
                </select>
            </span>
        </div>
    <?php endif; ?>
    <div class="ynvideo_clear"></div>
</div>

<?php if ($totalPlaylists > 0): ?>
    <ul class="ynvideo_frame ynvideo_videos_manage videos_manage">
        <h3>
            <?php 
                $totalPlaylist = $this->paginator->getTotalItemCount();
                echo $this->translate(array('%1$s playlist', '%1$s playlists', $totalPlaylist), $this->locale()->toNumber($totalPlaylist));
            ?>
        </h3>
        <?php foreach ($this->paginator as $playlist) : ?>
            <li>
                <div class="ynvideo_thumb_wrapper video_thumb_wrapper">
                    <?php
                    if ($playlist->photo_id) {
                        echo $this->htmlLink($playlist->getHref(), $this->itemPhoto($playlist, 'thumb.normal'));
                    } else {
                         echo '<a href="' . $playlist->getHref() . '">' 
                            . '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png" />'
                            . '</a>';
                    }
                    ?>
                </div>
                <div class="video_options">
                    <?php if ($playlist->isEditable()) : ?>
                        <?php
                            echo $this->htmlLink(
                                    $this->url(array('action' => 'edit', 'playlist_id' => $playlist->getIdentity()), 'video_playlist'), 
                                    $this->translate('Edit'), 
                                    array('class' => 'buttonlink ynvideo_playlist_edit')
                            );
                        ?>
                    <?php endif; ?>
                    <?php if ($playlist->isDeletable()) : ?>
                        <?php
                            echo $this->htmlLink(
                                    $this->url(array('action' => 'delete', 'playlist_id' => $playlist->getIdentity()), 'video_playlist'), 
                                    $this->translate('Delete'), 
                                    array('class' => 'smoothbox buttonlink ynvideo_playlist_delete')
                            );
                        ?>
                    <?php endif; ?>
                </div>

                <div class="video_info video_info_in_list">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($playlist->getHref(), $playlist->title) ?>
                    </div>

                    <div class="video_stats">
                        <span class="video_views">
                            <?php 
                                echo $this->translate(array('%1$s video', '%1$s videos', $playlist->video_count), $this->locale()->toNumber($playlist->video_count));
                            ?>
                            &nbsp;-&nbsp;
                            <?php echo $this->translate('Created on'); ?>
                            &nbsp;<?php echo $this->timestamp(strtotime($playlist->creation_date)) ?>                        
                        </span>                    
                    </div>

                    <div class="video_desc">
                        <?php echo $this->string()->truncate($playlist->description, 300) ?>
                    </div>
                </div>  
            </li>
        <?php endforeach; ?>
        <?php if ($this->paginator->getCurrentItemCount() < $totalPlaylists) : ?>
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->paginator); ?>
            </li>
        <?php endif; ?>
    </ul>
<?php else : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any playlists.'); ?>
            <?php if ($this->can_create): ?>
                <?php echo $this->translate('Get started by %1$sposting%2$s a new playlist.', '<a href="' . $this->url(array('action' => 'create'), 'video_playlist') . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>

<script language="javascript" type="text/javascript">
    function listPlaylist(value) {
        window.location = value;
    }
</script>   