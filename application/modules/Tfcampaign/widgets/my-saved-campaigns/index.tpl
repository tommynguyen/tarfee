<?php if(count($this -> saveRows)) :?>
	<ul>
	<?php foreach($this -> saveRows as $saveRow) :?>
		<?php $campaign = Engine_Api::_() -> getItem('tfcampaign_campaign', $saveRow -> campaign_id);?>
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
			
			<div class="submission">
				<i class="fa fa-user fa-lg"></i>
				<?php echo $this -> translate(array("%s submission", "%s submissions", $campaign -> getTotalSubmission()), $campaign -> getTotalSubmission());?>
			</div>
			<?php if($this -> viewer() -> getIdentity()) :?>
				<a class="smoothbox" href='<?php echo $this -> url(array('action' => 'remove-save', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_general' , true);?>'><button><?php echo $this->translate('remove')?></button></a>
			<?php endif;?>
		</li>
		<?php endif;?>
	<?php endforeach;?>
	</ul>
<?php endif;?>