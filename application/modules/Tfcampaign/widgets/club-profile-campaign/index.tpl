<?php if($this->subject->isOwner($this->viewer())) :?>
	<?php $url = $this -> url(array(
	    'action' => 'create',
	    'club_id' => $this->subject->getIdentity(),
	    ),'tfcampaign_general', true)
	;?>
	<div class="group_album_options">
		<a class="smoothbox tf_button_action" href='<?php echo $url?>'><?php echo $this->translate('Add Campaign')?></a>
	</div>
<?php endif;?>
<?php if( count($this -> campaigns) > 0 ): ?>
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
		<?php if($this -> viewer() -> getIdentity() && Engine_Api::_()->user()->canTransfer($campaign)) :?>
			<?php $url = $this -> url(array(
			    'action' => 'transfer-item',
			    'subject' => $campaign -> getGuid(),
			    ),'user_general', true)
			;?>
			<div class="tf_btn_action">
				<a class="smoothbox tf_button_action" href='<?php echo $url?>'><i class="fa fa-exchange fa-lg"></i></a>
			</div>
		<?php endif;?>	
		<?php if($this -> viewer() -> getIdentity() && $campaign -> isOwner($this -> viewer())) :?>
			<?php if($campaign -> isEditable()) :?>
				<div class="tf_btn_action">
					<a class="smoothbox tf_button_action" href="<?php echo $this -> url(array('action' => 'edit', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_specific' , true);?>"><i class="fa fa-pencil-square-o fa-lg"></i></a>
				</div>
			<?php endif;?>
			<?php if($campaign -> isDeletable()) :?>
				<div class="tf_btn_action">
					<a class="smoothbox tf_button_action" href="<?php echo $this -> url(array('action' => 'delete', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_specific' , true);?>"><i class="fa fa-trash-o fa-lg"></i></a>
				</div>
				<?php endif;?>
		<?php endif;?>
	</li>
<?php endforeach;?>
</ul>
<?php else: ?>
  <div class="tip" style="margin: 10px">
    <span>
      <?php echo $this->translate('No campaigns have been added to this club yet.');?>
    </span>
  </div>
<?php endif; ?>