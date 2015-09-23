<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
?>

<?php
	$api = Engine_Api::_()->sladvsubscription();
	$levels = $api->getLevels();
	$compares = $api->getCompares();
	$settings = $api->getSettings();
	$plans = $this->packages;
	$level_plans = array();	
	foreach ($plans as $plan)
	{
		if ($plan->package_id == $this->currentPackage->package_id)
			continue;
		$level_plans[$plan->level_id][] = $plan; 
	}
	
	$level_feature = 0;
	$feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('advsubscription.popular', '0');	
	if (key_exists($feature,$levels))
		$level_feature = $feature;
	else 
	{
		foreach ($levels as $id=>$level)
			if (!$level_feature)
				$level_feature = $id;
	}
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php if( $this->isAdmin ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Memberships are not required for ' .
          'administrators and moderators.') ?>
    </span>
  </div>
<?php return; endif; ?>

<div class="global_form payment_form_settings">
  <div>
    <div>
      <h3>
        <?php echo $this->translate('Membership') ?>
      </h3>
      <?php if( $this->currentPackage && $this->currentSubscription ): ?>
        <p class="form-description">
          <?php echo $this->translate('The plan you are currently subscribed ' .
              'to is: %1$s', '<strong>' .
              $this->translate($this->currentPackage->title) . '</strong>') ?>
          <br />
          <?php echo $this->translate('You are currently paying: %1$s',
              '<strong>' . $this->currentPackage->getPackageDescription()
              . '</strong>') ?>
        </p>
        <p style="padding-top: 15px; padding-bottom: 15px;">
          <?php echo $this->translate('If you would like to change your ' .
              'sembership, please select an option below.') ?>
        </p>
      <?php else: ?>
        <p class="form-description">
          <?php echo $this->translate('You have not yet selected a ' .
              'sembership plan. Please choose one now below.') ?>
        </p>
      <?php endif; ?>
	  <?php $payment_settings = Engine_Api::_()->getApi('settings', 'core')->payment;?>
	   <!-- discount -->
      <?php 
      if(isset($payment_settings['disableUpgrade']) && $payment_settings['disableUpgrade'] == 0):
     	 if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.referral_enable', 1)) :?>
      <div class="form-elements">
      	<div id="discount-wrapper" class="form-wrapper">
            <div id="discount-element" class="form-element">
              <div class="discount-container">
                <label class="package-label" >
                  <?php echo $this->translate("Discount Code") ?>
                </label>
                <p class="discount-description">
                  <input value="<?php echo (isset($_SESSION['ref_code']))? $_SESSION['ref_code']: " ";?>" type="text" name="discount-text" id="discount-text">
                  <i id="in_valid_code" title="<?php echo $this -> translate("Invalid Code");?>" style="float:right; color:red; display:none" class="fa fa-exclamation"></i>
                  <i id="valid_code"    title="<?php echo $this -> translate("Valid Code");?>"   style="float:right; color:green; display:none" class="fa fa-check"></i>
                </p>
              </div>
            </div>
          </div>
      </div>
      <?php endif;?>
       <?php endif;?>
      <!-- end discount -->
	  
      <div class="plan">
			<div class="plan-table">
				<table cellpadding="0" cellspacing="1px">
					<tr>
						<th class="title-width"></th>
						<?php $index = 0;?>
						<?php foreach ($levels as $id=>$level):?>
							<th class="detail-width" style="background-color: <?php if ($index % 2 == 0) echo $settings['odd_header_column_color']; else echo $settings['even_header_column_color'];$index++;?>;<?php echo $api->getStyle('header');?>">
								<?php echo $level?>
								<?php if ($id == $level_feature):?>
									<img class="popular_icon" src="<?php echo $this->baseUrl().'/'.$settings['most_popular_icon']?>" alt="<?php echo $this->translate('most popular');?>" />
								<?php endif;?>
							</th>
						<?php endforeach;?>
					</tr>
					<?php $index = 0;
					
					// Add Tips
					$tips = array(
					1 => $this -> translate('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ratione pariatur nihil, voluptatum magni voluptatem voluptate atque nobis tenetur omnis eos quisquam quis nulla animi quia sunt neque, accusamus rem officia.'),
					2 => $this -> translate('Tip for message to non follower'),
					// add more the same with above
					);
					?>
					<?php foreach ($compares as $compare):?>				
						<tr style="background-color: <?php if ($index % 2 == 0) echo $settings['odd_row_color']; else echo $settings['even_row_color'];$index++;?>;">
							<td style="<?php echo $api->getStyle('row');?>">
								<?php echo $compare['title']?>
								<?php if(!empty($tips[$index])):?>
								<div class="tf-settings-info">
									<?php echo $tips[$index];?>
								</div>
								<?php endif;?>
							</td>
							<?php foreach ($levels as $id=>$level):?>
								<td>
									<?php if ($compare['package'][$id]['radio'] == '0'):?>
										<span style="<?php echo $api->getStyle('cell');?>"><?php echo $compare['package'][$id]['text'];?></span>
									<?php elseif ($compare['package'][$id]['radio'] == '1'):?>
										<img src="<?php echo $this->baseUrl().'/'.$settings['ticker_image_link']?>" alt="<?php echo $this->translate('yes');?>"/>
									<?php else :?>
										<img src="<?php echo $this->baseUrl().'/'.$settings['x_image_link']?>" alt="<?php echo $this->translate('no');?>"/>
									<?php endif;?>
								</td>
							<?php endforeach;?>
						</tr>
					<?php endforeach;?>
					<tr>
						<td class="no-border">&nbsp;</td>
						<?php foreach ($levels as $id=>$level):?>						
							<td>
								<?php
								$action = 'confirm';
								if(isset($payment_settings['disableUpgrade']) && $payment_settings['disableUpgrade'] == 1)
								{
									$action = 'contact-us';
								}?>
								<form method="get" action="<?php echo $this->baseUrl();?>/payment/settings/<?php echo $action;?>">
									<input class="discount-input" type="hidden" value="<?php echo (isset($_SESSION['ref_code']))? $_SESSION['ref_code']: " ";?>" id="discount" name="discount">
									<?php if (isset($level_plans[$id])):?>
										<select name="package_id" onchange="changePackage(this);" style="<?php if (count($level_plans[$id])<2) echo "display:none;"; ?>">
											<?php foreach ($level_plans[$id] as $plan):?>
												<option value="<?php echo $plan->getIdentity();?>"><?php echo $plan->getTitle();?></option>
											<?php endforeach;?>
										</select>
										<p class="price_title">
											<?php $index = 0;?>
											<?php foreach ($level_plans[$id] as $plan):?>
												<span id="plan_<?php echo $plan->getIdentity();?>" class="plan" style="<?php if ($index != 0) echo 'display:none;';$index++; ?>"><?php echo $api->getTextPrice($plan);?></span>
											<?php endforeach;?>
										</p>
										<button name="submit" id="submit" type="submit" tabindex="4"><?php echo $this->translate('Upgrade Now!');?></button>
									<?php endif;?>
								</form>
							</td>
						<?php endforeach;?>
					</tr>
				</table>
			</div>
		</div>
		<script>
		function changePackage(element)
		{
			var parent = $(element).getParent();
			parent.getElements('.plan').each(function(e)
			{
				if (e.get('id') == 'plan_' + $(element).value)
					e.show();
				else
					e.hide();
			});
		}
		</script>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				if($('discount-text')) {
					$('discount-text').addEvent('change', function(){
						$('valid_code').setStyle("display", "none");
						$('in_valid_code').setStyle("display", "none");
						var code = this.get('value');
						var url = '<?php echo $this -> url(array('action' => 'check-code'), 'user_general', true) ?>';
						new Request.JSON({
							url: url,
							data: {
								'code': code,
							},
							onSuccess : function(responseJSON, responseText)
							{
								if(responseJSON.error == "0") {
									$('valid_code').setStyle("display", "block");
									$$('.discount-input').each(function(el) {
										el.set('value', code.trim())
									});
									
								} else {
									$('in_valid_code').setStyle("display", "block");
								}
							}
						}).send();
					});
				}
			});
		</script>
    </div>
  </div>
</div>
