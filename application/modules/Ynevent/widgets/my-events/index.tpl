<ul id="profile_events_<?php echo $this->identity?>" class="ynevents_profile_tab">
    <?php foreach( $this->paginator as $event ): ?>
    <li>
        <div class="ynevents_info">
            <div class="ynevents_title">
                <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
            </div>
        </div>
        <div class="ynevents_time_place_rating">
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
						<?php echo (!is_null($startDateObj)) ?  date('d M, Y H:i', $startDateObj -> getTimestamp()) : ''; ?>
					<?php endif; ?>
                </span>
            </div>
        </div>
        <?php if($event -> isOwner($this->viewer())) :?>
			<div class="tf_btn_action">
			<?php
				echo $this->htmlLink(array(
					'route' => 'event_specific',
					'action' => 'edit',
					'event_id' => $event->event_id,
			    ), '<i class="fa fa-pencil-square-o fa-lg"></i>', array('class' => 'tf_button_action'));
			?>
		    </div>
		    <div class="tf_btn_action">
			<?php
				echo $this->htmlLink(array(
			 	        'route' => 'event_specific', 
			         	'action' => 'delete', 
			         	'event_id' => $event->event_id, 
			         	'format' => 'smoothbox'), 
			         	'<i class="fa fa-trash-o fa-lg"></i>', array('class' => 'tf_button_action smoothbox'
			     ));
			?>
			</div>
		<?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>

<?php if($this->paginator->getTotalItemCount() > $this->itemCountPerPage):?>
  <?php echo $this->htmlLink($this->url(array('action' => 'manage'), 'event_general'), $this -> translate("View all"), array('class' => 'icon_event_viewall')) ?>
<?php endif;?>