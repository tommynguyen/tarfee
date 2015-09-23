<?php if(count($this -> submitCampaignIds)) :?>
	<ul>
	<?php foreach($this -> submitCampaignIds as $campaign_id) :?>
			<?php $campaign = Engine_Api::_() -> getItem('tfcampaign_campaign', $campaign_id);?>
			<?php if($campaign && !Engine_Api::_()->user()->itemOfDeactiveUsers($campaign)) :?>
			<li>
				<div class="title"><?php echo $campaign;?></div>

				<div class="date">
					<?php 
						$startDateObj = null;
						if (!is_null($campaign->start_date) && !empty($campaign->start_date) && $campaign->start_date) 
						{
							$startDateObj = new Zend_Date(strtotime($campaign->start_date));	
						}
						if( $this->viewer() && $this->viewer()->getIdentity() ) {
							$tz = $this->viewer()->timezone;
							if (!is_null($startDateObj))
							{
								$startDateObj->setTimezone($tz);
							}
					    }
					?>
					<?php if(!empty($startDateObj)) :?>
						<?php echo (!is_null($startDateObj)) ?  date('M d Y ', $startDateObj -> getTimestamp()).$this -> translate('at').date(' g:ia', $startDateObj -> getTimestamp()) : ''; ?>
					<?php endif;?>
				</div>	

				<?php if($this -> viewer() -> getIdentity()) :?>
					<a class="smoothbox" href='<?php echo $this -> url(array('action' => 'list-withdraw', 'campaign_id' => $campaign->getIdentity()), 'tfcampaign_specific' , true)?>'><button><?php echo $this->translate('withdraw')?></button></a>
					<a class="smoothbox" href='<?php echo $this -> url(array('action' => 'list-edit', 'campaign_id' => $campaign->getIdentity()), 'tfcampaign_specific' , true)?>'><button><?php echo $this->translate('edit')?></button></a>
				<?php endif;?>
			</li>
			<?php endif;?>
	<?php endforeach;?>
	</ul>
<?php endif;?>
