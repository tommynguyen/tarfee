<ul class="tfcampaign_list">
<?php foreach($this -> campaigns as $campaign) :?>
	<li>
		<div class="tfcampaign_sport">
			<?php 
			if($campaign -> getSport())
				echo $this -> itemPhoto($campaign -> getSport(), 'thumb.icon');
			else
				echo $this -> itemPhoto($campaign, 'thumb.icon');?>
		</div>
		<div class="tfcampaign_title"><?php echo $campaign;?></div>
		<?php if($campaign -> getLocation()):?>
		<div class="tfcampaign_location">
			<span><?php echo $this -> translate("Location");?>:</span>
			<p><?php echo $campaign -> getLocation();?></p>
		</div>
		<?php endif;?>
		<div class="tfcampaign_gender">
			<span><?php echo $this -> translate("Gender") ;?>:</span>
			<p><?php echo $campaign -> getGender();?></p>
		</div>
		<div class="tfcampaign_closing">
			<?php 
				$endDateObj = null;
				if (!is_null($campaign->end_date) && !empty($campaign->end_date) && $campaign->end_date) 
				{
					$endDateObj = new Zend_Date(strtotime($campaign->end_date));	
				}
				if( $this->viewer() && $this->viewer()->getIdentity() ) {
					$tz = $this->viewer()->timezone;
					if (!is_null($endDateObj))
					{
						$endDateObj->setTimezone($tz);
					}
			    }
				if(!empty($endDateObj)) :?>
					<span><?php echo $this -> translate('Closing Date') ;?>:</span>
					<p><?php echo (!is_null($endDateObj)) ?  date('d M, Y', $endDateObj -> getTimestamp()) : ''; ?></p>
			<?php endif; ?>
		</div>
		<div class="tfcampaign_author">
	        <?php echo $this->translate('by') ?>
	        <?php
	        $poster = $campaign->getOwner();
	            if ($poster) {
	                echo $this->htmlLink($poster, $poster->getTitle());
	            }
	        ?>
	    </div>
	</li>
<?php endforeach;?>
</ul>