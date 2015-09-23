<?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
 	$this->headLink()
		 ->prependStylesheet($staticBaseUrl . 'application/modules/Tfcampaign/externals/styles/slider/styles.css')
		 ->prependStylesheet("//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css")
		 ;
		
	$this->headScript()
  		 ->appendFile($staticBaseUrl . 'application/modules/Tfcampaign/externals/scripts/jquery.min.js')	
		 ->appendScript('jQuery.noConflict();')
  		 ->appendFile($staticBaseUrl . 'application/modules/Tfcampaign/externals/scripts/jquery-ui.min.js')	
		;	
?>

<div class="tf_campaign_browse">
	<div class="tfcampaign_box_sort">
		<select name="direction" id="tfcampaign-campaign-direction">				
				<option value="DESC">DESC</option>
				<option value="ASC">ASC</option>
		</select>
		<select name="sort" id="tfcampaign-campaign-sort">
				<option value="campaign.creation_date"><?php echo $this -> translate('Posting Date');?></option>
				<option value="campaign.start_date"><?php echo $this -> translate('Starting Date');?></option>
				<option value="campaign.end_date"><?php echo $this -> translate('Closing Date');?></option>
				<!--<option value="campaign.view_count"><?php echo $this -> translate('Sort by view count');?></option>-->
		</select>
	</div>
	<?php if( count($this->paginator) > 0 ): ?>
		<ul class="tfcampaign_list_browse">
	    <?php foreach( $this->paginator as $campaign): ?>
	    	<li>
	    		<div class="tfcampaign_title">
	    			<?php 
	    			if($campaign -> getSport())
	    				echo $this -> itemPhoto($campaign -> getSport(), 'thumb.icon');
	    			else
	    				echo $this -> itemPhoto($campaign);?>
					<?php echo $campaign;?>

					<div class="tfcampaign_author">
			            <?php echo $this->translate('by') ?>

			            <?php
			            $poster = $campaign->getOwner();
				            if ($poster) {
				                echo $this->htmlLink($poster, $poster->getTitle());
				            }
			            ?>
			        </div>
	    		</div>
				
				<div class="tfcampaign_desc">
					<?php echo $this->viewMore($campaign -> getDescription());?>
				</div>
				
				<div class="tfcampaign_infomation">
					<div class="tfcampaign_infomation_item">

						<ul class="block-first">
							<li>
								<?php $position = $campaign -> getPosition();?>
								<?php if($position) :?>
									<span><?php echo $this -> translate("Position") ;?></span>
									<p><?php echo $position -> getTitle();?></p>
								<?php endif;?>
							</li>
							<li>
								<?php if($campaign -> getLocation()) :?>
									<span><?php echo $this -> translate("Location");?></span>
									<p><?php echo $campaign -> getLocation();?></p>
								<?php endif;?>
							</li>
						</ul>
						<ul class="block-second">
							<li>
								<span><?php echo $this -> translate("Gender") ;?></span>
								<p><?php echo $campaign -> getGender();?></p>
							</li>
							<li>
								<span><?php echo $this -> translate("Age (Years)") ;?></span>
								<p><?php echo $this -> translate("%s - %s", date("Y") - $campaign -> from_age, date("Y") - $campaign -> to_age);?></p>
							</li>
							<?php 
								$endDateObj = null;
								$startDateObj = null;
								if (!is_null($campaign->start_date) && !empty($campaign->start_date) && $campaign->start_date) 
								{
									$startDateObj = new Zend_Date(strtotime($campaign->start_date));	
								}
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
									if (!is_null($startDateObj))
									{
										$startDateObj->setTimezone($tz);
									}
							    }
								?>
								<li>
								<?php if(!empty($startDateObj)) :?>
									<span><?php echo $this -> translate('Start Date') ;?></span>
									<p><?php echo (!is_null($startDateObj)) ?  date('d M, Y', $startDateObj -> getTimestamp()) : ''; ?></p>
								<?php endif;?>
								</li>

								<li>
								<?php if(!empty($endDateObj)) :?>
									<span><?php echo $this -> translate('Closing Date') ;?></span>
									<p><?php echo (!is_null($endDateObj)) ?  date('d M, Y', $endDateObj -> getTimestamp()) : ''; ?></p>
								<?php endif;?>
								</li>
						</ul>
					</div>
				</div>
					<?php if($this -> viewer() -> getIdentity()) :?>
						<div class="tfcampaign_boxbutton">
						<?php 
							$submissionIds = $campaign -> getSubmissionByUser($this -> viewer(), $campaign);
							$startDate = date_create($campaign->start_date);
							$endDate = date_create($campaign->end_date);
				            $nowDate = date_create('now');
				            if ($nowDate <= $endDate) :
						?>
							<?php 
							$userPlayers = Engine_Api::_() -> getItemTable('user_playercard') -> getAllPlayerCard($this -> viewer() -> getIdentity());
							$totalPlayerMatch = 0;
							$submissionPlayers = $campaign -> getSubmissionPlayers();
							$arrSubmission = array();
							foreach($submissionPlayers as $submissionPlayer) {
								$arrSubmission[] = $submissionPlayer -> player_id;
							}
							foreach ($userPlayers as $player) 
							{
								if(!in_array($player -> getIdentity(), $arrSubmission)) 
								{
									if($player -> countPercentMatching($campaign) >= $campaign -> percentage){
										$totalPlayerMatch++;
									}
								}
							}
							if($totalPlayerMatch > 0):
								echo $this->htmlLink(
								    array('route' => 'tfcampaign_specific','action' => 'submit', 'campaign_id' => $campaign->getIdentity()), 
								    "<button>".$this->translate('apply')."</button>", 
								array('class' => 'smoothbox'));
							endif; ?>
						<?php endif;?>
						<?php if(count($submissionIds)) :?>
							<a class="smoothbox" href='<?php echo $this -> url(array('action' => 'list-withdraw', 'campaign_id' => $campaign->getIdentity()), 'tfcampaign_specific' , true)?>'><button class="withdraw"><?php echo $this->translate('withdraw')?> &nbsp;&nbsp;&nbsp;<i class="fa fa-times"></i></button></a>
						<?php endif;?>	
							
						<a href="javascript:void(0)">
							<button data-id="<?php echo $campaign -> getIdentity();?>" onclick="saveCampaign(this);" class="<?php echo ($campaign -> isSaved())? 'campaign-save-active' : ''  ?>">
							<?php echo ($campaign -> isSaved())? $this -> translate('saved') : $this -> translate('save for later'); ?>
							</button>
						</a>
					</div>
					<?php endif;?>
			</li>
	    <?php endforeach; ?>
		</ul>
	<?php if( count($this->paginator) > 1 ): ?>
	        <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	        )); ?>
	    <?php endif; ?>
	<?php else: ?>
	    <div class="tip">
	        <span><?php echo $this->translate('No campaigns found.') ?></span>
	    </div>
	<?php endif; ?>
</div>
<script type="text/javascript">
	function saveCampaign(ele){
		var id = ele.get('data-id');
		if(ele.hasClass('campaign-save-active')) {
			ele.removeClass('campaign-save-active');
			ele.innerHTML = "<?php echo $this -> translate('save for later');?>";
		} else {
			ele.addClass('campaign-save-active');
			ele.innerHTML = "<?php echo $this -> translate('saved');?>";
		}
		var url = '<?php echo $this -> url(array('action' => 'save'), 'tfcampaign_general', true) ?>';
		new Request.JSON({
	        url: url,
	        data: {
	            'campaign_id': id,
	        },
	    }).send();
	}
	
	window.addEvent('domready', function(){
		$('tfcampaign-campaign-sort').addEvent('change', function (){
			$('campaign-sort').set('value', this.get('value'));
			$('fiter-campaign').submit();
		});
		
		<?php if(!empty($this -> isSort)):?>
			$('tfcampaign-campaign-sort').set('value', '<?php echo $this -> isSort;?>');
			$('campaign-sort').set('value', '<?php echo $this -> isSort;?>');
		<?php endif;?>
		
		$('tfcampaign-campaign-direction').addEvent('change', function (){
			$('campaign-direction').set('value', this.get('value'));
			$('fiter-campaign').submit();
		});
		
		<?php if(!empty($this -> direction)):?>
			$('tfcampaign-campaign-direction').set('value', '<?php echo $this -> direction;?>');
			$('campaign-direction').set('value', '<?php echo $this -> direction;?>');
		<?php endif;?>
		
	});
	
</script>