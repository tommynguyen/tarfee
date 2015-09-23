<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9575 2011-12-28 20:42:08Z john $
 * @author     Sami
 */
?>
<?php $session = new Zend_Session_Namespace('mobile'); ?>
<h3>
  <?php echo $this->translate('Event Details') ?>
</h3>
<div id='ynevent_stats'>
  <ul>
    <?php if( !empty($this->subject->description) ): ?>
    <li>
      <?php echo nl2br($this->subject->description) ?>
    </li>
    <?php endif ?>
    <li class="ynevent_date">
      <?php
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->subject->starttime));
        $endDateObject = new Zend_Date(strtotime($this->subject->endtime));
        if( $this->viewer() && $this->viewer()->getIdentity() ) {
			$tz = $this->viewer()->timezone;
			$startDateObject->setTimezone($tz);
			$endDateObject->setTimezone($tz);
        }
      ?>
      <?php if( $this->subject->starttime == $this->subject->endtime ): ?>
        <div class="label">
          <?php echo $this->translate('Date') ?>
        </div>
        <div class="ynevent_stats_content">
          <?php echo $this->locale()->toDate($startDateObject) ?>
        </div>

        <div class="label">
          <?php echo $this->translate('Time') ?>
        </div>
        <div class="ynevent_stats_content">
          <?php echo $this->locale()->toTime($startDateObject) ?>
        </div>

      <?php elseif( $startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd') ): ?>
        <div class="label">
          <?php echo $this->translate('Date')?>
        </div>
        <div class="ynevent_stats_content">
          <?php echo $this->locale()->toDate($startDateObject) ?>
        </div>

        <div class="label">
          <?php echo $this->translate('Time')?>
        </div>
        <div class="ynevent_stats_content">
          <?php echo $this->locale()->toTime($startDateObject) ?>
          -
          <?php echo $this->locale()->toTime($endDateObject) ?>
        </div>

      <?php else: ?>  
        <div class="ynevent_stats_content">
          <?php echo $this->translate('%1$s at %2$s',
            $this->locale()->toDate($startDateObject),
            $this->locale()->toTime($startDateObject)
          ) ?>
          - <br />
          <?php echo $this->translate('%1$s at %2$s',
            $this->locale()->toDate($endDateObject),
            $this->locale()->toTime($endDateObject)
          ) ?>
        </div>
      <?php endif ?>
    </li>
     <?php if( !empty($this->subject->capacity) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Capacity')?></div>
    	<div class="ynevent_stats_content"><?php echo $this->subject->capacity; ?></div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->price) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Fee')?></div>
    	<div class="ynevent_stats_content"><?php echo round(floatval($this->subject->price),2); ?> <?php echo $this->translate('$')?></div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->location) ): ?>
    <li>
      <div class="label"><?php echo $this->translate('Where')?></div>
      <div class="ynevent_stats_content">
      	<a id="ynevent_widget_profile_info_map_href" href="https://maps.google.com/?q=<?php echo $this->subject->location; ?>"><?php echo $this->subject->location; ?></a>
      </div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->host) ): ?>
      <?php if( $this->subject->host != $this->subject->getParent()->getTitle()): ?>
        <li>
          <div class="label"><?php echo $this->translate('Host') ?></div>
          <div class="ynevent_stats_content"><?php
          if(strpos($this->subject->host,'younetco_event_key_') !== FALSE)
		  {
		  	$user_id = substr($this->subject->host, 19, strlen($this->subject->host));
			$user = Engine_Api::_() -> getItem('user', $user_id);
			echo $user;
		  }
		  else{
		  	echo $this->subject->host;
		  }
		  
          ?></div>
        </li>
      <?php endif ?>
      <li>
        <div class="label"><?php echo $this->translate('Led by') ?></div>
        <div class="ynevent_stats_content"><?php echo $this->subject->getParent()->__toString() ?></div>
      </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->category_id) ): ?>
    <li>
      <div class="label"><?php echo $this->translate('Category')?></div>
      <div class="ynevent_stats_content">
        <?php echo $this->htmlLink(array(
          'route' => 'event_general',
          'action' => 'browse',
          'category_id' => $this->subject->category_id,
        ), $this->translate((string)$this->subject->categoryName())) ?>
      </div>
    </li>
    <?php endif ?>
  
  	<?php if( !empty($this->subject->email) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Email')?></div>
    	<div class="ynevent_stats_content" title="<?php echo $this->subject->email ?>">
    		<a href="mailto:<?php echo $this->subject->email ?>" target="_blank">
    			<?php echo ((strlen($this->subject->email) > 18) && (!$session -> mobile)) ? (substr($this->subject->email , 0, 18) . '...') : ($this->subject->email); ?>
    		</a>
    	</div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->url) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Url')?></div>
    	<div class="ynevent_stats_content"><a href="<?php echo $this->subject->url ?>" title="<?php echo $this->subject->url ?>">
    		<?php echo ((strlen($this->subject->url) > 16) && (!$session -> mobile)) ? (substr($this->subject->url , 0, 16) . '...') : ($this->subject->url); ?>
    	</a></div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->phone) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Phone')?></div>
    	<div class="ynevent_stats_content"><?php echo $this->subject->phone ?></div>
    </li>
    <?php endif ?>
    
    <?php if( !empty($this->subject->contact_info) ): ?>
    <li>
    	<div class="label"><?php echo $this->translate('Contact')?></div>
    	<div class="ynevent_stats_content"><?php echo $this->subject->contact_info ?></div>
    </li>
    <?php endif ?>
    <li class="ynevent_widget_cover_custom_fields">
		<?php if($this->fieldStructure):?>
	         <?php echo $this->fieldValueLoop($this->subject, $this->fieldStructure); ?>
	    <?php endif;?>
	</li>
    <li class="ynevent_stats_info">
      <div class="label"><?php echo $this->translate('RSVPs');?></div>
      <div class="ynevent_stats_content">
        <ul>
          <li>
            <?php echo $this->locale()->toNumber($this->subject->getAttendingCount()) ?>
            <span><?php echo $this->translate('attending');?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject->getMaybeCount()) ?>
            <span><?php echo $this->translate('maybe attending');?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject->getNotAttendingCount()) ?>
            <span><?php echo $this->translate('not attending');?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject->getAwaitingReplyCount()) ?>
            <span><?php echo $this->translate('awaiting reply');?></span>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</div>

<script>
window.addEvent('domready', function() {
	var el = $$(".tab_layout_ynevent_profile_map");
	var classList = '';
	if (el !=  null){
		classList = el.get('class').toString();
	}
	if (classList != ''){
		tab_id = classList.split(" ")[0].split("_")[1];
	}
	if (tab_id){
	}
	else{
			el.setStyle('display', 'none');	
	}
});
	
</script>