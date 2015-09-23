<?php if(count($this -> ownCampaigns)) :?>
	<ul>
	<?php foreach($this -> ownCampaigns as $campaign) :?>
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

			<div class="submission">
				<i class="fa fa-user fa-lg"></i>
				<?php echo $this -> translate(array("%s submission", "%s submissions", $campaign -> getTotalSubmission()), $campaign -> getTotalSubmission());?>
			</div>
			
			<?php if($this -> viewer() -> getIdentity()) :?>
				<?php if($campaign -> isDeletable()) :?>
					<a class="smoothbox" href="<?php echo $this -> url(array('action' => 'delete', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_specific' , true);?>"><button><?php echo $this -> translate("remove");?></button></a>
				<?php endif;?>
				<?php if($campaign -> isEditable()) :?>
					<a href="<?php echo $this -> url(array('action' => 'edit', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_specific' , true);?>"><button><?php echo $this -> translate("edit");?></button></a>
				<?php endif;?>
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>
<?php endif;?>