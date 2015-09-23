<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @access	   John
 */
?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
    var anchor = $('ynevent_profile_photos').getParent();
    $('ynevent_profile_photos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynevent_profile_photos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynevent_profile_photos_previous').removeEvents('click').addEvent('click', function(){
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

    $('ynevent_profile_photos_next').removeEvents('click').addEvent('click', function(){
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
  });
</script>
<?php if( $this->paginator->getTotalItemCount() > 0 || $this->canUpload ): ?>
  <div class="ynevent_album_options">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'event_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
          'tab' => $this->identity,
        ), $this->translate('View All Photos'), array(
          'class' => 'buttonlink icon_event_photo_view'
      )) ?>
    <?php endif; ?>
   
    	<?php if( $this->canUpload ): ?>
	      <?php echo $this->htmlLink(array(
	          'route' => 'event_extended',
	          'controller' => 'photo',
	          'action' => 'upload',
	          'subject' => $this->subject()->getGuid(),
	          'tab' => $this->identity,
	        ), $this->translate('Upload Photos'), array(
	          'class' => 'buttonlink icon_event_photo_new'
	      )) ?>
    	<?php endif; ?>
    	
  </div>
  <br />
<?php endif; ?>



<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="thumbs" id="ynevent_profile_photos">
    <?php 
    $thumb_photo = 'thumb.normal';
		if(defined('YNRESPONSIVE'))
		{
			$thumb_photo = 'thumb.profile';
		}
    foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </a>
        <p class="thumbs_info">
          <?php echo $this->translate('By');?>
          <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
          <br />
          <?php echo $this->timestamp($photo->creation_date) ?>
        </p>
      </li>
    <?php endforeach;?>
  </ul>
<div>
  <div id="ynevent_profile_photos_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="ynevent_profile_photos_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this event yet.');?>
    </span>
  </div>

<?php endif; ?>