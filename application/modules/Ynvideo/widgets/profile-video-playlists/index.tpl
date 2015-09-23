<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynvideo_profile_playlists').getParent();
            $('ynvideo_profile_playlists_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynvideo_profile_playlists_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynvideo_profile_playlists_previous').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });

            $('ynvideo_profile_playlists_next').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });
        <?php endif; ?>
    });
</script>

<ul id="ynvideo_profile_playlists" class="videos_browse">
    <?php foreach ($this->paginator as $playlist): ?>
        <li>
            <div class="ynvideo_thumb_wrapper video_thumb_wrapper">
                <?php
                if ($playlist->photo_id) {
                    echo $this->htmlLink($playlist->getHref(), $this->itemPhoto($playlist, 'thumb.normal'));
                } else {
                    echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">';
                }
                ?>
            </div>
            
            <div class="video_info video_info_in_list">
                <div class="ynvideo_title">
                    <?php echo $this->htmlLink($playlist->getHref(), $this->string()->truncate($playlist->title, 35), array('title' => $playlist->title)) ?>
                </div>
                
                <div class="video_stats">
                    <span class="video_views">
                        <?php 
                            echo $this->translate(array('%s video', '%s videos', $playlist->video_count), $this->locale()->toNumber($playlist->video_count));
                        ?>
                        &nbsp;-&nbsp;
                        <?php echo $this->translate('Created on'); ?>
                        &nbsp;<?php echo $this->timestamp(strtotime($playlist->creation_date)) ?>                        
                    </span>                    
                </div>
            </div>
        </li>
<?php endforeach; ?>
</ul>

<div>
    <div id="ynvideo_profile_playlists_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynvideo_profile_playlists_next" class="paginator_next">
<?php
echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    'onclick' => '',
    'class' => 'buttonlink_right icon_next'
));
?>
    </div>
</div>