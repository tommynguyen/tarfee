<?php if(count($this->events) > 0):?>
<ul class="generic_list_widget events_browse">
  <?php foreach( $this->events as $item ): 
  	$startDateObject = new Zend_Date(strtotime($item->starttime));
	if( $this->viewer() && $this->viewer()->getIdentity() ) {
		$tz = $this->viewer()->timezone;
		$startDateObject->setTimezone($tz);
	}?>
    <li>
        <div class="list-view">  
            <div title="<?php echo $this -> translate("Rates")?>" class="rating-right <?php echo (Engine_Api::_()->ynevent()->checkRated($item->getIdentity(), $this->viewer()->getIdentity())) ? "rated" : ""; ?>"><span class = "events_members"><?php echo number_format($item->rating, 1); ?></span><i class="ynicon-rating-w"></i></div>          
            <div class="photo">
                <div class="date">                    
                    <strong><?php 
                    $start_time = strtotime($item -> starttime);
					$oldTz = date_default_timezone_get();
					if($this->viewer() && $this->viewer()->getIdentity())
					{
						date_default_timezone_set($this -> viewer() -> timezone);
					}
					else 
					{
						date_default_timezone_set( $this->locale() -> getTimezone());
					}
                    echo date("d", $start_time); ?></strong>
                    <?php echo date("M", $start_time);
                    date_default_timezone_set($oldTz);?>
                </div>
                <?php echo $this->htmlLink($item->getHref(), '<span class="image-thumb" style="background-image: url('.$item->getPhotoUrl().');"></span>', array('class' => 'thumb')) ?>
            </div>
          <div class="info">
            <div class="title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
           
            <div class="events_members">
            <?php
                echo $this->translate('%1$s %2$s',
	            $this->locale()->toDate($startDateObject,  array('size' => 'long')),
	            $this->locale()->toTime($startDateObject)
	          	) ?>
                <br />
                <?php 
                if($item->host)
	            {
	            	if(strpos($item->host,'younetco_event_key_') !== FALSE)
					{
					  	$user_id = substr($item->host, 19, strlen($item->host));
						$user = Engine_Api::_() -> getItem('user', $user_id);
						
						echo $this->translate('host by %1$s',
	                  	$this->htmlLink($user->getHref(), $user->getTitle())) ;
					}
					else{
						echo $this->translate('host by %1$s', $item->host);
					}
				}
				else{
					echo $this->translate('by %1$s',
	                  	$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ;
				}
                ?>
                <br />
            </div>
            <div class="desc">
                <?php echo $this->string()->truncate(strip_tags($item->description), 100); ?>
              </div>
          </div>
        </div>
        <div class="grid-view">
            <div class="photo">
                <?php echo $this->htmlLink($item->getHref(), '<span class="image-thumb" style="background-image: url('.$item->getPhotoUrl().');"></span>', array('class' => 'thumb')) ?>
            </div>
            <div class="info">
                <div class="date">
                    <span class="day"><?php 
                    $start_time = strtotime($item -> starttime);
					$oldTz = date_default_timezone_get();
					if($this->viewer() && $this->viewer()->getIdentity())
					{
						date_default_timezone_set($this -> viewer() -> timezone);
					}
					else {
						date_default_timezone_set( $this->locale() -> getTimezone());
					}
                    echo date("d", $start_time); ?></span>
                    <span class="month"><?php echo date("M", $start_time); 
                    date_default_timezone_set($oldTz);?></span>
                </div>
                <div class="title">
                  <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                  <br />
                  <span class="events_members" style="font-weight: normal">
                  <?php 
                	if($item->host)
	            	{
		            	if(strpos($item->host,'younetco_event_key_') !== FALSE)
						{
						  	$user_id = substr($item->host, 19, strlen($item->host));
							$user = Engine_Api::_() -> getItem('user', $user_id);
							
							echo $this->translate('host by %1$s',
		                  	$this->htmlLink($user->getHref(), $user->getTitle())) ;
						}
						else{
							echo $this->translate('host by %1$s', $item->host);
						}
					}
					else{
						echo $this->translate('by %1$s',
		                  	$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ;
					}
                	?>
                 </span>
                </div>                
                <div class="stats">
                    <span class="person" title="<?php echo $this -> translate("Guests")?>"><?php echo $item->member_count; ?> <i class="ynicon-person"></i></span>
                    <span class="view" title="<?php echo $this -> translate("Views")?>"><?php echo $item->view_count; ?> <i class="ynicon-followed"></i></span>
                    <span class="like" title="<?php echo $this -> translate("Likes")?>"><?php echo $item->likes()->getLikeCount(); ?> <i class="ynicon-liked-m<?php if ($item->likes()->getLikeCount()==0) echo "gray";?>"></i></span>
                    <span class="rating" title="<?php echo $this -> translate("Rates")?>"><?php echo number_format($item->rating, 1);?> <i class="ynicon-rating-w<?php if ($item->rating==0) echo "gray";?>"></i></span>
                </div>                
              </div>
              <div class="desc">
                    <?php echo $this->string()->truncate(strip_tags($item->description), 100); ?> 
              </div>
        </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php else:?>
	<div class="tip">
        <span>
            <?php echo $this->translate("There were no events found matching your search criteria.") ?>
        </span>
    </div>
<?php endif;?>