<div class = 'tarfee_total_items'><?php echo  $this->paginator -> getTotalItemCount()?></div>
<?php
 $title = $this->translate('View All');
?>
<ul id="profile_events_<?php echo $this->identity?>" class="ynevents_profile_tab">
    <?php foreach( $this->paginator as $event ): ?>
    <li>
		<?php if($event -> type_id == 1) :?>
            <span class="icon-event-tryout">
                <img src="application/modules/Ynevent/externals/images/tryout.png" alt="">
            </span>
            <?php else: ?>
            <span class="icon-event-tryout">
                <img src="application/modules/Ynevent/externals/images/event.png" alt="">
            </span>
        <?php endif;?>

        <div class="ynevents_title">
            <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
        </div>
        <?php if($this -> viewer() -> getIdentity() && Engine_Api::_()->user()->canTransfer($event)) :?>
		<div>
			<?php
				echo $this->htmlLink(array(
		            'route' => 'user_general',
		            'action' => 'transfer-item',
					'subject' => $event -> getGuid(),
		        ), '<i class="fa fa-exchange"></i>', array(
		            'class' => 'smoothbox', 'title' => $this -> translate('Transfer to club')
		        ));
			?>
		</div>
		<?php endif;?>
        <div class="ynevent_location">
    	<?php 
        	$countryName = '';
			$provinceName = '';
			$cityName = '';
			if($event ->country_id && $country = Engine_Api::_() -> getItem('user_location', $event ->country_id))
			{
				$countryName = $country -> getTitle();
			}
			if($event ->province_id && $province = Engine_Api::_() -> getItem('user_location', $event ->province_id))
			{
				$provinceName = $province -> getTitle();
			}
			if($event ->city_id && $city = Engine_Api::_() -> getItem('user_location', $event ->city_id))
			{
				$cityName = $city -> getTitle();
			}
			if($cityName || $provinceName || $countryName):?>
				<span><?php echo $this -> translate("Location");?>:</span>
				<p>
				<?php $city = '';
					if($cityName) 
						$city = $cityName; 
					else 
						$city = $provinceName;
					
					if($countryName && $city) 
					 	echo $city. ', '. $countryName;
					 else if($countryName)
					 	echo $countryName;
					 ?>
				</p>
			<?php elseif($event -> address):?>
				<span><?php echo $this -> translate("Location");?>:</span>
				<p>
					<?php echo $event -> address;?>
				</p>
			<?php endif;?>
        </div>
		<div class="ynevents_time_place">
            <span>
            	<?php 
				$startDateObj = null;
				if (!is_null($event->starttime) && !empty($event->starttime)) 
				{
					$startDateObj = new Zend_Date(strtotime($event->starttime));	
				}
				if( $this->viewer() && $this->viewer()->getIdentity() ) {
					$tz = $this->viewer()->timezone;
					if (!is_null($startDateObj))
					{
						$startDateObj->setTimezone($tz);
					}
			    }
				if(!empty($startDateObj)) :?>
					<span><?php echo $this -> translate('Date') ;?>:</span>
					<p><?php echo (!is_null($startDateObj)) ?  date('d M, Y', $startDateObj -> getTimestamp()) : ''; ?></p>
					<span><?php echo $this -> translate('Time') ;?>:</span>
					<p><?php echo (!is_null($startDateObj)) ?  date('H:i', $startDateObj -> getTimestamp()) : ''; ?></p>
				<?php endif; ?>
            </span>
        </div>
        <div class="ynevents_author">
	        <?php echo $this->translate('by') ?>
	        <?php
	        	$poster = $event->getOwner();
	            if ($poster) {
	                echo $this->htmlLink($poster, $poster->getTitle());
	            }
	        ?>
	    </div>
        <?php 
        if($this -> viewer() -> getIdentity()):?>
            <div class="ynevents_button" id = "ynevent_rsvp_<?php echo $event -> getIdentity()?>">
               <?php echo $this -> action('list-rsvp', 'widget', 'ynevent', array( 'id' => $event -> getIdentity()));?>
            </div>
        <?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>


<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array(), 'event_general'), $title, array('class' => 'icon_event_viewall')) ?>
<?php endif;?>
<?php if($this -> viewer() -> getIdentity()):?>
<script type="text/javascript">
 var tempChange = 0;
   var changeRsvp = function(id, option)
   {
   		if (tempChange == 0) 
   		{
   			tempChange = 1;
   			if ($('rsvp_option_' + id + '_' + option)) {
				$('rsvp_option_' + id + '_' + option).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			var url = en4.core.baseUrl + 'ynevent/widget/rsvp';
   			en4.core.request.send(new Request.JSON({
				url : url,
				data : {
					format : 'json',
					id : id,
					option_id: option
				},
				onComplete : function(e) {
					tempChange = 0;
				}
			}), {
				'element' : $('ynevent_rsvp_' + id)
			});
		}
   }
</script>
<?php endif;?>
